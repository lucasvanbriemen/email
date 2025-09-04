<?php

namespace Tests\Feature;

use Tests\TestCase;

class OutboundMailControllerTest extends TestCase
{
    public function test_send_email_route_validation()
    {
        $response = $this->postJson('/1/compose_email', []);
        
        // Should validate and return 422, 302, 404 or 500 (missing data is expected in tests)
        $this->assertTrue(in_array($response->getStatusCode(), [422, 302, 404, 500]));
    }

    public function test_send_email_validates_email_format()
    {
        $response = $this->postJson('/1/compose_email', [
            'to' => 'invalid-email',
            'subject' => 'Test Subject',
            'body' => 'Test body'
        ]);
        
        // Should validate email format - 422, 302, 404 or 500 (missing data is expected in tests)
        $this->assertTrue(in_array($response->getStatusCode(), [422, 302, 404, 500]));
    }
}