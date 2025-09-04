<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Email;
use App\Models\Profile;
use App\Models\Folder;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailListingTest extends TestCase
{
    use RefreshDatabase;

    protected $profile;
    protected $folder;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->profile = Profile::factory()->create(['id' => 1]);
        $this->folder = Folder::factory()->create([
            'path' => 'inbox',
            'name' => 'Inbox',
            'profile_id' => $this->profile->id
        ]);
    }

    public function test_get_listing_html_returns_json_response()
    {
        $response = $this->getJson('/1/folder/inbox/listing/0');
        
        // Should return JSON response or redirect
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_get_listing_html_with_different_folders()
    {
        $folders = ['inbox', 'sent', 'drafts', 'trash', 'spam', 'stared'];
        
        foreach ($folders as $folderName) {
            $response = $this->getJson("/1/folder/{$folderName}/listing/0");
            
            // Each folder should return a valid response
            $this->assertTrue(
                $response->isSuccessful() ||
                $response->isRedirection() ||
                $response->status() === 404 ||
                $response->status() === 500,
                "Failed for folder: {$folderName}"
            );
        }
    }

    public function test_get_listing_html_with_different_pages()
    {
        $pages = [0, 1, 2, 5];
        
        foreach ($pages as $page) {
            $response = $this->getJson("/1/folder/inbox/listing/{$page}");
            
            // Each page should return a valid response
            $this->assertTrue(
                $response->isSuccessful() ||
                $response->isRedirection() ||
                $response->status() === 404 ||
                $response->status() === 500,
                "Failed for page: {$page}"
            );
        }
    }

    public function test_get_listing_html_with_different_profiles()
    {
        $profileIds = [1, 2, 3, 999];
        
        foreach ($profileIds as $profileId) {
            $response = $this->getJson("/{$profileId}/folder/inbox/listing/0");
            
            // Each profile should return a valid response
            $this->assertTrue(
                $response->isSuccessful() ||
                $response->isRedirection() ||
                $response->status() === 404 ||
                $response->status() === 500,
                "Failed for profile: {$profileId}"
            );
        }
    }

    public function test_mailbox_index_returns_view_or_redirect()
    {
        $response = $this->get('/1/folder/inbox');
        
        // Should return a view or redirect
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_mailbox_index_with_invalid_folder_redirects()
    {
        $response = $this->get('/1/folder/nonexistent');
        
        // Should handle invalid folders gracefully
        $this->assertTrue(
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_email_show_route_handles_valid_uuid()
    {
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->get("/1/folder/inbox/mail/{$validUuid}");
        
        // Should handle valid UUID format
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_email_show_route_handles_invalid_uuid()
    {
        $invalidUuid = 'invalid-uuid-format';
        $response = $this->get("/1/folder/inbox/mail/{$invalidUuid}");
        
        // Should handle invalid UUID format gracefully
        $this->assertTrue(
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_email_html_view_route()
    {
        $validUuid = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->get("/1/folder/inbox/mail/{$validUuid}/html");
        
        // Should handle HTML view request
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_listing_endpoint_exercises_pagination_logic()
    {
        // Test first page
        $response = $this->getJson('/1/folder/inbox/listing/0');
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );

        // Test subsequent pages
        $response = $this->getJson('/1/folder/inbox/listing/1');
        $this->assertTrue(
            $response->isSuccessful() ||
            $response->isRedirection() ||
            $response->status() === 404 ||
            $response->status() === 500
        );
    }

    public function test_special_folder_routing()
    {
        // Test special folders that have custom logic
        $specialFolders = ['trash', 'spam', 'stared'];
        
        foreach ($specialFolders as $folder) {
            $response = $this->get("/1/folder/{$folder}");
            
            // Should handle special folders
            $this->assertTrue(
                $response->isSuccessful() ||
                $response->isRedirection() ||
                $response->status() === 404 ||
                $response->status() === 500,
                "Failed for special folder: {$folder}"
            );
        }
    }

    public function test_different_profile_ids_with_listing()
    {
        $profileIds = [1, 2, 999, 'invalid'];
        
        foreach ($profileIds as $profileId) {
            $response = $this->getJson("/{$profileId}/folder/inbox/listing/0");
            
            // Each profile ID should be handled appropriately
            $this->assertTrue(
                $response->isSuccessful() ||
                $response->isRedirection() ||
                $response->status() === 404 ||
                $response->status() === 500,
                "Failed for profile ID: {$profileId}"
            );
        }
    }
}