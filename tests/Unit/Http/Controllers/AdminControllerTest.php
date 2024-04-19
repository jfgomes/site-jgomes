<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    public function testIndexReturnsJsonResponse(): void
    {
        // Arrange
        $controller = new AdminController();
        $request = $this->createMock(Request::class);

        // Act
        $response = $controller->index($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
    }


    public function testIndexContainsUserData(): void
    {
        // Arrange
        $controller = new AdminController();
        $user = ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];
        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('user')->willReturn((object) $user);

        // Act
        $response = $controller->index($request);
        $data = $response->getData();

        // Assert
        $this->assertObjectHasProperty('result', $data);
        $this->assertObjectHasProperty('user', $data->result);
        $this->assertEquals($user, (array) $data->result->user);
    }
}
