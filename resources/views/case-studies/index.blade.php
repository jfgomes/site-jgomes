<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case study list</title>
    <link rel="shortcut icon" href="favicon.png" >
    <link rel="stylesheet" href="../css/case-studies.css">
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
                                <a href="case-studies/file/{{ base64_encode(Str::after($file, 'public/')) }}">
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
