<!DOCTYPE html>
    <!--[if lt IE 8 ]><html class="no-js ie ie7" lang="en"> <![endif]-->
    <!--[if IE 8 ]><html class="no-js ie ie8" lang="en"> <![endif]-->
    <!--[if (gte IE 8)|!(IE)]><!--><html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <!--- Basic Page Needs
        ================================================== -->
        <title>Home</title>
        @include('partials.meta')

        <!-- Favicons
         ================================================== -->
        <link rel="shortcut icon" href="favicon.png" >

        <!-- JS + CSS
        ================================================== -->
        @include('partials.css_js')
    </head>
    <body>
        <!-- Overlay to block the page during the loading
         ================================================== -->
        @include('partials.overlay')

        <!-- Header
        ================================================== -->
        <header>
            <div class="header-content">
                <h1>Home</h1>
                <div class="button-container">
                    <a href="/admin" class="adminLink">
                        <button class="adminBtn">
                            👮‍♀️ Admin
                        </button>
                    </a>
                    <a href="/lang" class="adminLink">
                        <button class="adminBtn">
                            📝 Translations
                        </button>
                    </a>
                    <a href="/locations">
                        <button class="adminBtn">
                            🗺️ Locations
                        </button>
                    </a>
                    @include('partials.logout')
                </div>
            </div>
        </header> <!-- Header End -->

        <!-- footer
        ================================================== -->
        <footer>
            @include('partials.cookies')
        </footer> <!-- Footer End-->

        <!-- Get data
        ================================================== -->
        <script>
            serverLessRequests.checkAuthAndGetData('/api/v1/home').then(response => {
                $("#overlay").hide();
            });
        </script>
    </body>
</html>
