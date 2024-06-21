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
            <h2>FileBlend - Convert and Merge Files</h2>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" accept=".pdf,.docx" required>
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
                            <option value="pdf_to_csv">PDF to CSV</option>
                            <option value="word_to_pdf">Word to PDF</option>
                            <option value="word_to_csv">Word to CSV</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
