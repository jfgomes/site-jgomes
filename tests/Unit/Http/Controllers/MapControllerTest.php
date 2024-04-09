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

    /*public function testTestMapCaches()
    {
        // Arrange
        $controller = new MapController();
        $request = new Request();
        $request->merge(['level' => 'districts', 'options' => 'test']);

        // Mock APCUIterator
        $apcuIteratorMock = \Mockery::mock(\APCUIterator::class);
        $apcuIteratorMock->shouldReceive('rewind')->andReturnSelf();
        $apcuIteratorMock->shouldReceive('valid')->andReturn(false);
        $apcuIteratorMock->shouldReceive('next')->andReturnNull();
        $apcuIteratorMock->shouldReceive('current')->andReturnNull();
        $apcuIteratorMock->shouldReceive('key')->andReturnNull();
        $apcuIteratorMock->shouldReceive('offsetGet')->andReturnNull();
        $apcuIteratorMock->shouldReceive('offsetExists')->andReturn(false);
        $apcuIteratorMock->shouldReceive('getTotalHits')->andReturn(0);
        $apcuIteratorMock->shouldReceive('getTotalSize')->andReturn(0);

        $this->app->instance(\APCUIterator::class, $apcuIteratorMock);

        // Mock Redis
        $redisMock = \Mockery::mock(\Illuminate\Redis\Connections\PhpRedisConnection::class);
        $redisMock->shouldReceive('ping')->andReturn(true);
        $redisMock->shouldReceive('keys')->andReturn(['location_pt_district_test_1']);
        $redisMock->shouldReceive('get')->andReturn('{"district_name":"Test District","district_code":"1"}');
        $redisMock->shouldReceive('select')->with(2)->andReturnSelf();
        $this->app->instance('redis', $redisMock);

        // Mock DB
        $dbResult = [['district_name' => 'Test District', 'district_code' => '1']];
        DB::shouldReceive('table->select->distinct->where->orderBy->get->toArray')->andReturn($dbResult);

        // Act
        $response = $controller->testMapCaches($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('result', $responseData);
        $this->assertArrayHasKey('locations', $responseData['result']);
        $this->assertEquals(['district_name' => 'Test District', 'district_code' => '1'], json_decode($responseData['result']['locations'][0], true));
        $this->assertEquals('APCu', $responseData['result']['source']);
    } */
}
