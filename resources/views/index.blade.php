<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileBlend</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://png.pngtree.com/thumb_back/fh260/background/20210510/pngtree-school-assignments-education-colorful-cool-image_700456.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .centered-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            max-width: 600px;
            width: 100%;
            margin-bottom: 20px;
        }
        .card-body {
            padding: 20px;
        }
        .custom-file-input {
            width: 0;
            height: 0;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }
        .custom-file-label {
            cursor: pointer;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .custom-select {
            max-width: 600px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container centered-container">
        <div class="text-center mb-4">
            <h1 class="display-4">Welcome to FileBlend</h1>
            <p class="lead">Convert your PDF and Word documents with ease</p>
        </div>
        <div class="card">
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" accept=".pdf,.docx" required onchange="updateFileName(this)">
                                <label class="custom-file-label" for="file">Choose file</label>
                            </div>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="submit">Convert</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="conversion_type">Select conversion type</label>
                        <select name="conversion_type" class="form-control custom-select" required>
                            <option value="pdf_to_word">PDF to Word</option>
                            <option value="pdf_to_pptx">PDF to PPT</option>
                            <option value="word_to_pdf">Word to PDF</option>
                            <option value="pptx_to_pdf">PPTX to PDF</option>
                        </select>
                    </div>
                </form>
                
                <!-- Display converted file and download link -->
                @if(session('downloadLink') && session('convertedFileName'))
                    <div class="mt-3">
                        <p>Converted File: {{ session('convertedFileName') }}</p>
                        <a href="{{ session('downloadLink') }}" class="btn btn-success" role="button" download>Download Converted File</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <script>
        function updateFileName(input) {
            var fileName = input.files[0].name;
            input.nextElementSibling.innerText = fileName;
        }
    </script>
</body>
</html>
