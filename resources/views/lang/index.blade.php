<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <!--- Basic Page Needs ========================================= -->
        <title>Translations</title>
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
                <h1>Translations</h1>
                <div class="button-container">
                    <a href="/home">
                        <button class="adminBtn">
                            🏠 Home
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

        <!-- Language ====================================================== -->
        <section style=" float: right;  margin-top: 50px; ">
            <form id="translationsForm" style="
    z-index: 9999;
text-align: right;
    height: 104%;
    background-color: #0c5460;
  color: white;
  border: 1px solid white;
  font-size: 10px;
  margin-right: 9px;
  overflow-y: scroll;
            ">

                <div id="translationsContainer" style=" padding: 30px;"></div>
            </form>
        </section>
        <section style=" float: right;  margin-top: 50px; ">
            <div style="z-index: 9999999;">
                <button  class="adminBtn" style="padding:40px;margin-right: 30px" onclick="saveTranslations(event)">Save</button>
            </div>
        </section>
        <!-- Footer ================================================== -->
        <footer>
            @include('partials.cookies')
        </footer> <!-- Footer End-->

        <!-- Get default language data ============================== -->
        <script>
            $(document).ready(function()
            {
                // Check authentication, get translation data from the server and create the fields
                serverLessRequests.checkAuthAndGetData('/api/v1/translations').then(response =>
                {
                    $("#overlay").hide();

                    // Clear the container before adding new fields
                    $('#translationsContainer').empty();

                    // Iterate over the translation files
                    $.each(response.data, function(file, translationKeys)
                    {
                        // Add a title for the translation file
                        $('#translationsContainer').append('<h2>Page: ' + file + '</h2>');

                        // Iterate over the translation keys within the file
                        $.each(translationKeys, function(translationKey, translationValues)
                        {
                            // Add a title for the translation key
                            let inputField = '<div style="margin-top: 10px;">';
                                inputField += '<label style="font-size: 15px"><strong>' + translationKey + '</strong>: </label>';

                            // Iterate over the translation values for each language
                            $.each(translationValues, function(lang, translationValue)
                            {
                                // Compose a unique key for the translation value
                                let composedKey = file + "###-###" + lang + "###-###" + translationKey + "###-###";

                                let emoji;

                                // Create a text field for each translation value
                                switch(lang.toUpperCase()) {
                                    case 'EN':
                                        emoji = '🏴󠁧󠁢󠁥󠁮󠁧󠁿';
                                        break;
                                    case 'PT':
                                        emoji = '🇵🇹';
                                        break;
                                    case 'JP':
                                        emoji = '🇯🇵';
                                        break;
                                    default:
                                        emoji = '';
                                }

                                inputField += '<label style="margin-left: 20px;font-size: 30px;position: relative; top: 8px;" for="' + composedKey + '">' + emoji + '</label>';
                                if (translationKey.includes('textarea'))
                                {
                                    inputField += '<textarea style="margin-top: 10px; margin-left: 10px; height: 200px; width:300px; padding:5px; " name="' + composedKey + '">' + translationValue + '</textarea>';

                                } else {

                                    inputField += '<input style="width:300px; margin-left: 10px; height: 30px; padding:5px; " type="text" name="' + composedKey + '" value="' + translationValue + '">';
                                }

                            });

                            inputField += '</div>';
                            $('#translationsContainer').append(inputField);
                        });
                    });
                });
            });

            // Update translations
            function saveTranslations(event)
            {
                // Prevent default behavior
                event.preventDefault();

                // Post data updated
                serverLessRequests.checkAuthAndPostData(
                     // Set endpoint
                     '/api/v1/translations',
                     // Set dat
                     $("#translationsForm").serialize()
                ).then(response => {

                    // Output from server
                    alert(response.result);

                    // Hide overlay
                    $("#overlay").hide();
                });
            }
        </script>
    </body>
</html>
