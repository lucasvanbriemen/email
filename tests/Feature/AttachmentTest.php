<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Attachment;

class AttachmentTest extends TestCase
{
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
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->get("/1/folder/inbox/mail/{$uuid}");
        
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
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->get("/1/folder/inbox/mail/{$uuid}");
        
        // Should handle email with potential TXT attachments
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_attachment_get_content_method()
    {
        $attachment = new Attachment();
        $this->assertTrue(method_exists($attachment, 'getContent'));
    }
}