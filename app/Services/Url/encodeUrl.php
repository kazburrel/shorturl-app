<?php

namespace App\Services\Url;

use App\Traits\UrlMapTrait;

/**
 * Service class for encoding URLs into shortened versions
 */
class encodeUrl
{
    use UrlMapTrait;

    /**
     * Encode a URL into a shortened version
     *
     * @param string $url The original URL to encode
     * @return array Array containing both the shortened and original URLs
     */
    public function __invoke(string $url): array
    {
        // Generate a unique code for the URL by:
        // 1. Using crc32() to create a 32-bit hash of the URL
        // 2. Converting the hash from base 16 (hex) to base 36 (alphanumeric)
        // Note: Since crc32 is deterministic, the same URL will always generate the same code,
        // which ensures consistency when the same URL is shortened multiple times
        $code = base_convert(crc32($url), 16, 36);

        // Create array with both shortened and original URLs
        $urlData = [
            'short_url' => url("/{$code}"),
            'original_url' => $url
        ];

        // Store the URL mapping in persistent storage
        self::storeUrl($code, $urlData);

        return $urlData;
    }
}
