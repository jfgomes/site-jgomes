<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <!--- Basic Page Needs ========================================= -->
        <title>🇵🇹 Locations</title>
        @include('partials.meta')

        <!-- Favicons ================================================== -->
        <link rel="shortcut icon" href="favicon.png" >

        <!-- JS + CSS ================================================== -->
        @include('partials.css_js')

    </head>
    <body>
        <!-- Overlay to block the page during the loading ============== -->
        @include('partials.overlay')

        <!-- Header ==================================================== -->
        <header>
            <div class="header-content">
                <h1>🇵🇹 Locations</h1>
                <div class="button-container">
                    <a href="/home">
                        <button class="adminBtn">
                            🏠 Home
                        </button>
                    </a>
                    <a href="/lang" class="">
                        <button class="adminBtn">
                            📝 Translations
                        </button>
                    </a>

                    @include('partials.logout')
                </div>
            </div>
        </header> <!-- Header End -->

        <!-- Empty content to create a fake height ==================== -->
        <div class="fake-height"></div>

        <!-- Map ====================================================== -->
        <section>
            <div class="map-container">
                <div id="map-level-selector">
                    <div class="select-container">
                        <label for="districtSelect"></label>
                        <select id="districtSelect" class="backInLeft custom-btn hidden"></select>

                        <label for="municipalitySelect"></label>
                        <select id="municipalitySelect" class="custom-btn hidden"></select>

                        <label for="parishSelect"></label>
                        <select id="parishSelect" class="custom-btn hidden"></select>
                    </div>
                    <div id="map"></div>
                </div>
                <div class="side-panel">
                    <div class="backInRight buttons-container" style="min-height: 36px">
                        <button id="resetRedisCache" class="custom-btn">
                            Clean Redis
                        </button>
                        <button id="resetAPCuCache" class="custom-btn">
                            Clean APCu
                        </button>
                    </div>
                    <div class="logs-container">
                        <label for="loadLogs"></label>
                        <textarea id="loadLogs" readonly></textarea>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer ================================================== -->
        <footer>
            @include('partials.cookies')
        </footer> <!-- Footer End-->

        <!-- Get default locations data ============================== -->
        <script>
            $(document).ready(function()
            {
                let frontend_endpoint  = '{{ (app()->environment() === 'prod') ? env('APP_URL') : url()->to('/') }}';
                let show_cache_buttons = '{{ $hasSpecialCookie }}';

                // Initialize the module when the DOM is ready.
                locationsModule.init(
                    frontend_endpoint,
                    show_cache_buttons
                );
            });
        </script>
    </body>
</html>
