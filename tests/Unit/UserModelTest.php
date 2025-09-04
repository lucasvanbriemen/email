<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserModelTest extends TestCase
{
    public function test_user_model_exists()
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Authenticatable::class, $user);
    }

    public function test_user_has_factory()
    {
        $this->assertTrue(method_exists(User::class, 'factory'));
    }

    public function test_user_has_notifiable_trait()
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'notify'));
    }

    public function test_user_has_fillable_attributes()
    {
        $user = new User();
        $fillable = $user->getFillable();
        
        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
    }

    public function test_user_has_hidden_attributes()
    {
        $user = new User();
        $hidden = $user->getHidden();
        
        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    public function test_user_has_cast_attributes()
    {
        $user = new User();
        $casts = $user->getCasts();
        
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
    }
}