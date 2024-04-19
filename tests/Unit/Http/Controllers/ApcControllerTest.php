<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ApcController;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Tests\TestCase;

class ApcControllerTest extends TestCase
{
    public function testIndexReturnsView(): void
    {
        // Arrange
        $controller = new ApcController();
        $request = new Request();

        // Act
        $response = $controller->index($request);

        // Assert
        $this->assertInstanceOf(View::class, $response);
    }

    public function testIndexReturnsViewWithData(): void
    {
        // Arrange
        $controller = new ApcController();
        $request = new Request([
            'SCOPE' => 'A',
            'SORT1' => 'H',
            'SORT2' => 'D',
            'COUNT' => 20,
            'OB' => 1,
        ]);

        // Act
        $response = $controller->index($request);

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertArrayHasKey('SCOPE', $response->getData());
        $this->assertArrayHasKey('SORT1', $response->getData());
        $this->assertArrayHasKey('SORT2', $response->getData());
        $this->assertArrayHasKey('COUNT', $response->getData());
        $this->assertArrayHasKey('OB', $response->getData());
        $this->assertEquals('A', $response->getData()['SCOPE']);
        $this->assertEquals('H', $response->getData()['SORT1']);
        $this->assertEquals('D', $response->getData()['SORT2']);
        $this->assertEquals(20, $response->getData()['COUNT']);
        $this->assertEquals(1, $response->getData()['OB']);
    }
}
