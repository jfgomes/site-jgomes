<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{ (app()->environment() === 'prod')
            ? mix('js/prod/private/app.js')
            : 'js/local/private/serverLessRequests.js' }}">
        </script>
    </head>
    <body>
        <h2>Home</h2>
        <button id="logout-btn">
            Logout
        </button>
        <script>
            // To avoid infinitive loop.. too many requests.. let's exclude login page..
            if (window.location.pathname !== "/login")
            {
                serverLessRequests.checkAuthAndGetData('/api/v1/home');
            }
        </script>
    </body>
</html>
