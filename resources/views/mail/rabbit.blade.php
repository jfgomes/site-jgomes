<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rabbit notification</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f4f4f4;
                color: #333;
                padding: 20px;
                margin: 0;
            }

            h1 {
                color: #2B2B2B;
                font-size: 18px;
                margin-bottom: 10px;
            }

            p {
                margin-bottom: 15px;
            }

            strong {
                color: #2B2B2B;
            }

            .details {
                margin-left: 20px;
            }

            .details p {
                padding-left: 20px;
            }
        </style>
    </head>
    <body>
        <h1>Rabbit notification</h1>
        <p>You have received a new rabbit notification with the following details:</p>

        <div class="details">
            <strong>Queue data:</strong>
            <p>{{ $email_data }}</p>

            @if ($email_error)
                <strong>Error:</strong>
                <p>{{ $email_error }}</p>
            @endif

        </div>
        <p>Thank you!</p>
    </body>
</html>
