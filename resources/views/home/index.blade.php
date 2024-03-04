<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <link rel="stylesheet" href="css/local/private/loadingOverlay.css">
        <link rel="stylesheet" href="css/local/private/flashMessages.css">
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="{{ (app()->environment() === 'prod')
            ? mix('js/prod/private/app.js')
            : 'js/local/private/serverLessRequests.js' }}">
        </script>
    </head>
    <body>
        <div id="overlay">
            <div id="overlay-content"> ⏳</div>
        </div>
        <h2>Home</h2>
        <button id="logout-btn" onclick="serverLessRequests.doLogout()">
            Logout
        </button>
        <script>
            serverLessRequests.checkAuthAndGetData('/api/v1/home');
        </script>
    </body>
</html>
