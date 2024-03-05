<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        @if(app()->environment('prod'))
            <script src="{{ mix('js/prod/app.js') }}"></script>
            <link rel="stylesheet" href="{{ mix('css/prod/app.css') }}">
        @else
            <link rel="stylesheet" href="css/local/private/loadingOverlay.css">
            <link rel="stylesheet" href="css/local/private/flashMessages.css">
            <script src="/js/jquery-3.7.1.js"></script>
            <script src="/js/local/private/serverLessRequests.js"></script>
        @endif
    </head>
    <body>
        <div id="overlay">
            <div id="overlay-content"> ⏳</div>
        </div>
        <h2>Admin</h2>
        <button id="logout-btn" onclick="serverLessRequests.doLogout()">
            Logout
        </button>
        <script>
            serverLessRequests.checkAuthAndGetData('/api/v1/admin');
        </script>
    </body>
</html>
