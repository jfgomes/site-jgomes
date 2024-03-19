<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MapController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'result' => compact('user')
        ]);
    }

    public function testMapCaches(Request $request): JsonResponse
    {
        $user = ['user' => ['role' => null]];
        $locations = ['locations' => null];

        if ($request->has('level')) {
            $level = $request->get('level');
            $code = $request->has('options')
                ? $request->get('options') : null;

            $locations = $this->getLocations($level, $code);
        }

        return response()->json([
            'result' => array_merge($locations, $user)
        ]);
    }


    private function getLocationsFromAPCu($level, $code)
    {
        $result = array();
        try {

            if ($level == "districts") {
                $codeForApcu = "district";

            } else {

                $codeForApcu = "{$level}_{$code}";
            }


            foreach (new \APCUIterator("/^location_pt_$codeForApcu/") as $counter) {
                $results = $counter['value'];
            }

            return $result;

        } catch (\Exception $e) {
            echo 'Erro ao iterar sobre os contadores APCu: ' . $e->getMessage(); die;
        }

    }




    private function getLocations(string $level, string $code = null) : array
    {

        // Validate levels
        if ($level != "districts" && $level != "municipality" && $level != "parish")
        {
            die("invalid level");
        }

        $results = [];

        try {

            if ($level == "districts")
            {
                $codeForApcu = "district" ;

            } else {

                $codeForApcu = "{$level}_{$code}";
            }


            foreach (new \APCUIterator("/^location_pt_$codeForApcu/") as $counter) {
                 $results['locations'][] = $counter['value'];
            }
        } catch (\Exception $e) {
            echo 'Erro ao iterar sobre os contadores APCu: ' . $e->getMessage();
        }

        // Verificar se há resultados antes de retornar
        if (!empty($results['locations'])) {
            $results['source'] = 'APCu';
             return $results;
        }


       // var_dump($results); die;


        // Go to Redis. Case exists save in APCu and return.
        Redis::select(2);
         $redis = Redis::connection();
        if ($redis->ping()) {
            // Conexão bem-sucedida
           //  $code = ($code != "%") ? $code : "";

            $level_aux = $level;
            if ($level == "districts")
            {
                $code = "";
                $level_aux = "district";
            }


            $pattern = "location_pt_{$level_aux}_{$code}*";
            $keys = $redis->keys($pattern);

            foreach ($keys as $key)
            {
                $value = Redis::get($key);
                $results['locations'][] = $value;

                // Save in APCu
                 apcu_store($key, $value);
            }

        } else {
            // Conexão falhou
            echo "Não foi possível estabelecer conexão com o Redis.";
        }

        if (!empty($results))
        {
            sort($results['locations']);
            $results['source'] = 'Redis';
            return $results;
        }




        $level_aux = "";
        $level_aux2 = $level;
        if ($level == "districts")
        {
            $level_aux = "district";
            $level_aux2 = "district";
        }

        if ($level == "municipality")
        {
            $level_aux = "district";
        }

        if ($level == "parish")
        {
            $level_aux = "municipality";
        }

        // Go to DB. Case exists save in Redis and APCu.
        {
            $dbResult = DB::table('locations_pt')
                ->select("{$level_aux2}_name", "{$level_aux2}_code")
                ->distinct();
                if ($level != "districts"){
                    $dbResult = $dbResult->where("{$level_aux}_code", '=', $code);
                }
                $dbResult->orderBy("{$level_aux2}_name");
                $dbResult = $dbResult->get()
                ->toArray();
        }

        // Save caches
        foreach ($dbResult as $value)
        {
            $level_aux = $level;
            if ($level == "districts")
            {
                $level_aux = "district";
            }



            $propertyValue = $results['locations'][] = json_encode($value);
            $propertyKey   = "{$level_aux}_code";


           // var_dump($value); die;

            // Save in Redis
            Redis::set("location_pt_{$level_aux}_{$value->$propertyKey}", $propertyValue);

            // Save in APCu
            apcu_store("location_pt_{$level_aux}_{$value->$propertyKey}", $propertyValue);
        }

        $results['source'] = 'Database';
        return $results;
    }

}
