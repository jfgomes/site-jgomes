<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Http\Controllers\MessagesController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Jobs\MessagesJob;
use App\Models\Messages;
use Illuminate\Support\Facades\Validator;

class MessagesControllerTest extends TestCase
{
    public function tesSendSuccess()
    {
        // Arrange
        $messagesModelMock = \Mockery::mock(Messages::class);
        $messagesModelMock->shouldReceive('validateData')->andReturn(true);
        $controller = new MessagesController($messagesModelMock);
        $request = new Request([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'content' => 'Test Content'
        ]);
        $jobMock = \Mockery::mock(MessagesJob::class);
        $this->mock(MessagesJob::class, function ($mock) use ($jobMock) {
            $mock->shouldReceive('dispatch')->withArgs([$jobMock])->once();
        });

        // Act
        $response = $controller->send($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(['success-site'], $responseData);
    }

    public function tesSendValidationFailed()
    {
        // Arrange
        $validationErrors = '{"email":["The email field is required."]}';
        $messagesModelMock = \Mockery::mock(Messages::class);
        $messagesModelMock->shouldReceive('validateData')->andReturn($validationErrors);
        $controller = new MessagesController($messagesModelMock);
        $request = new Request([
            'name' => 'John Doe',
            'subject' => 'Test Subject',
            'content' => 'Test Content'
        ]);

        // Act
        $response = $controller->send($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => $validationErrors], $responseData);
    }

    public function tesSendException()
    {
        // Arrange
        $messagesModelMock = \Mockery::mock(Messages::class);
        $messagesModelMock->shouldReceive('validateData')->andThrow(new \Exception('Test Exception'));
        $controller = new MessagesController($messagesModelMock);
        $request = new Request([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'content' => 'Test Content'
        ]);

        // Expecting the error method to be called once and return null
        Log::shouldReceive('error')->once()->andReturnNull();

        // Act
        $response = $controller->send($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => 'Test Exception'], $responseData);
    }





    public function testValidateData()
    {
        // Arrange
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'content' => 'Test Content'
        ];
        $validatorMock = \Mockery::mock(\Illuminate\Validation\Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(false);
        $messagesModelMock = \Mockery::mock(Messages::class);
        $messagesModelMock->shouldReceive('validateData')->andReturn($validatorMock);
        $controller = new MessagesController($messagesModelMock);

        // Act
        $result = $controller->validateData($data);

        // Assert
        $this->assertTrue($result);
    }

    public function testValidateDataWithValidationErrors()
    {
        // Arrange
        $data = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'subject' => 'Test Subject',
            'content' => 'Test Content'
        ];
        $validatorMock = \Mockery::mock(\Illuminate\Validation\Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(true);
        $validatorMock->shouldReceive('errors')->andReturn(collect(['email' => ['The email field is invalid.']]));
        $messagesModelMock = \Mockery::mock(Messages::class);
        $messagesModelMock->shouldReceive('validateData')->andReturn($validatorMock);
        $controller = new MessagesController($messagesModelMock);

        // Act
        $result = $controller->validateData($data);

        // Assert
        $this->assertEquals('{"email":["The email field is invalid."]}', $result);
    }

    public function testPrepareMessage()
    {
        // Arrange
        $request = new Request([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'content' => 'Test Content'
        ]);
        $controller = new MessagesController(new Messages());

        // Act
        $result = $controller->prepareMessage($request);

        // Assert
        $expected = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'content' => 'Test Content'
        ];
        $this->assertEquals($expected, $result);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }
}
