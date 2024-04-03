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
<h1>Project:</h1>
<p style="color: grey; font-size:15px">
    My CV portal is based on all the next listed features, and therefore all topics are implemented, functional and tested.
    <br />
    <br />
    All the next features are the base of this project and it has an executable setup that automatically creates a production environment, all with microservices, <br />
    with a data backup systems, cache systems, versioning, minification of JS and CSS, authentication system, api, serverless,
    asynchronous messaging system, a CI/CD system, ssl certificated, etc... <br /> without the need code more and make it up and running easy... all with just a single command.
    <br />
    <br />
    Also the local / development env has a executable setup that mounts a production similar env, all done automatically and allow the developers ready to dev in seconds.
    <br />
    This local env works with GIT and Jenkins, and this 2 techs act like a bridge to prod.
    <br />
    <br />
    Checkout the following features list to get more details.
</p>
<h1>Features list:</h1>
@if(count($foldersWithFiles) > 0)
    <ul>
        @foreach($foldersWithFiles as $folder)
            <li>
                @if(str_contains($folder['name'], '##DONE##'))
                    {{ str_replace('##DONE##', '✔️', $folder['name']) }}
                @elseif(str_contains($folder['name'], '##STARTED_NOT_DONE##'))
                    {!! str_replace('##STARTED_NOT_DONE##', '<strong> ( 🚧 Work in progress 🚧 ) </strong>', $folder['name']) !!}
                @elseif(str_contains($folder['name'], '##NOT_STARTED##'))
                    {!! str_replace('##NOT_STARTED##', '<strong> ( ❌ Dev not started ❌ ) </strong>', $folder['name']) !!}
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
