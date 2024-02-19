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
<h1>Project case studies:</h1>
@if(count($foldersWithFiles) > 0)
    <ul>
        @foreach($foldersWithFiles as $folder)
            <li>
                @if(str_contains($folder['name'], '##DONE##'))
                    {{ str_replace('##DONE##', '✔️', $folder['name']) }}
                @elseif(str_contains($folder['name'], '##STARTED_NOT_DONE##'))
                    {!! $folder['name'] . '<strong> ( 🚧 Work in progress 🚧) </strong>' !!}
                @elseif(str_contains($folder['name'], '##NOT_STARTED##'))
                    {!! str_replace('##NOT_STARTED##', '<strong> ( ❌ Dev not started ❌) </strong>', $folder['name']) !!}
                @endif
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
                <br>
            </li>
        @endforeach
    </ul>
@else
    <p>No dir found.</p>
@endif


</body>
</html>
