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
        // Crie uma instância do controlador LocationsController
        $controller = new LocationsController();

        // Crie uma solicitação vazia
        $request = new Request();

        // Chame o método de index do controlador
        $response = $controller->index($request);

        // Verifique se a resposta é uma instância de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verifique se a resposta contém a chave 'result'
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('result', $responseData);

        // Verifique se a chave 'user' está presente em 'result'
        $this->assertArrayHasKey('user', $responseData['result']);
    }

  /*  public function testGetLocationsReturnsJson()
    {
        // Crie uma instância do controlador LocationsController
        $controller = new LocationsController();

        // Crie uma solicitação com parâmetros level e options
        $request = new Request([
            'level' => 'district',
            'options' => 'some_code'
        ]);

        // Substitua a chamada para APCUIterator por um array vazio
        $apcuIterator = function ($keyPattern) {
            return [];
        };

        // Substitua a chamada para a função apcu_store por uma função vazia
        $apcuStore = function ($key, $value) {
            // Faz nada
        };

        // Substitua a função Redis::get por uma que retorna null
        Redis::shouldReceive('get')->andReturn(null);

        // Chame o método getLocations do controlador
        $response = $controller->getLocations($request);

        // Verifique se a resposta é uma instância de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verifique se a resposta contém a chave 'result'
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('result', $responseData);

        // Verifique se a chave 'locations' está presente em 'result'
        $this->assertArrayHasKey('locations', $responseData['result']);

        // Verifique se a chave 'user' está presente em 'result'
        $this->assertArrayHasKey('user', $responseData['result']);
    }*/
}
