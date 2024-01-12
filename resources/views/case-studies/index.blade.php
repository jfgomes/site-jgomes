<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case study list</title>
    <link rel="shortcut icon" href="favicon.png" >
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 20px;
        }

        h1 {
            color: #aaaaaa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 28px;
            font-weight: bold;
        }

        ul {
            padding: 0;
        }

        li {
            margin-top: 10px;
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
            list-style: none;
        }

        a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: color 0.3s ease-in-out;
        }

        a:hover {
            color: #007bff;
        }

        p {
            color: #dc3545;
            font-weight: bold;
            padding-left: 20px;
        }
    </style>
</head>
<body>
<h1>Case study list:</h1>
@if(count($foldersWithFiles) > 0)
    <ul>
        @foreach($foldersWithFiles as $folder)
            <li>
                {{ $folder['name'] }}
                @if(count($folder['files']) > 0)
                    <ul>
                        @foreach($folder['files'] as $file)
                            <li>
                                <a href="example/{{ base64_encode(Str::after($file, 'public/')) }}">
                                    {{ basename($file) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>Any file found.</p>
                @endif
            </li>
            ---
        @endforeach
    </ul>
@else
    <p>No dir found.</p>
@endif


</body>
</html>
