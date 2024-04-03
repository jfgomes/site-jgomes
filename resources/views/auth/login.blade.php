<!DOCTYPE html>
    <!--[if lt IE 8 ]><html class="no-js ie ie7" lang="en"> <![endif]-->
    <!--[if IE 8 ]><html class="no-js ie ie8" lang="en"> <![endif]-->
    <!--[if (gte IE 8)|!(IE)]><!--><html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <!--- Basic Page Needs
        ================================================== -->
        <title>Login</title>
        @include('partials.meta')

        <!-- Favicons
         ================================================== -->
        <link rel="shortcut icon" href="favicon.png" >

        <!-- CSS + JS
        ================================================== -->
        @include('partials.css_js')
    </head>
    <body>
        <!-- Overlay to block the page during the loading
        ================================================== -->
        @include('partials.overlay')

        <!-- Header ==================================================== -->
        <header>
            <div class="header-content">
                <h1>🔐 Login</h1>
                <div class="button-container">
                    <a href="/">
                        <button class="publicBtn">
                            ⏪ Go back to CV site
                        </button>
                    </a>
                </div>
            </div>
        </header> <!-- Header End -->

        <!-- footer
        ================================================== -->
        <footer>
            @include('partials.cookies')
        </footer> <!-- Footer End-->

        <!-- Modal -->
        <div id="loginModal">
            <form id="loginForm">
                <h2>🔐</h2>
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
            $(document).ready(function()
            {
                // Initialize the module when the DOM is ready.
                serverLessRequests.init();

                // Extra style actions
                $('#loginModal').css('display', 'flex');
                $('#overlay').css('display', 'block').hide();
            });
        </script>
    </body>
</html>
