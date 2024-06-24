<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\Response;
use GuzzleHttp\Client;
use Smalot\PdfParser\Parser;

class FileController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,docx|max:10240',
            'conversion_type' => 'required|string'
        ]);

        $file = $request->file('file');
        $conversionType = $request->input('conversion_type');

        $originalFilename = $file->getClientOriginalName();

        $path = $file->storeAs('uploads', $originalFilename);

        Log::info('Uploaded file', ['path' => $path, 'type' => $conversionType]);

        return redirect()->route('convert', ['path' => $path, 'type' => $conversionType]);
    }

    public function convert(Request $request)
    {
        $path = $request->query('path');
        $type = $request->query('type');

        Log::info('Converting file', ['path' => $path, 'type' => $type]);

        if (!$path) {
            return back()->with('error', 'Path is missing.');
        }
        if (!$type) {
            return back()->with('error', 'Conversion type is missing.');
        }

        $filePath = storage_path('app/' . $path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        $originalFilename = pathinfo($filePath, PATHINFO_FILENAME);
        $originalExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        $convertedExtension = '';
        switch ($type) {
            case 'pdf_to_word':
                $convertedExtension = 'docx';
                break;
            // case 'ppt_to_pdf':
            //     $convertedExtension = 'csv';
            //     break;
            case 'word_to_pdf':
                $convertedExtension = 'pdf';
                break;
            case 'word_to_csv':
                $convertedExtension = 'csv';
                break;
            default:
                return back()->with('error', 'Invalid conversion type.');
        }

        $convertedFileName = $originalFilename . '.' . $convertedExtension;
        $convertedPath = storage_path('app/converted/') . $convertedFileName;


        switch ($type) {
            case 'pdf_to_word':
                $this->convertPdfToWord($filePath, $convertedPath);
                break;
            // case 'ppt_to_pdf':
            //     $this->convertPdfToCsv($filePath, $convertedPath);
            //     break;
            case 'word_to_pdf':
                $this->convertWordToPdf($filePath, $convertedPath);
                break;
            case 'word_to_csv':
                $this->convertWordToCsv($filePath, $convertedPath);
                break;
            default:
                return back()->with('error', 'Invalid conversion type.');
        }

        if (file_exists($convertedPath) && filesize($convertedPath) > 0) {
            $downloadLink = route('download', ['path' => $convertedFileName]);
            return back()->with([
                'downloadLink' => $downloadLink,
                'convertedFileName' => $convertedFileName
            ]);
        } else {
            return back()->with('error', 'Conversion failed or resulted in an empty file.');
        }
    }

    public function convertPdfToWordWithCloudmersive($filePath)
    {
        $client = new \GuzzleHttp\Client();
        $url = env('CLOUDMERSIVE_API_URL', 'https://api.cloudmersive.com/convert/pdf/to/docx');
        $headers = [
            'Apikey' => 'cb9cec2e-96e5-407e-929b-95038d4de645',
            'Content-Type' => 'application/pdf'
        ];
        $body = fopen($filePath, 'r');

        $response = $client->request('POST', $url, [
            'headers' => $headers,
            'body' => $body
        ]);

        if ($response->getStatusCode() == 200) {
            $convertedFilePath = storage_path('app/converted/') . pathinfo($filePath, PATHINFO_FILENAME) . '.docx';
            file_put_contents($convertedFilePath, $response->getBody());

            Log::info('Cloudmersive API call successful', ['time' => now()]);

            return $convertedFilePath;
        } else {
            Log::error('Cloudmersive API call failed', ['time' => now(), 'response' => $response->getBody()]);
            return null;
        }
    }

    private function convertPdfToWord($sourcePath, $targetPath)
    {
        $result = $this->convertPdfToWordWithCloudmersive($sourcePath, $targetPath);

        if (!$result) {
            $parser = new Parser();
            $pdf = $parser->parseFile($sourcePath);
            $text = $pdf->getText();

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $section->addText($text);
            $phpWord->save($targetPath, 'Word2007');
        }
    }

    // private function convertPptToPdf($sourcePath, $targetPath)
    // {
       
    // }

    private function convertWordToPdf($sourcePath, $targetPath)
    {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($sourcePath);

        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        $htmlWriter->save(storage_path('app/html'));

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(file_get_contents(storage_path('app/html')));

        $dompdf->render();

        file_put_contents($targetPath, $dompdf->output());

        if (file_exists(storage_path('app/html'))) {
            unlink(storage_path('app/html'));
        }
    }

    private function convertWordToCsv($sourcePath, $targetPath)
    {
        
    }

    public function download($path)
    {
        $filePath = storage_path('app/converted/' . $path);

        if (file_exists($filePath)) {
            return Response::download($filePath)->deleteFileAfterSend(true);
        } else {
            abort(404, 'File not found.');
        }
    }
}

