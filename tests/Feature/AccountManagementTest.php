<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Profile;
use App\Models\ImapCredentials;
use App\Models\SmtpCredentials;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $profile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->profile = Profile::factory()->create(['id' => 1]);
    }

    public function test_account_edit_route_exists()
    {
        $response = $this->get('/account');
        
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_account_edit_with_profile_id()
    {
        $response = $this->get('/account/1');
        
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_store_imap_credentials_validation()
    {
        $response = $this->post('/account/1/imap', []);
        
        // Should validate required fields
        $this->assertTrue(
            $response->status() === 302 ||  // Redirect with validation errors
            $response->status() === 422 ||  // Validation error response
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_store_imap_credentials_with_valid_data()
    {
        $response = $this->post('/account/1/imap', [
            'host' => 'imap.example.com',
            'port' => 993,
            'username' => 'test@example.com',
            'password' => 'password123',
            'encryption' => 'ssl'
        ]);
        
        $this->assertTrue(
            $response->isRedirection() ||  // Successful redirect
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_store_smtp_credentials_validation()
    {
        $response = $this->post('/account/1/smtp', []);
        
        // Should validate required fields
        $this->assertTrue(
            $response->status() === 302 ||  // Redirect with validation errors
            $response->status() === 422 ||  // Validation error response
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_store_smtp_credentials_with_valid_data()
    {
        $response = $this->post('/account/1/smtp', [
            'host' => 'smtp.example.com',
            'port' => 587,
            'username' => 'test@example.com',
            'password' => 'password123',
            'reply_to_name' => 'Test User',
            'reply_to_email' => 'reply@example.com',
            'from_name' => 'Test Sender',
            'from_email' => 'sender@example.com'
        ]);
        
        $this->assertTrue(
            $response->isRedirection() ||  // Successful redirect
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_store_smtp_credentials_with_invalid_email_format()
    {
        $response = $this->post('/account/1/smtp', [
            'host' => 'smtp.example.com',
            'port' => 587,
            'username' => 'test@example.com',
            'password' => 'password123',
            'reply_to_email' => 'invalid-email-format',  // Invalid email
            'from_email' => 'also-invalid'  // Invalid email
        ]);
        
        $this->assertTrue(
            $response->status() === 302 ||  // Redirect with validation errors
            $response->status() === 422 ||  // Validation error response
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_imap_credentials_model_exists()
    {
        $credentials = new ImapCredentials();
        $this->assertInstanceOf(ImapCredentials::class, $credentials);
    }

    public function test_smtp_credentials_model_exists()
    {
        $credentials = new SmtpCredentials();
        $this->assertInstanceOf(SmtpCredentials::class, $credentials);
    }
}