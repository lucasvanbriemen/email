<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set a test token for the testing environment
        config(['app.user_token' => 'test_token']);
        
        // Mock the external authentication API call
        Http::fake([
            'login.lucasvanbriemen.nl/api/user/token/*' => Http::response([
                'user' => [
                    'id' => 1,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'profile_id' => 1,
                    'last_activity' => now()->subHours(1)->toDateTimeString(),
                ]
            ], 200),
        ]);
        
        // Bind a default current_user for tests
        app()->singleton('current_user', function () {
            return (object) [
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'profile_id' => 1,
                'last_activity' => now()->subHours(1)->toDateTimeString(),
            ];
        });
    }
}
