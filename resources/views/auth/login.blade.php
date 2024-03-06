<!DOCTYPE html>
    <!--[if lt IE 8 ]><html class="no-js ie ie7" lang="en"> <![endif]-->
    <!--[if IE 8 ]><html class="no-js ie ie8" lang="en"> <![endif]-->
    <!--[if (gte IE 8)|!(IE)]><!--><html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <!--- Basic Page Needs
        ================================================== -->
        <title>Authentication</title>
        <meta charset="utf-8">
        <meta name="description" content="Private area">
        <meta name="author" content="José Gomes">

        <!-- Mobile Specific Metas
        ================================================== -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <!-- Favicons
         ================================================== -->
        <link rel="shortcut icon" href="favicon.png" >

        <!-- JS + CSS
        ================================================== -->
        @if(app()->environment('prod'))
            <script src="{{ mix('js/prod/app.js') }}"></script>
            <link rel="stylesheet" href="{{ mix('css/prod/app.css') }}">
        @else
            <!-- CSS -->
            <link rel="stylesheet" href="css/cookies.css">
            <link rel="stylesheet" href="css/local/private/loadingOverlay.css">
            <link rel="stylesheet" href="css/local/private/flashMessages.css">
            <link rel="stylesheet" href="css/local/private/login.css">
            <link rel="stylesheet" href="css/local/private/general.css">

            <!-- JS -->
            <script src="/js/jquery-3.7.1.js"></script>
            <script src="/js/local/private/serverLessRequests.js"></script>
            <script src="/js/cookies.js"></script>
        @endif
    </head>
    <body>
        <!-- Overlay to block the page during the loading
         ================================================== -->
        <div id="overlay">
            <div id="overlay-content"> ⏳</div>
        </div>

        <!-- Header
        ================================================== -->
        <header>
            <div class="header-content">
                <h1>🔐Authentication</h1>
            </div>
        </header> <!-- Header End -->

        <!-- footer
        ================================================== -->
        <footer>
            <div id="cookie-consent-bar" class="cookie-consent-barExtra">
                <p>🍪 This website uses cookies to ensure you get the best experience on our website.</p>
                <button onclick="acceptCookies()">✅ Got it!</button>
            </div>
        </footer> <!-- Footer End-->

        <!-- Modal -->
        <div id="loginModal">
            <form id="loginForm">
                <!-- Messages area
                ================================================== -->
                <div id="loginMsg">
                    @if($errorMessage)
                        <div class="flashMessage error">
                            {{ $errorMessage }}
                        </div>
                    @endif
                    @if($successMessage)
                        <div class="flashMessage success">
                            {{ $successMessage }}
                        </div>
                    @endif
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button id="loginBtn" type="button">
                    Login
                </button>
            </form>
        </div>
        <script>
            // Initialize the module when the DOM is ready.
            $(document).ready(function()
            {
                serverLessRequests.init();

                // Extra style actions
                $('#loginModal').css('display', 'flex');
                $('#overlay').css('display', 'block').hide();
            });
        </script>
    </body>
</html>
