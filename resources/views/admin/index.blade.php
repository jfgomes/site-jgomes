<!DOCTYPE html>
    <!--[if lt IE 8 ]><html class="no-js ie ie7" lang="en"> <![endif]-->
    <!--[if IE 8 ]><html class="no-js ie ie8" lang="en"> <![endif]-->
    <!--[if (gte IE 8)|!(IE)]><!--><html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <!--- Basic Page Needs
        ================================================== -->
        <title>Admin</title>
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
            <link rel="stylesheet" href="css/local/private/home.css">
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
                <h1>Admin</h1>
                <button id="logout-btn" onclick="serverLessRequests.doLogout()">Logout</button>
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

        <!-- Get data
        ================================================== -->
        <script>
            serverLessRequests.checkAuthAndGetData('/api/v1/admin');
        </script>
    </body>
</html>
