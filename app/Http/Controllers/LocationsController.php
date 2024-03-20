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
