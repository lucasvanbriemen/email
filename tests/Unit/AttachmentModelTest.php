<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Attachment;
use App\Models\Email;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachmentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_attachment_model_exists()
    {
        $attachment = new Attachment();
        $this->assertInstanceOf(Attachment::class, $attachment);
    }

    public function test_attachment_has_fillable_attributes()
    {
        $attachment = new Attachment();
        $expected = ['email_id', 'name', 'path', 'mime_type'];
        
        $this->assertEquals($expected, $attachment->getFillable());
    }

    public function test_attachment_belongs_to_email()
    {
        // Test that the relationship method exists
        $attachment = new Attachment();
        $this->assertTrue(method_exists($attachment, 'email'));
    }

    public function test_attachment_get_content_method_exists()
    {
        $attachment = new Attachment();
        $this->assertTrue(method_exists($attachment, 'getContent'));
    }
}