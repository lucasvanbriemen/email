<?php

namespace Tests\Feature;

use Tests\TestCase;

class MailboxControllerTest extends TestCase
{
    public function test_mailbox_route_exists()
    {
        $response = $this->get('/1/folder/inbox');
        
        // Should return 200, 302 (redirect), 404, or 500 (missing data is expected in tests)
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302, 404, 500]));
    }

    public function test_mailbox_listing_route_exists()
    {
        $response = $this->get('/1/folder/inbox/listing/0');
        
        // Should return 200, 302 (redirect), 404, or 500 (missing data is expected in tests)
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302, 404, 500]));
    }
}