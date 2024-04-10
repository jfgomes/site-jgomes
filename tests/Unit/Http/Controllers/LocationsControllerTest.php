<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\LocationsController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Tests\TestCase;

class LocationsControllerTest extends TestCase
{
    public function testIndexReturnsJsonWithUser()
    {
        // Create an instance of the LocationsController controller
        $controller = new LocationsController();

        // Create an empty request
        $request = new Request();

        // Call the index method of the controller
        $response = $controller->index($request);

        // Check if the response is an instance of JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Check if the response contains the 'result' key
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('result', $responseData);

        // Check if the 'user' key is present in 'result'
        $this->assertArrayHasKey('user', $responseData['result']);
    }
}
