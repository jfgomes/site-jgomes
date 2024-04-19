<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User; // Import the User model if necessary
use Laravel\Sanctum\Sanctum;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase; // This will recreate the database before each test

    /**
     * Tests if the index method returns an instance of JsonResponse.
     *
     * @return void
     */
    public function testIndexReturnsJsonResponse()
    {
        $controller = new HomeController();
        $request = new Request();

        $response = $controller->index($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
