<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User; // Importe o modelo de usuário, se necessário
use Laravel\Sanctum\Sanctum;

class HomeControllerTest extends TestCase
{

    use RefreshDatabase; // Isso irá recriar o banco de dados antes de cada teste

    /**
     * Testa se o método index retorna uma instância de JsonResponse.
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

  /*  public function testIndexReturnsJsonWithUser()
    {
        // Crie uma instância do controlador HomeController
        $controller = new HomeController();

        // Crie um usuário autenticado para simular a autenticação
        $user = \App\Models\User::factory()->create();

        // Autentique o usuário usando Sanctum
        Sanctum::actingAs($user);

        // Crie uma solicitação vazia
        $request = new Request();

        // Chame o método de index do controlador com a solicitação autenticada
        $response = $controller->index($request);

        // Verifique se a resposta é uma instância de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verifique se a resposta contém o resultado esperado
        $responseData = $response->getData(true);
        var_dump($responseData); // Adicionando esta linha para imprimir os dados da resposta

        // Verifique se a chave 'result' existe no array de resposta
        $this->assertArrayHasKey('result', $responseData);

        // Verifique se a chave 'user' existe dentro de 'result'
        if (isset($responseData['result']['user'])) {
            // Verifique os atributos do usuário retornados na resposta
            $this->assertEquals($user->id, $responseData['result']['user']['id']);
            $this->assertEquals($user->name, $responseData['result']['user']['name']);
            $this->assertEquals($user->email, $responseData['result']['user']['email']);
        } else {
            $this->fail('The "user" key was not found in the response data.');
        }
    }*/
}
