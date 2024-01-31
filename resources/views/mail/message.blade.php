<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Message Received</title>
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
        <h1>New Message Received</h1>
        <p>You have received a new message in your website with the following details:</p>

        <div class="details">
            <strong>From:</strong>
            <p>{{ $email_name }}</p>

            <strong>Email:</strong>
            <p>{{ $email_address }}</p>

            @if ($email_subject)
                <strong>Subject:</strong>
                <p>{{ $email_subject }}</p>
            @endif

            <strong>Message:</strong>
            <p>{{ $email_body }}</p>
        </div>
        <p>Thank you!</p>
    </body>
</html>
