<?php

namespace App\Services\Url;

use App\Traits\UrlMapTrait;

/**
 * Service class for decoding shortened URLs back to their original form
 */
class decodeUrl
{
    use UrlMapTrait;

    /**
     * Decode a shortened URL back to its original form
     *
     * @param string $url The shortened URL to decode
     * @return array Array containing both the shortened and original URLs
     * @throws \InvalidArgumentException If the URL is not found in storage
     */
    public function __invoke(string $url): array
    {
        // Extract the code from the end of the URL path
        $code = basename(parse_url($url, PHP_URL_PATH));

        // Retrieve the URL data from storage using the code
        $urlData = self::getUrl($code);

        // If no URL data found, throw exception
        if (!$urlData) {
            throw new \InvalidArgumentException('URL not found');
        }

        return $urlData;
    }
}
