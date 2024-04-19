<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Artisan;

class MaintenanceControllerTest extends TestCase
{
    public function testDeactivateMaintenanceMode()
    {
        // Arrange
        $controller = new MaintenanceController();
        Artisan::shouldReceive('call')->with('down')->once();

        // Act
        $response = $controller->deactivate();

        // Assert
        $this->assertEquals('Site is deactivated. Maintenance mode activated. Users will <strong>NOT</strong> be able to access the site.', $response);
    }

    public function testActivateMaintenanceMode()
    {
        // Arrange
        $controller = new MaintenanceController();
        Artisan::shouldReceive('call')->with('up')->once();

        // Act
        $response = $controller->activate();

        // Assert
        $this->assertEquals('Site is activated. Maintenance mode <strong>NOT</strong> activated. Users will be able to access the site.', $response);
    }
}
