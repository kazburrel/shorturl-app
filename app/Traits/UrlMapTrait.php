<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

/**
 * Trait for handling URL storage and retrieval operations
 * Used by URL encoding and decoding services to persist shortened URL mappings
 */
trait UrlMapTrait
{
    /**
     * Store a URL mapping in the cache
     *
     * @param string $code The unique code/key for the shortened URL
     * @param array $data Array containing both shortened and original URLs
     * @return void
     */
    protected static function storeUrl(string $code, array $data): void
    {
        // Store the URL data in cache using the shortened code as the key
        Cache::put($code, $data);
    }

    /**
     * Retrieve a URL mapping from the cache
     *
     * @param string $url The code/key to look up
     * @return array|null The URL mapping data if found, null otherwise
     */
    protected static function getUrl(string $url): ?array
    {
        // Get the URL data from cache using the code
        return Cache::get($url);
    }
}
