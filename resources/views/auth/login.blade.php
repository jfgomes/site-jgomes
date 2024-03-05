<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
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
    <body id="login-body">
        <div id="overlay">
            <div id="overlay-content"> ⏳</div>
        </div>
        @if($errorMessage)
            <div class="flash-message error">
                {{ $errorMessage }}
            </div>
        @endif
        @if($successMessage)
            <div class="flash-message success">
                {{ $successMessage }}
            </div>
        @endif
        <h2>Login</h2>
        <form id="login-form">
            <div>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required>
            </div><br>
            <button id="login-btn" type="button">
                Login
            </button>
        </form>
        <script>
            // Initialize the module when the DOM is ready.
            $(document).ready(function()
            {
                serverLessRequests.init();
            });
        </script>
    </body>
    <script>
        // Initialize the module when the DOM is ready.
        $(document).ready(function()
        {
            serverLessRequests.init();
        });
    </script>
</html>
