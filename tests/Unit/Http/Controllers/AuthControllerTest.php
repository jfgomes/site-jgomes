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
        // Crie um usuário mock autenticado
        $user = new User(); // Certifique-se de importar o modelo de usuário
        $user->id = 1;
        $user->name = 'John Doe';
        $user->email = 'john@example.com';
        $user->password = bcrypt('password'); // A senha precisa ser criptografada

        // Mock do facade Auth para retornar o usuário autenticado
        Auth::shouldReceive('attempt')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        // Instancie o controlador AuthController
        $controller = new AuthController();

        // Crie um objeto de solicitação com as credenciais de teste
        $request = new Request(['email' => 'john@example.com', 'password' => 'password']);

        // Chame o método de login do controlador
        $response = $controller->login($request);

        // Verifique se a resposta é uma instância de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verifique se a resposta contém um token de acesso
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('access_token', $responseData);
    }

    public function testLogoutSuccessfully(): void
    {
        // Crie uma instância do controlador AuthController
        $controller = new AuthController();

        // Crie um mock do objeto de solicitação
        $request = $this->createMock(Request::class);

        // Crie um mock de usuário autenticado
        $user = $this->createMock(User::class);

        // Mock do método user para retornar o usuário autenticado
        $request->expects($this->once())
            ->method('user')
            ->willReturn($user);

        // Crie um mock para PersonalAccessToken
        $token = $this->createMock(PersonalAccessToken::class);

        // Mock do método tokens para retornar o token falso
        $user->expects($this->once())
            ->method('tokens')
            ->willReturn($token);

        // Mock do método delete para os tokens
        $token->expects($this->once())
            ->method('delete');

        // Chame o método de logout do controlador
        $response = $controller->logout($request);

        // Verifique se a resposta é uma instância de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verifique se a resposta contém a mensagem 'Successfully logged out'
        $responseData = $response->getData(true);
        $this->assertEquals('Successfully logged out', $responseData['message']);
    }

    public function testRefreshToken(): void
    {
        // Crie uma instância do controlador AuthController
        $controller = new AuthController();

        // Crie um usuário autenticado real
        $user = User::factory()->create();

        // Crie um objeto de solicitação com o usuário autenticado
        $request = new Request();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Chame o método de refresh do controlador
        $response = $controller->refresh($request);

        // Verifique se a resposta é uma instância de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verifique se a resposta contém o novo token de acesso
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('access_token', $responseData);
    }

    public function testUserMethodReturnsJsonResponseWithUserDetails(): void
    {
        // Crie uma instância do controlador AuthController
        $controller = new AuthController();

        // Crie um mock do objeto de solicitação
        $request = $this->createMock(Request::class);

        // Crie um usuário autenticado para simular a solicitação
        $user = User::factory()->make(); // Use User::factory()->make() para criar um usuário fictício
        $request->expects($this->once())
            ->method('user')
            ->willReturn($user);

        // Chame o método do controlador
        $response = $controller->user($request);

        // Verifique se a resposta é uma instância de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verifique se os dados do usuário estão presentes na resposta
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('email', $responseData);

        // Verifique se os detalhes do usuário na resposta correspondem aos do usuário simulado
        $this->assertEquals($user->name, $responseData['name']);
        $this->assertEquals($user->email, $responseData['email']);
    }

    public function testCheckMethodReturnsJsonResponseWithCorrectValue(): void
    {
        // Crie uma instância do controlador AuthController
        $controller = new AuthController();

        // Crie um mock do objeto de solicitação
        $request = $this->createMock(Request::class);

        // Configure o mock para retornar um usuário autenticado
        $request->expects($this->any())
            ->method('user')
            ->willReturnOnConsecutiveCalls(new User(), null);

        // Chame o método do controlador e verifique se retorna verdadeiro
        $response = $controller->check($request);
        $responseData = $response->getData(true);
        $this->assertTrue($responseData);

        // Chame o método do controlador novamente e verifique se retorna falso
        $response = $controller->check($request);
        $responseData = $response->getData(true);
        $this->assertFalse($responseData);
    }
}
