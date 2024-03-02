<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Form</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{ (app()->environment() === 'prod')
            ? mix('js/prod/private/app.js')
            : 'js/local/private/serverLessRequests.js' }}">
        </script>
    </head>
    <body>
        <div id="response-message"></div>
        <h2>Login</h2>
        <form id="login-form">
            <div>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required>
            </div>
            <br>
            <button id="login-btn" type="button">
                Login
            </button>
        </form>
    </body>
</html>
