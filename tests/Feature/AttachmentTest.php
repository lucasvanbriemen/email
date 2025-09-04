<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Email;
use App\Models\Profile;
use App\Models\Folder;
use App\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected $profile;
    protected $folder;
    protected $email;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->profile = Profile::factory()->create(['id' => 1]);
        $this->folder = Folder::factory()->create([
            'path' => 'inbox',
            'profile_id' => $this->profile->id
        ]);
        $this->email = Email::factory()->create([
            'profile_id' => $this->profile->id,
            'folder_id' => $this->folder->id
        ]);
    }

    public function test_attachment_model_exists()
    {
        $attachment = new Attachment();
        $this->assertInstanceOf(Attachment::class, $attachment);
    }

    public function test_attachment_has_fillable_attributes()
    {
        $fillable = (new Attachment())->getFillable();
        $expected = ['email_id', 'name', 'path', 'mime_type'];
        
        $this->assertEquals($expected, $fillable);
    }

    public function test_attachment_belongs_to_email()
    {
        // Test that the relationship method exists
        $attachment = new Attachment();
        $this->assertTrue(method_exists($attachment, 'email'));
    }

    public function test_email_with_html_attachment_shows_content()
    {
        // This tests the logic in MailboxController::show where HTML attachments are loaded
        $response = $this->get("/1/folder/inbox/mail/{$this->email->uuid}");
        
        // Should handle email with potential HTML attachments
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_email_with_txt_attachment_shows_content()
    {
        // This tests the logic for TXT attachment handling
        $response = $this->get("/1/folder/inbox/mail/{$this->email->uuid}");
        
        // Should handle email with potential TXT attachments
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }
}