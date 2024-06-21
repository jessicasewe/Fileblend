<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
            'conversion_type' => 'required|string',
        ]);

        $file = $request->file('file');
        $conversionType = $request->conversion_type;
        $path = $file->store('uploads');

        return redirect()->route('convert', ['path' => $path, 'type' => $conversionType]);
    }
}
