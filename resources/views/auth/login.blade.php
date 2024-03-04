<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="css/local/private/loadingOverlay.css">
        <link rel="stylesheet" href="css/local/private/flashMessages.css">
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="{{ (app()->environment() === 'prod')
            ? mix('js/prod/private/app.js')
            : 'js/local/private/serverLessRequests.js' }}">
        </script>
    </head>
    <body id="login-body">
        <div id="overlay">
            <div id="overlay-content"> ⏳</div>
        </div>
        @if(request()->has('error'))
            <div class="flash-message error">
                {{ request()->get('error') }}
            </div>
        @endif
        @if(request()->has('success'))
            <div class="flash-message success">
                {{ request()->get('success') }}
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
    </body>
    <script>
        // Initialize the module when the DOM is ready.
        $(document).ready(function()
        {
            serverLessRequests.init();
        });
    </script>
</html>
