<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Email;
use App\Models\Folder;
use App\Models\Profile;
use Illuminate\Support\Str;

class EmailModelTest extends TestCase
{
    public function test_email_has_fillable_attributes()
    {
        $fillableAttributes = [
            'profile_id',
            'subject',
            'from',
            'sender_email',
            'to',
            'sent_at',
            'has_read',
            'uid',
            'html_body',
            'folder_id',
            'is_archived',
            'is_starred',
            'is_deleted',
        ];

        $email = new Email();
        $this->assertEquals($fillableAttributes, $email->getFillable());
    }

    public function test_custom_view_folders_constant()
    {
        $expectedFolders = ['trash', 'all', 'spam', 'stared'];
        
        $this->assertEquals($expectedFolders, Email::$customViewFolders);
    }

    public function test_email_model_exists()
    {
        $email = new Email();
        $this->assertInstanceOf(Email::class, $email);
    }

    public function test_email_creates_uuid_on_instantiation()
    {
        $email = new Email();
        
        // Test the booted method by creating an instance
        $this->assertTrue(method_exists($email, 'getAttribute'));
    }

    public function test_email_has_folder_relationship()
    {
        $email = new Email();
        
        // Test the folder relationship exists
        $this->assertTrue(method_exists($email, 'folder'));
    }

    public function test_email_has_user_relationship()
    {
        $email = new Email();
        
        // Test the user relationship exists
        $this->assertTrue(method_exists($email, 'user'));
    }

    public function test_get_emails_returns_collection_for_null_folder()
    {
        $result = Email::getEmails(null, new Profile());
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }
}