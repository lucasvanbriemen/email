<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\SmtpCredentials;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SmtpCredentialsModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_smtp_credentials_model_exists()
    {
        $credentials = new SmtpCredentials();
        $this->assertInstanceOf(SmtpCredentials::class, $credentials);
    }

    public function test_smtp_credentials_has_fillable_attributes()
    {
        $credentials = new SmtpCredentials();
        $fillable = $credentials->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertContains('profile_id', $fillable);
        $this->assertContains('host', $fillable);
        $this->assertContains('port', $fillable);
        $this->assertContains('username', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('reply_to_name', $fillable);
        $this->assertContains('reply_to_email', $fillable);
        $this->assertContains('from_name', $fillable);
        $this->assertContains('from_email', $fillable);
    }

    public function test_smtp_credentials_model_structure()
    {
        // Test model structure
        $credentials = new SmtpCredentials();
        $this->assertInstanceOf(SmtpCredentials::class, $credentials);
    }
}