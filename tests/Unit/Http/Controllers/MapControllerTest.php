<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Http\Controllers\MapController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MapControllerTest extends TestCase
{
    public function testIndexReturnsJsonWithUser()
    {
        // Arrange
        $controller = new MapController();
        $request = new Request();
        $request->setUserResolver(function () {
            return ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];
        });

        // Act
        $response = $controller->index($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('result', $responseData);
        $this->assertEquals(['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'], $responseData['result']['user']);
    }
}
