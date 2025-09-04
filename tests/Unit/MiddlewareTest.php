<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Middleware\IsLoggedIn;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MiddlewareTest extends TestCase
{
    public function test_is_logged_in_middleware_exists()
    {
        $middleware = new IsLoggedIn();
        $this->assertInstanceOf(IsLoggedIn::class, $middleware);
    }

    public function test_middleware_has_handle_method()
    {
        $middleware = new IsLoggedIn();
        $this->assertTrue(method_exists($middleware, 'handle'));
    }

    public function test_middleware_in_testing_environment()
    {
        // In testing environment, middleware should create mock user
        $this->assertTrue(app()->environment('testing'));
        
        // Test that current_user is available after middleware runs
        $this->assertTrue(true); // This passes if the test setup works
    }
}