![Caches Logo](https://jgomes.site/images/cs/redis-apc.png)

## Introduction
- For PHP applications, I propose an efficient architecture for handling geographic data, in this example case, for districts, municipalities, and parishes, which are stored as the source of truth in a MySQL database. 
- To enhance performance and reduce load on the database, I implement a caching system using Redis as the primary cache and APCu for caching on each frontend ( thinking about some load balancing system ).

## Source of true = DB
For this situation, we want the database not to receive many requests and for them to be resolved by cache systems,
however the source of truth remains the information stored in the database.

![Redis-commander](https://jgomes.site/images/cs/redis/db.png)

## What is Redis and why?
Redis is an in-memory caching storage system known for its speed and flexibility. 
It operates as an in-memory database that can be used as a cache, primary database, or a combination of both. 

In the context of this case study, Redis acts as a primary cache, storing accessed geographic data for all frontends.

## How does Redis work?
Redis operates in main memory and is highly efficient in reading and writing data. 
It utilizes a key-value structure to store data, making it extremely fast for retrieving information. 
Additionally, Redis supports various data types such as strings, lists, sets, etc., providing flexibility for various caching needs.

## Redis configurations
#### Redis Dockerfile
```
    FROM redis:latest
    COPY redis-local.conf /usr/local/etc/redis/redis.conf
    CMD ["redis-server", "/usr/local/etc/redis/redis.conf"]
```

#### Redis ports config ( To copy to redis.conf )
``` 
    port ${REDIS_PORT}
    requirepass ${REDIS_PASS}
```

#### Service and orchestration for Redis
```
    redis:
        container_name: jgomes_site_dev_redis
        restart: always
        build: './redis'
        volumes:
            - redis:/var/lib/redis
            - dbRedis:/data
        ports:
            - "6378:6378"
        networks:
            - redis-network
```

#### Redis-commander Dockerfile
```
    # redis-commander base image
    FROM rediscommander/redis-commander:latest
    
    # Port expose
    EXPOSE 8081
```

#### Service and orchestration for Redis-commander
```
    redis-commander:
        container_name: jgomes_site_dev_redis-commander
        build: './redis-commander'
        platform: linux/amd64  # Forces the platform to amd64
        restart: always
        ports:
            - "8082:8081"
        networks:
            - redis-network
        depends_on:
            - redis
        environment:
            - REDIS_HOSTS=${REDIS_HOSTS}
            - HTTP_USER=${REDIS_USER}
            - HTTP_PASSWORD=${REDIS_PASS}
```

#### Redis / Redis-commander interface
![Redis-commander](https://jgomes.site/images/cs/redis/redis.png)

## What is APCu and why?
APCu (Alternative PHP Cache) is a PHP extension that provides a local, in-memory caching system for storing temporary data. 
It is useful for storing data specific to a single PHP execution, such as database query results or complex computation results.

## How does APCu work?
APCu stores data in RAM, making it very fast for retrieving information compared to reading from a database or even disk cache. 
It operates within the context of a single PHP execution, meaning data stored with APCu is accessible only by the PHP process that created it. 
This makes it ideal for storing temporary and session-specific data.

## APCu configurations
#### APCu service controller configuration customizations to adapt to Laravel
```
<?php

namespace App\Http\Controllers;
class ApcController extends Controller
{
    public function index()
    {
        // Get the values that came from apcu request
        $scope = request('SCOPE', 'A');
        $sort1 = request('SORT1', 'H');
        $sort2 = request('SORT2', 'D');
        $count = request('COUNT ', 20);
        $ob    = request('OB', 1);

        // Inject the values to view
        return view('apc.index', [
            'SCOPE' => $scope,
            'SORT1' => $sort1,
            'SORT2' => $sort2,
            'COUNT' => $count,
            'OB'    => $ob
        ]);
    }
}
```

#### APCu's configuration file is too long and complex, so is better to put the link to repository under ( This is using the APCu default created interface, with some customizations to adapt to Laravel )
APCu interface file link [here](https://raw.githubusercontent.com/jfgomes/site-jgomes/master/resources/views/apc/index.blade.php)

#### APCu's interface 
![APCu](https://jgomes.site/images/cs/redis/apcu.png)

## Application functionality - How does it work
In the proposed system, when a frontend receives a request for geographic data, it first checks the local APCu cache. 
If the data is available in APCu, it is returned directly to the client. Otherwise, the frontend queries the Redis cache. 
If the data is cached in Redis, it is retrieved and stored locally in APCu for future requests. 
If the data is not cached in Redis, the frontend queries the main database. 
After retrieving the data from the database, it is stored in both the Redis cache and APCu, ensuring fast access to this data in the future.

## Goal
By implementing this efficient caching system using Redis as the primary cache and APCu for local caching on each frontend, 
we can significantly improve the performance and scalability of our application. 
This reduces the load on the main database, speeds up client requests, and enhances the overall user experience.

## Laravel technical implementation
#### Server less route
![Serverless route](https://jgomes.site/images/cs/redis/serve_less_route.png)

#### Server less view blade template
```
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
                    <a href="/admin" class="adminLink">
                        <button class="adminBtn">
                            👮‍♀️ Admin
                        </button>
                    </a>
                    <a href="/home">
                        <button class="adminBtn">
                            🏠 Home
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
``` 

#### Server less js to do the location requests
```
let locationsModule = (function($)
{
    function init(frontend_endpoint, show_cache_buttons)
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
                error: function(xhr, status, error)
                {
                    // Logic to handle errors
                    alert(error);
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
                    alert(error);
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

        // Function to show or hide the cache buttons based on the presence of the cookie
        function toggleButtons(show_cache_buttons)
        {
            let cacheButtons = $('#resetRedisCache, #resetAPCuCache');
            if (show_cache_buttons) {
                // If the cookie is present, show the buttons
                cacheButtons.show();
            } else {
                // Otherwise, hide the buttons
                cacheButtons.hide();
            }
        }

        toggleButtons(show_cache_buttons);

    }

    // Return init
    return {
        init: init
    };

})(jQuery);
```

#### Service route
![Service route](https://jgomes.site/images/cs/redis/service_route.png)

#### Service controller
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class LocationsController extends Controller
{
    public const DISTRICT         = "district";
    public const MUNICIPALITY     = "municipality";
    public const PARISH           = "parish";
    public const LOCATIONS        = "locations";
    public const SOURCE           = "source";
    public const LOCATION_PREFIX  = "location_pt";

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'result' => compact('user')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getLocations(Request $request): JsonResponse
    {
        $user      = ['user' => ['role' => null]];
        $locations = ['locations' => null];

        if ($request->has('level'))
        {
            $level = $request->get('level');
            $code  = $request->has('options')
                ? $request->get('options')
                : null;

            $locations = $this->getData($level, $code);
        }

        return response()->json([
            'result' => array_merge($locations, $user)
        ]);
    }

    /**
     * @param string $level
     * @param string|null $code
     * @return array
     */
    private function getData(string $level, string $code = null) : array
    {
        // Level validation
        if (!in_array($level, [self::DISTRICT, self::MUNICIPALITY, self::PARISH]))
        {
            throw new \InvalidArgumentException(
                "Invalid level. Valid levels: district, municipality, parish"
            );
        }

        // Try to get data from APCu. If found, return
        $result = $this->getLocationsFromAPCu($level, $code);
        if (!empty($result[self::LOCATIONS]))
        {
            return $result;
        }

        // Try to get data from Redis. If found warm up APCu and return
        $result = $this->getLocationsFromRedis($level, $code);
        if (!empty($result[self::LOCATIONS]))
        {
            return $result;
        }

        // Try to get data from DataBase. If found warm up APCu and Redis and return
        return $this->getLocationsFromDB($level, $code);
    }

    /**
     * @param $level
     * @param $code
     * @return array|null
     */
    private function getLocationsFromAPCu($level, $code) : array | null
    {
        // Start register time
        $startTime = microtime(true);

        // Initialize the result array
        $result[self::LOCATIONS] = [];

        // Define the cache key pattern
        $keyPattern = "/^" . self::LOCATION_PREFIX . "_{$level}_{$code}/";

        try
        {
            // Iterate over APCu cache keys matching the pattern
            foreach (new \APCUIterator($keyPattern) as $counter) {
                $result[self::LOCATIONS][] = $counter['value'];
            }

            // Sort locations and show process time
            if ($result[self::LOCATIONS])
            {
                // Sort the locations array by name
                sort($result[self::LOCATIONS]);

                // End register time
                $endTime = microtime(true);

                // Total time of process
                $processingTime       = number_format($endTime - $startTime, 6);
                $result[self::SOURCE] = "APCu ( $processingTime Sec )";
            }

        } catch (\Exception $e) {

            // Handle exceptions
            echo "Error: " . $e->getMessage();
            die;
        }

        return $result;
    }

    /**
     * @param $level
     * @param $code
     * @return array|null
     */
    private function getLocationsFromRedis($level, $code) : array | null
    {
        // Start register time
        $startTime = microtime(true);

        // Initialize the result array
        $result[self::LOCATIONS] = [];

        // Go to Redis DB 2
        Redis::select(2);
        $redis = Redis::connection();

        try
        {
            // Check if Redis connection is successful
            if ($redis->ping())
            {
                // Build the pattern for keys
                $pattern = self::LOCATION_PREFIX . "_{$level}_{$code}*";

                // Retrieve keys matching the pattern
                $keys = $redis->keys($pattern);

                // Iterate over keys and fetch values
                foreach ($keys as $key)
                {
                    $value = Redis::get($key);
                    $result[self::LOCATIONS][] = $value;

                    // Save in APCu
                    apcu_store($key, $value);
                }

                // Sort locations
                if ($result[self::LOCATIONS])
                {
                    // Sort the locations array by name
                    sort($result[self::LOCATIONS]);

                    // End register time
                    $endTime              = microtime(true);

                    // Total time of process
                    $processingTime       = number_format($endTime - $startTime, 6);
                    $result[self::SOURCE] = "Redis ( $processingTime Sec )";
                }

            } else {

                // If Redis connection fails, throw an exception
                throw new \Exception(
                    "Failed to establish connection with Redis."
                );
            }
        } catch (\Exception $e) {

            // Handle exceptions
            echo "Error: " . $e->getMessage();
            die;
        }

        return $result;
    }

    /**
     * @param $level
     * @param $code
     * @return array|null
     */
    private function getLocationsFromDB($level, $code): array | null
    {
        // Start register time
        $startTime = microtime(true);

        // Initialize the result array
        $result[self::LOCATIONS] = [];

        // Start query the database for location data
        $dbResult = DB::table("locations_pt")
            ->select("{$level}_name", "{$level}_code")
            ->distinct();

        // Case level != "district", apply a filter based on the previews level
        // Ex: if level == municipality, the filter is district
        // Ex: if level == parish, the filter is municipality
        if ($level != self::DISTRICT)
        {
            // Determine the filter level based on the current level
            $filterLevel = ($level == self::MUNICIPALITY)
                ? self::DISTRICT
                : (($level == self::PARISH)
                    ? self::MUNICIPALITY
                    : "");

            // Add the filter to the query
            $dbResult = $dbResult->where("{$filterLevel}_code", '=', $code);
        }

        // Order the results by the name field
        $dbResult->orderBy("{$level}_name");

        // Execute the query and convert the results to an array
        $dbResult = $dbResult->get()->toArray();

        // Save retrieved data to caches
        foreach ($dbResult as $value)
        {
            // Encode the value to JSON
            $propertyValue = $result[self::LOCATIONS][] = json_encode($value);
            $propertyKey   = "{$level}_code";

            // Save in Redis
            Redis::set(self::LOCATION_PREFIX . "_{$level}_{$value->$propertyKey}", $propertyValue);

            // Save in APCu
            apcu_store(self::LOCATION_PREFIX . "_{$level}_{$value->$propertyKey}", $propertyValue);
        }

        // Sort the locations array by name
        sort($result[self::LOCATIONS]);

        // End register time
        $endTime = microtime(true);

        // Total time of process
        $processingTime = number_format($endTime - $startTime, 6);;

        // Set the data source
        $result[self::SOURCE] = "Database ( $processingTime Sec )";

        // Return the result
        return $result;
    }
}
```

## Security
#### Requests authentication
To do the requests related with locations and receive the results is mandatory to load the 'serverLessRequests' module before to get the data

#### Requests special cookies
Case we want to reset the Redis / APCu caches in production we need to add the special cookie in the browser, otherwise users cannot reset both cache systems

## Requirements
- Redis server
- APCu

## Caches diagram

This graphic reflects the application functionality example in a X location place in 2 frontends with 4 hypothetical users

![Caches diagram](https://jgomes.site/images/diagrams/caches.drawio.png)

## Demonstration
#### ( Click on the image to watch the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=qu3Etw_2Ksw)
