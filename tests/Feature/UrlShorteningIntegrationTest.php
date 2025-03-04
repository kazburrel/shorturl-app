<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UrlShorteningIntegrationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_returns_same_short_url_for_same_original_url()
    {
        // Arrange
        $originalUrl = 'https://example.com';

        // Act 1: Encode the URL first time
        $encodeResponse1 = $this->postJson('/api/v1/encode', [
            'url' => $originalUrl
        ]);
        $shortUrl1 = $encodeResponse1->json('short_url');

        // Act 2: Encode the same URL second time
        $encodeResponse2 = $this->postJson('/api/v1/encode', [
            'url' => $originalUrl
        ]);
        $shortUrl2 = $encodeResponse2->json('short_url');

        // Assert
        $this->assertEquals($shortUrl1, $shortUrl2);
    }

    public function test_it_handles_error_when_decoding_expired_or_nonexistent_url()
    {
        // Arrange - Create a URL that doesn't exist in the system
        $nonExistentUrl = url('/nonexistent');

        // Act
        $response = $this->postJson('/api/v1/decode', [
            'url' => $nonExistentUrl
        ]);

        // Assert
        $response->assertStatus(422);
        $this->assertArrayHasKey('error', $response->json());
    }

    public function test_it_can_encode_and_then_decode_a_url_in_a_full_flow()
    {
        // Arrange
        $originalUrl = 'https://example.com';

        // Act 1: Encode the URL
        $encodeResponse = $this->postJson('/api/v1/encode', [
            'url' => $originalUrl
        ]);

        // Assert 1
        $encodeResponse->assertStatus(201);
        $shortUrl = $encodeResponse->json('short_url');
        $this->assertNotEmpty($shortUrl);

        // Act 2: Decode the URL
        $decodeResponse = $this->postJson('/api/v1/decode', [
            'url' => $shortUrl
        ]);

        // Assert 2
        $decodeResponse->assertStatus(200);
        $decodedUrl = $decodeResponse->json('original_url');
        $this->assertEquals($originalUrl, $decodedUrl);
    }
}
