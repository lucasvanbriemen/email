<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\ImapCredentials;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImapCredentialsModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_imap_credentials_model_exists()
    {
        $credentials = new ImapCredentials();
        $this->assertInstanceOf(ImapCredentials::class, $credentials);
    }

    public function test_imap_credentials_has_fillable_attributes()
    {
        $credentials = new ImapCredentials();
        $fillable = $credentials->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertContains('profile_id', $fillable);
        $this->assertContains('host', $fillable);
        $this->assertContains('port', $fillable);
        $this->assertContains('username', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('encryption', $fillable);
    }

    public function test_imap_credentials_model_structure()
    {
        // Test model structure
        $credentials = new ImapCredentials();
        $this->assertInstanceOf(ImapCredentials::class, $credentials);
    }
}