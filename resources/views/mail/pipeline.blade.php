<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $result }}</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f4f4f4;
                color: #333;
                margin: 0;
                padding: 20px;
            }

            h1 {
                color: #007bff;
            }

            p {
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 10px;
            }

            .result {
                color: black;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1>Jenkins notification</h1>
        <p><span class="result">{{ $result }}</span></p>
        <p><strong>{{ $msg }}</strong></p>
    </body>
</html>
