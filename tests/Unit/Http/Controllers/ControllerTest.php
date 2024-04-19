<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Controller;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    /**
     * Checks if the Controller class exists.
     *
     * @return void
     */
    public function testControllerClassExists()
    {
        $this->assertTrue(class_exists(Controller::class));
    }

    /**
     * Checks if the Controller class uses the expected traits.
     *
     * @return void
     */
    public function testControllerUsesTraits()
    {
        $traits = class_uses(Controller::class);

        $this->assertContains('Illuminate\Foundation\Auth\Access\AuthorizesRequests', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\DispatchesJobs', $traits);
        $this->assertContains('Illuminate\Foundation\Validation\ValidatesRequests', $traits);
    }

    /**
     * Checks if the Controller class has the expected methods.
     *
     * @return void
     */
    public function testControllerHasExpectedMethods()
    {
        $expectedMethods = [
            'authorize',
            'dispatch',
            'validate',
        ];

        $controller = new Controller();

        foreach ($expectedMethods as $method) {
            $this->assertTrue(method_exists($controller, $method));
        }
    }
}
