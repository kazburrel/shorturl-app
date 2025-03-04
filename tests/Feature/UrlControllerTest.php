<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UrlControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /**
     * Test that a URL can be successfully encoded.
     */
    public function test_url_can_be_encoded()
    {
        $response = $this->postJson('/api/v1/encode', [
            'url' => 'https://example.com'
        ]);

        $response->assertStatus(201);

        // Check that the response contains only the short_url
        $responseData = $response->json();
        $this->assertArrayHasKey('short_url', $responseData);
        $this->assertNotEmpty($responseData['short_url']);
    }

    /**
     * Test that a URL can be successfully decoded.
     */
    public function test_url_can_be_decoded()
    {
        // First encode a URL
        $encodeResponse = $this->postJson('/api/v1/encode', [
            'url' => 'https://example.com'
        ]);

        $shortUrl = $encodeResponse->json('short_url');

        // Now decode it
        $decodeResponse = $this->postJson('/api/v1/decode', [
            'url' => $shortUrl
        ]);

        $decodeResponse->assertStatus(200);

        // Check that the response contains only the original_url
        $decodeData = $decodeResponse->json();
        $this->assertArrayHasKey('original_url', $decodeData);
        $this->assertEquals('https://example.com', $decodeData['original_url']);
    }

    /**
     * Test that invalid URLs are rejected during encoding.
     */
    public function test_invalid_url_is_rejected_during_encoding()
    {
        $response = $this->postJson('/api/v1/encode', [
            'url' => 'not-a-valid-url'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['url']);
    }

    /**
     * Test that non-existent URLs return an error during decoding.
     */
    public function test_nonexistent_url_returns_error_during_decoding()
    {
        $response = $this->postJson('/api/v1/decode', [
            'url' => url('/nonexistent')
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('error', $response->json());
    }

    /**
     * Test that the same URL always generates the same short URL.
     */
    public function test_same_url_generates_same_short_url()
    {
        $response1 = $this->postJson('/api/v1/encode', [
            'url' => 'https://example.com'
        ]);

        $response2 = $this->postJson('/api/v1/encode', [
            'url' => 'https://example.com'
        ]);

        $this->assertEquals(
            $response1->json('short_url'),
            $response2->json('short_url')
        );
    }

    /**
     * Test that different URLs generate different short URLs.
     */
    public function test_different_urls_generate_different_short_urls()
    {
        $response1 = $this->postJson('/api/v1/encode', [
            'url' => 'https://example.com'
        ]);

        $response2 = $this->postJson('/api/v1/encode', [
            'url' => 'https://example.org'
        ]);

        $this->assertNotEquals(
            $response1->json('short_url'),
            $response2->json('short_url')
        );
    }
}
