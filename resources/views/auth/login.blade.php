<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

@if (session('error'))
    <h2>Login</h2>
    <div style="color: red;">{{ session('error') }}</div>
@endif

<div id="response-message"></div>

@if(auth()->check())
    <h2>Logout</h2>
    <form id="logout-form">
        @csrf
        <button type="button" onclick="logout()">Logout</button>
    </form>
@else
    <h2>Login</h2>
    <form id="login-form">
        {{-- Adicione o token CSRF manualmente --}}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </div>

        <br>

        <button type="button" onclick="submitForm()">Login</button>
    </form>
@endif

<script>
    function submitForm()
    {
        let formData = $('#login-form').serialize();
        $.ajax({
            type: 'POST',
            url: '/api/v1/login',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            xhrFields: {
                withCredentials: true
            },
            success: function(response) {
                console.log(response);
                // Handle successful login
                $('#response-message')
                    .html('<div style="color: green;">Login successful!</div>');
                // You can redirect the user or perform other actions here
            },
            error: function(xhr) {
                // Handle login error
                let errorMessage = xhr.responseJSON.message || 'Login failed. Please try again.';
                $('#response-message')
                    .html('<div style="color: red;">' + errorMessage + '</div>');
            }
        });
    }

    function logout() {
        $.ajax({
            type: 'POST',
            url: '/api/v1/logout',
            success: function(response)
            {
                console.log(response.message);
                // Handle any additional actions after logout
            },
            error: function(xhr) {
                console.error('Logout failed:', xhr.responseText);
            }
        });
    }
</script>

</body>
</html>
