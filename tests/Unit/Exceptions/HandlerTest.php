<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\Handler;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;
use Throwable;

class HandlerTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testRenderReturns404ViewForHttpExceptionWithStatusCode404()
    {
        // Arrange
        $handler = new Handler($this->app);
        $request = Request::create('/');
        $exception = new HttpException(404);

        // Act
        $response = $handler->render($request, $exception);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue(View::exists('errors.404'));
    }

    /**
     * @throws Throwable
     */
    public function testRenderReturns500ViewForHttpExceptionWithStatusCode500()
    {
        // Arrange
        $handler = new Handler($this->app);
        $request = Request::create('/');
        $exception = new HttpException(500);

        // Act
        $response = $handler->render($request, $exception);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertTrue(View::exists('errors.500'));
    }

    /**
     * @throws Throwable
     */
    public function testRenderReturns429ViewForHttpExceptionWithStatusCode429()
    {
        // Arrange
        $handler = new Handler($this->app);
        $request = Request::create('/');
        $exception = new HttpException(429);

        // Act
        $response = $handler->render($request, $exception);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(429, $response->getStatusCode());
        $this->assertTrue(View::exists('errors.429'));
    }

    /**
     * @throws Throwable
     */
   /* public function testRenderCallsParentRenderForNonHttpException()
    {
        // Arrange
        $handler = new Handler($this->app);
        $request = Request::create('/');
        $exception = new \Exception();

        // Act
        $response = $handler->render($request, $exception);

        // Assert
        $this->assertNull($response); // Parent render should be called for non-HttpException
    } */
}
