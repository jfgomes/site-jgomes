<!DOCTYPE html>
    <!--[if lt IE 8 ]><html class="no-js ie ie7" lang="en"> <![endif]-->
    <!--[if IE 8 ]><html class="no-js ie ie8" lang="en"> <![endif]-->
    <!--[if (gte IE 8)|!(IE)]><!--><html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <!--- Basic Page Needs
        ================================================== -->
        <title>Admin</title>
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
                <h1>Admin</h1>
                <div class="button-container">
                    <a href="/home"><button class="adminBtn">🏠 Home</button></a>
                    <a href="/map-caches"><button class="adminBtn">🗺️ Map / Caches</button></a>
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
            serverLessRequests.checkAuthAndGetData('/api/v1/admin');
        </script>
    </body>
</html>
