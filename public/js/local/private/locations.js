let locationsModule = (function($)
{
    function init(frontend_endpoint)
    {
        $('#resetRedisCache').on('click', function()
        {
            $.ajax({
                url: '/reset_redis_cache_for_locations',
                type: 'GET',
                success: function()
                {
                    // Logic to handle the response
                    $("#loadLogs").val(
                        $('#loadLogs').val()
                        + "All Redis cache was cleaned! \n\n")
                        .addClass("animate__animated animate__headShake");

                    setTimeout(function()
                    {
                        $(".backInRight").removeClass("animate__animated animate__headShake");
                    }, 1000);
                },
                error: function()
                {
                    // Logic to handle errors
                    alert('An error occurred while trying to reset all caches.');
                }
            });
        });

        $('#resetAPCuCache').on('click', function()
        {
            $.ajax({
                url: '/reset_apcu_cache_for_locations',
                type: 'GET',
                success: function()
                {
                    // Logic to handle the response
                    $("#loadLogs").val(
                        $('#loadLogs').val()
                        + "APCu cache for "
                        + frontend_endpoint
                        + " was cleaned! \n\n")
                        .addClass("animate__animated animate__headShake");

                    setTimeout(function()
                    {
                        $(".backInRight").removeClass("animate__animated animate__headShake");
                    }, 1000);
                },
                error: function(xhr, status, error)
                {
                    // Logic to handle errors
                    alert('An error occurred while trying to reset all caches.');
                }
            });
        });

        function getCoordsByLocationString(addressComingFromInput, zoom)
        {
            // incoming user address from input should be encoded to be used in url
            const encodedAddress = encodeURIComponent("Portugal, " + addressComingFromInput);
            const nominatimURL   = 'https://nominatim.openstreetmap.org/search?addressDetails=1&q=' + encodedAddress + '&format=json&limit=1';

            // fetch lat and long and use it with leaflet
            fetch(nominatimURL)
                .then(response => response.json())
                .then(data => {
                    const lat = data[0].lat;
                    const long = data[0].lon;

                    if (mymap !== undefined) {
                        mymap.remove();
                    }

                    mymap = L.map('map').setView([lat, long], zoom);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: ''
                    }).addTo(mymap);
                });
        }

        let mymap = L.map('map').setView([39.557191, -7.8536599], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(mymap);

        // Function to build URL based on provided parameters and values
        function buildUrl(baseUrl, params)
        {
            let url = baseUrl + '?';
            Object.entries(params).forEach(([key, value], index, array) => {
                url += `${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
                if (index !== array.length - 1) {
                    url += '&';
                }
            });
            return url;
        }

        // Object containing the parameters
        let params = {
            level: "district"
        };

        // ===========================================================
        // Set all default district list
        serverLessRequests.checkAuthAndGetData(buildUrl('/api/v1/locations', params)).then(response => {

            // Clear the selection fields, if options already exist
            $('#municipalitySelect').empty();
            $('#parishSelect').empty();

            // Add the default option to the district select
            $('#districtSelect').empty().append($('<option>', {
                value: "",
                text: "Select a district",
                disabled: true, // Deactivate the option
                selected: true, // Select by default
                hidden: true // Hide the option
            }));

            // Iterate over the results and add options to the district select
            $.each(response.result.locations, function(index, district)
            {
                let districtObj = JSON.parse(district);
                $('#districtSelect').append($('<option>', {
                    value: districtObj.district_code,
                    text: districtObj.district_name
                }));
            });

            // Show animation for the right movement
            $(".backInRight").show()
                .addClass("animate__animated animate__backInRight");

            // Remove animation after a delay
            setTimeout(function() {
                $(".backInRight").removeClass("animate__animated animate__backInRight");
            }, 1000);

            // Show animation for the left movement
            $(".backInLeft").show()
                .addClass("animate__animated animate__backInLeft");

            // Remove animation after a delay
            setTimeout(function() {
                $(".backInLeft").removeClass("animate__animated animate__backInLeft");
            }, 1000);

            // Hide the overlay after a delay and update the log with district list information
            setTimeout(function()
            {
                $("#overlay").hide();
                $("#loadLogs").val("Districts list loaded from: '" + response.result.source + "'\n\n")
                    .scrollTop($("#loadLogs")[0].scrollHeight);
            }, 500);
        });

        // ===========================================================
        // Set municipality list according the selected district
        $('#districtSelect').on('change', function()
        {
            // Get the selected district code
            let selectedDistrictCode = $(this).val();

            // If no district is selected, clear the municipality select
            if (!selectedDistrictCode) {
                $('#municipalitySelect').empty();
                return;
            }

            // Get the text of the selected option
            let selectedText = $(this).find('option:selected').text();

            // Call the function to retrieve coordinates based on the selected location string, with a zoom level of 10
            getCoordsByLocationString(selectedText, 10);

            // Build new parameters to retrieve the municipalities of the selected district
            let districtParams = {
                level: "municipality",
                options: selectedDistrictCode
            };

            // Request to get the municipalities of the selected district
            serverLessRequests.checkAuthAndGetData(buildUrl('/api/v1/locations', districtParams))
                .then(response => {

                    // Clear the parish select
                    $('#parishSelect').empty();

                    // Clear and add the default option to the municipality select
                    $('#municipalitySelect').empty().append($('<option>', {
                        value: "",
                        text: "Select a municipality",
                        disabled: true, // Deactivate the option
                        selected: true, // Select by default
                        hidden: true // Hide the option
                    }));

                    // Iterate over the results and add options to the municipality select
                    $.each(response.result.locations, function(index, municipality) {
                        let municipalityObj = JSON.parse(municipality);
                        $('#municipalitySelect').append($('<option>', {
                            value: municipalityObj.municipality_code,
                            text: municipalityObj.municipality_name
                        }));
                    });

                    // Show the municipality select and hide the parish select
                    $('#municipalitySelect').show();
                    $("#parishSelect").hide();

                    // Update the log with the municipality list information and add animation
                    $("#loadLogs").val(
                        $('#loadLogs').val()
                        + "Municipality list for '" + selectedText
                        + "' loaded from: '" + response.result.source + "'\n\n")
                        .scrollTop($("#loadLogs")[0].scrollHeight)
                        .addClass("animate__animated animate__headShake");

                    // Remove the animation after a delay
                    setTimeout(function() {
                        $(".backInRight").removeClass("animate__animated animate__headShake");
                    }, 1000);
                })
                .catch(error => {
                    console.error(
                        'Error fetching municipalities:',
                        error
                    );
                }).finally(() => {

                // Hide the overlay regardless of success or failure
                setTimeout(function()
                {
                    $("#overlay").hide();
                }, 100); // 1000 milliseconds = 1 second
            });
        });

        // ===========================================================
        // Set parish list according the selected municipality
        $('#municipalitySelect').on('change', function()
        {
            // Get the selected municipality code
            let selectedMunicipalityCode = $(this).val();

            // If no municipality is selected, clear the parish select
            if (!selectedMunicipalityCode) {
                $('#municipalitySelect').empty();
                return;
            }

            // Build new parameters to retrieve the parishes of the selected municipality
            let municipalityParams = {
                level: "parish",
                options: selectedMunicipalityCode
            };

            // Get the text of the selected option
            let selectedText = $(this).find('option:selected').text();

            // Call the function to retrieve coordinates based on the selected location string, with a zoom level of 13
            getCoordsByLocationString(selectedText, 13);

            // Request to get the parishes of the selected municipality
            serverLessRequests.checkAuthAndGetData(buildUrl('/api/v1/locations', municipalityParams))
                .then(response => {
                    // Clear the parish select
                    $('#parishSelect').empty();

                    // Add the default option
                    $('#parishSelect').append($('<option>', {
                        value: "",
                        text: "Select a parish",
                        disabled: true, // Deactivate the option
                        selected: true, // Select by default
                        hidden: true // Hide the option
                    }));

                    // Iterate over the results and add options to the parish select
                    $.each(response.result.locations, function(index, parish) {
                        let parishObj = JSON.parse(parish);
                        $('#parishSelect').append($('<option>', {
                            value: parishObj.parish_code,
                            text: parishObj.parish_name
                        }));
                    });

                    // Show the parish select and add animation
                    $('#parishSelect').show().addClass("animate__animated animate__backInLeft");

                    // Update the log with the parish list information and add animation
                    $("#loadLogs").val(
                        $('#loadLogs').val()
                        + "Parish list for '" + selectedText
                        + "' loaded from: '" + response.result.source + "'\n\n")
                        .scrollTop($("#loadLogs")[0].scrollHeight)
                        .addClass("animate__animated animate__headShake");

                    // Remove the animation after a delay
                    setTimeout(function() {
                        $(".backInRight")
                            .removeClass("animate__animated animate__headShake");
                    }, 1000);
                })
                .catch(error => {
                    console.error('Error fetching parishes:', error);
                }).finally(() => {

                // Hide the overlay regardless of success or failure
                setTimeout(function()
                {
                    $("#overlay").hide();
                }, 100); // 1000 milliseconds = 1 second
            });
        });

        // ===========================================================
        // Select parish location
        $('#parishSelect').on('change', function()
        {
            // Get the text of the selected option
            let selectedText = $(this).find('option:selected').text();

            // Call the function to retrieve coordinates based on the selected location string, with a zoom level of 15
            getCoordsByLocationString(selectedText, 15);

            // Update the log with the selected parish information and add animation
            $("#loadLogs").val(
                $('#loadLogs').val()
                + "Parish '" + selectedText
                + "' selected!\n\n")
                .scrollTop($("#loadLogs")[0].scrollHeight)
                .addClass("animate__animated animate__headShake");

            // Remove the animation after a delay
            setTimeout(function()
            {
                $(".backInRight").removeClass("animate__animated animate__headShake");
            }, 1000);
        });
    }

    // Return init
    return {
        init: init
    };

})(jQuery);

