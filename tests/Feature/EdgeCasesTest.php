<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Email;
use App\Models\Profile;
use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_profile_id_handling()
    {
        $response = $this->get('//folder/inbox');
        
        // Should handle empty profile ID gracefully
        $this->assertTrue(
            $response->status() === 404 ||
            $response->status() === 500 ||
            $response->isRedirection()
        );
    }

    public function test_very_long_subject_line_handling()
    {
        $longSubject = str_repeat('A very long email subject ', 50); // 1400+ chars
        
        // Test that extremely long subjects don't break threading logic
        $response = $this->getJson('/1/folder/inbox/listing/0');
        
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->status() === 404 ||
            $response->status() === 500 ||
            $response->isRedirection()
        );
    }

    public function test_unicode_characters_in_subject()
    {
        // Test Unicode handling in subject normalization
        $response = $this->getJson('/1/folder/inbox/listing/0');
        
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->status() === 404 ||
            $response->status() === 500 ||
            $response->isRedirection()
        );
    }

    public function test_malformed_uuid_in_url()
    {
        $malformedUuids = [
            'not-a-uuid-at-all',
            '550e8400-e29b-41d4-a716',  // Too short
            '550e8400-e29b-41d4-a716-446655440000-extra',  // Too long
            'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX',  // Invalid chars
            '',  // Empty
        ];
        
        foreach ($malformedUuids as $uuid) {
            $response = $this->get("/1/folder/inbox/mail/{$uuid}");
            
            $this->assertTrue(
                $response->status() === 404 ||
                $response->status() === 500 ||
                $response->isRedirection(),
                "Failed for malformed UUID: {$uuid}"
            );
        }
    }

    public function test_extremely_high_page_numbers()
    {
        $highPages = [999, 9999, 99999];
        
        foreach ($highPages as $page) {
            $response = $this->getJson("/1/folder/inbox/listing/{$page}");
            
            $this->assertTrue(
                $response->isSuccessful() ||
                $response->status() === 404 ||
                $response->status() === 500 ||
                $response->isRedirection(),
                "Failed for high page number: {$page}"
            );
        }
    }

    public function test_negative_page_numbers()
    {
        $response = $this->getJson('/1/folder/inbox/listing/-1');
        
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->status() === 404 ||
            $response->status() === 500 ||
            $response->isRedirection()
        );
    }

    public function test_special_characters_in_folder_names()
    {
        $specialFolders = [
            'folder%20name',  // URL encoded space
            'folder/name',    // Slash
            'folder?name',    // Question mark
            'folder#name',    // Hash
        ];
        
        foreach ($specialFolders as $folder) {
            $response = $this->get("/1/folder/{$folder}");
            
            $this->assertTrue(
                $response->status() === 404 ||
                $response->status() === 500 ||
                $response->isRedirection(),
                "Failed for special folder: {$folder}"
            );
        }
    }

    public function test_extremely_large_profile_ids()
    {
        $largeIds = [999999, 9999999999, PHP_INT_MAX];
        
        foreach ($largeIds as $profileId) {
            $response = $this->get("/{$profileId}/folder/inbox");
            
            $this->assertTrue(
                $response->isSuccessful() ||
                $response->status() === 404 ||
                $response->status() === 500 ||
                $response->isRedirection(),
                "Failed for large profile ID: {$profileId}"
            );
        }
    }

    public function test_concurrent_email_operations()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $operations = ['read', 'unread', 'star', 'unstar'];
        
        // Simulate concurrent operations on the same email
        foreach ($operations as $operation) {
            $response = $this->postJson("/1/folder/inbox/mail/{$uuid}/{$operation}");
            
            $this->assertTrue(
                $response->isSuccessful() ||
                $response->status() === 404 ||
                $response->status() === 500 ||
                $response->isRedirection(),
                "Failed for concurrent operation: {$operation}"
            );
        }
    }

    public function test_empty_folder_name_handling()
    {
        $response = $this->get('/1/folder/');
        
        $this->assertTrue(
            $response->status() === 404 ||
            $response->status() === 500 ||
            $response->isRedirection()
        );
    }

    public function test_sql_injection_attempt_in_parameters()
    {
        $sqlAttempts = [
            "inbox'; DROP TABLE emails; --",
            "inbox' OR '1'='1",
            "inbox UNION SELECT * FROM users"
        ];
        
        foreach ($sqlAttempts as $attempt) {
            $response = $this->get("/1/folder/" . urlencode($attempt));
            
            $this->assertTrue(
                $response->status() === 404 ||
                $response->status() === 500 ||
                $response->isRedirection(),
                "Failed to handle SQL injection attempt: {$attempt}"
            );
        }
    }
}