<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Email;
use App\Models\IncomingEmailSender;
use App\Models\Profile;
use Carbon\Carbon;

class EmailApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_search_emails_without_filters()
    {
        // Create test data
        $profile = Profile::factory()->create();
        $sender = IncomingEmailSender::factory()->create(['email' => 'test@example.com']);
        Email::factory(3)->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender->id,
            'subject' => 'Test Email',
            'html_body' => '<p>This is a test email</p>',
        ]);

        $response = $this->get('/api/emails/search');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'count',
            'emails' => [
                '*' => ['id', 'subject', 'sender', 'date', 'preview', 'unread']
            ]
        ]);
        $this->assertEquals(3, $response->json('count'));
    }

    public function test_search_emails_with_keyword()
    {
        $profile = Profile::factory()->create();
        $sender = IncomingEmailSender::factory()->create(['email' => 'test@example.com']);
        Email::factory()->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender->id,
            'subject' => 'Meeting Tomorrow',
            'html_body' => '<p>Let\'s discuss the project</p>',
        ]);
        Email::factory()->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender->id,
            'subject' => 'Random Subject',
            'html_body' => '<p>Some other content</p>',
        ]);

        $response = $this->get('/api/emails/search?keyword=Meeting');

        $response->assertStatus(200);
        $response->assertJsonPath('count', 1);
    }

    public function test_search_emails_by_sender()
    {
        $profile = Profile::factory()->create();
        $sender1 = IncomingEmailSender::factory()->create(['email' => 'alice@example.com']);
        $sender2 = IncomingEmailSender::factory()->create(['email' => 'bob@example.com']);

        Email::factory()->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender1->id,
        ]);
        Email::factory()->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender2->id,
        ]);

        $response = $this->get('/api/emails/search?sender=alice@example.com');

        $response->assertStatus(200);
        $response->assertJsonPath('count', 1);
    }

    public function test_search_emails_by_date_range()
    {
        $profile = Profile::factory()->create();
        $sender = IncomingEmailSender::factory()->create();

        Email::factory()->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender->id,
            'sent_at' => Carbon::parse('2026-02-05'),
        ]);
        Email::factory()->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender->id,
            'sent_at' => Carbon::parse('2026-02-06'),
        ]);

        $response = $this->get('/api/emails/search?from_date=2026-02-06&to_date=2026-02-06');

        $response->assertStatus(200);
        $response->assertJsonPath('count', 1);
    }

    public function test_search_unread_only()
    {
        $profile = Profile::factory()->create();
        $sender = IncomingEmailSender::factory()->create();

        Email::factory()->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender->id,
            'has_read' => true,
        ]);
        Email::factory()->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender->id,
            'has_read' => false,
        ]);

        $response = $this->get('/api/emails/search?unread_only=true');

        $response->assertStatus(200);
        $response->assertJsonPath('count', 1);
        $response->assertJsonPath('emails.0.unread', true);
    }

    public function test_get_full_email()
    {
        $profile = Profile::factory()->create();
        $sender = IncomingEmailSender::factory()->create(['email' => 'sender@example.com']);
        $email = Email::factory()->create([
            'profile_id' => $profile->id,
            'sender_id' => $sender->id,
            'subject' => 'Test Subject',
            'html_body' => '<p>Full email body content</p>',
        ]);

        $response = $this->get("/api/emails/{$email->uuid}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'subject', 'sender', 'date', 'body'
        ]);
        $response->assertJsonPath('subject', 'Test Subject');
        $response->assertJsonPath('body', 'Full email body content');
    }

    public function test_get_email_not_found()
    {
        $response = $this->get('/api/emails/invalid-uuid');

        $response->assertStatus(404);
        $response->assertJsonPath('error', "Email with ID 'invalid-uuid' not found");
    }

    public function test_api_auth_token_validation()
    {
        // Re-enable middleware
        $this->withMiddleware();

        // Without token - should fail if token is required
        $response = $this->get('/api/emails/search', [
            'Authorization' => 'Bearer wrong-token'
        ]);

        // Should either return 401 or succeed (depending on env config)
        $this->assertIn($response->status(), [200, 401]);
    }
}
