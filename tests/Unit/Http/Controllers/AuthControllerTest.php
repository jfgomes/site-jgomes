<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\AuthController;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testLoginWithValidCredentials()
    {
        // Create a mock authenticated user
        $user = new User(); // Make sure to import the user model
        $user->id = 1;
        $user->name = 'John Doe';
        $user->email = 'john@example.com';
        $user->password = bcrypt('password'); // Password needs to be hashed

        // Mock the Auth facade to return the authenticated user
        Auth::shouldReceive('attempt')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        // Instantiate the AuthController
        $controller = new AuthController();

        // Create a request object with test credentials
        $request = new Request(['email' => 'john@example.com', 'password' => 'password']);

        // Call the login method of the controller
        $response = $controller->login($request);

        // Check if the response is an instance of JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Check if the response contains an access token
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('access_token', $responseData);
    }

    public function testLogoutSuccessfully(): void
    {
        // Instantiate the AuthController
        $controller = new AuthController();

        // Create a mock request object
        $request = $this->createMock(Request::class);

        // Create a mock authenticated user
        $user = $this->createMock(User::class);

        // Mock the user method to return the authenticated user
        $request->expects($this->once())
            ->method('user')
            ->willReturn($user);

        // Create a mock for PersonalAccessToken
        $token = $this->createMock(PersonalAccessToken::class);

        // Mock the tokens method to return the fake token
        $user->expects($this->once())
            ->method('tokens')
            ->willReturn($token);

        // Mock the delete method for tokens
        $token->expects($this->once())
            ->method('delete');

        // Call the logout method of the controller
        $response = $controller->logout($request);

        // Check if the response is an instance of JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Check if the response contains the message 'Successfully logged out'
        $responseData = $response->getData(true);
        $this->assertEquals('Successfully logged out', $responseData['message']);
    }

    public function testRefreshToken(): void
    {
        // Instantiate the AuthController
        $controller = new AuthController();

        // Create a real authenticated user
        $user = User::factory()->create();

        // Create a request object with the authenticated user
        $request = new Request();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Call the refresh method of the controller
        $response = $controller->refresh($request);

        // Check if the response is an instance of JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Check if the response contains the new access token
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('access_token', $responseData);
    }

    public function testUserMethodReturnsJsonResponseWithUserDetails(): void
    {
        // Instantiate the AuthController
        $controller = new AuthController();

        // Create a mock request object
        $request = $this->createMock(Request::class);

        // Create an authenticated user to simulate the request
        $user = User::factory()->make(); // Use User::factory()->make() to create a fake user
        $request->expects($this->once())
            ->method('user')
            ->willReturn($user);

        // Call the controller method
        $response = $controller->user($request);

        // Check if the response is an instance of JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Check if the user data is present in the response
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('email', $responseData);

        // Check if the user details in the response match the simulated user's details
        $this->assertEquals($user->name, $responseData['name']);
        $this->assertEquals($user->email, $responseData['email']);
    }

    public function testCheckMethodReturnsJsonResponseWithCorrectValue(): void
    {
        // Instantiate the AuthController
        $controller = new AuthController();

        // Create a mock request object
        $request = $this->createMock(Request::class);

        // Set up the mock to return an authenticated user
        $request->expects($this->any())
            ->method('user')
            ->willReturnOnConsecutiveCalls(new User(), null);

        // Call the controller method and check if it returns true
        $response = $controller->check($request);
        $responseData = $response->getData(true);
        $this->assertTrue($responseData);

        // Call the controller method again and check if it returns false
        $response = $controller->check($request);
        $responseData = $response->getData(true);
        $this->assertFalse($responseData);
    }
}
