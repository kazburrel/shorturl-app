<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Cache;
use App\Traits\UrlMapTrait;
use Tests\TestCase;

class UrlMapTraitTest extends TestCase
{
    // Create a test class that uses the trait
    private $testClass;

    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        // Create an anonymous class that uses the trait
        $this->testClass = new class {
            use UrlMapTrait;

            // Make protected methods public for testing
            public static function storeUrlPublic(string $code, array $data): void
            {
                self::storeUrl($code, $data);
            }

            public static function getUrlPublic(string $code): ?array
            {
                return self::getUrl($code);
            }
        };
    }

    public function test_it_can_store_a_url_in_the_cache()
    {
        // Arrange
        $code = 'testcode';
        $data = [
            'short_url' => url("/{$code}"),
            'original_url' => 'https://example.com'
        ];

        // Mock the Cache facade
        Cache::shouldReceive('put')
            ->once()
            ->with($code, $data)
            ->andReturn(true);

        // Act
        $this->testClass::storeUrlPublic($code, $data);

        // No assertion needed as we're verifying the mock expectation
        $this->assertTrue(true); // Dummy assertion
    }

    public function test_it_can_retrieve_a_url_from_the_cache()
    {
        // Arrange
        $code = 'testcode';
        $data = [
            'short_url' => url("/{$code}"),
            'original_url' => 'https://example.com'
        ];

        // Mock the Cache facade
        Cache::shouldReceive('get')
            ->once()
            ->with($code)
            ->andReturn($data);

        // Act
        $result = $this->testClass::getUrlPublic($code);

        // Assert
        $this->assertEquals($data, $result);
    }

    public function test_it_returns_null_when_url_is_not_found_in_cache()
    {
        // Arrange
        $code = 'nonexistent';

        // Mock the Cache facade
        Cache::shouldReceive('get')
            ->once()
            ->with($code)
            ->andReturn(null);

        // Act
        $result = $this->testClass::getUrlPublic($code);

        // Assert
        $this->assertNull($result);
    }
}
