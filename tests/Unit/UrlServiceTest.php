<?php

namespace Tests\Unit;

use App\Services\Url\encodeUrl;
use App\Services\Url\decodeUrl;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UrlServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_encodes_a_url_correctly()
    {
        // Arrange
        Cache::shouldReceive('put')->once()->andReturn(true);

        $encoder = new encodeUrl();
        $originalUrl = 'https://example.com';

        // Act
        $result = $encoder($originalUrl);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('short_url', $result);
        $this->assertArrayHasKey('original_url', $result);
        $this->assertEquals($originalUrl, $result['original_url']);

        // The short URL should contain the base URL and a code
        $code = basename(parse_url($result['short_url'], PHP_URL_PATH));
        $this->assertNotEmpty($code);
    }

    public function test_it_decodes_a_url_correctly()
    {
        // Arrange
        $shortCode = 'abc123';
        $shortUrl = url("/{$shortCode}");
        $originalUrl = 'https://example.com';
        $urlData = [
            'short_url' => $shortUrl,
            'original_url' => $originalUrl
        ];

        Cache::shouldReceive('get')
            ->once()
            ->with($shortCode)
            ->andReturn($urlData);

        $decoder = new decodeUrl();

        // Act
        $result = $decoder($shortUrl);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('short_url', $result);
        $this->assertArrayHasKey('original_url', $result);
        $this->assertEquals($originalUrl, $result['original_url']);
        $this->assertEquals($shortUrl, $result['short_url']);
    }

    public function test_it_throws_exception_when_decoding_non_existent_url()
    {
        // Arrange
        $shortCode = 'nonexistent';
        $nonExistentUrl = url("/{$shortCode}");

        Cache::shouldReceive('get')
            ->once()
            ->with($shortCode)
            ->andReturn(null);

        $decoder = new decodeUrl();

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('URL not found');
        $decoder($nonExistentUrl);
    }

    public function test_it_generates_consistent_short_codes_for_the_same_url()
    {
        // Arrange
        Cache::shouldReceive('put')->twice()->andReturn(true);

        $encoder = new encodeUrl();
        $originalUrl = 'https://example.com';

        // Act
        $result1 = $encoder($originalUrl);
        $result2 = $encoder($originalUrl);

        // Assert - The same URL should generate the same short code
        $this->assertEquals($result1['short_url'], $result2['short_url']);
    }

    public function test_it_generates_different_short_codes_for_different_urls()
    {
        // Arrange
        Cache::shouldReceive('put')->twice()->andReturn(true);

        $encoder = new encodeUrl();
        $url1 = 'https://example.com';
        $url2 = 'https://example.org';

        // Act
        $result1 = $encoder($url1);
        $result2 = $encoder($url2);

        // Assert - Different URLs should generate different short codes
        $this->assertNotEquals($result1['short_url'], $result2['short_url']);
    }

    public function test_it_extracts_the_code_from_a_url_correctly_in_decode_url()
    {
        // Arrange
        $shortCode = 'abc123';
        $shortUrl = url("/{$shortCode}");
        $originalUrl = 'https://example.com';
        $urlData = [
            'short_url' => $shortUrl,
            'original_url' => $originalUrl
        ];

        Cache::shouldReceive('get')
            ->once()
            ->with($shortCode)
            ->andReturn($urlData);

        $decoder = new decodeUrl();

        // Act
        $result = $decoder($shortUrl);

        // Assert
        $this->assertEquals($urlData, $result);
    }

    public function test_it_handles_urls_with_query_parameters_correctly()
    {
        // Arrange
        Cache::shouldReceive('put')->once()->andReturn(true);

        $encoder = new encodeUrl();
        $originalUrl = 'https://example.com?param=value&another=123';

        // Act
        $result = $encoder($originalUrl);

        // Assert
        $this->assertEquals($originalUrl, $result['original_url']);
    }
}
