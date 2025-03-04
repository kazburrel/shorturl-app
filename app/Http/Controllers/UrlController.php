<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecodeUrlRequest;
use App\Http\Requests\encodeUrlRequest;
use App\Http\Resources\dencodeUrlResource;
use App\Http\Resources\encodeUrlResource;
use App\Services\Url\decodeUrl;
use App\Services\Url\encodeUrl;
use InvalidArgumentException;

/**
 * Controller for handling URL shortening and decoding operations
 */
class UrlController extends Controller
{
    /**
     * Encode a URL into its shortened form
     *
     * @param encodeUrlRequest $request The validated request containing the URL to shorten
     * @param encodeUrl $action The service class that handles the URL encoding
     * @return \Illuminate\Http\JsonResponse The encoded URL data with 201 status on success, or error with 422 status
     */
    public function encode(encodeUrlRequest $request, encodeUrl $action)
    {
        try {
            $url = $request->validated();
            $result = $action($url['url']);
            return (new encodeUrlResource($result))
                ->response()
                ->setStatusCode(201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Decode a shortened URL back to its original form
     *
     * @param DecodeUrlRequest $request The validated request containing the shortened URL
     * @param decodeUrl $action The service class that handles the URL decoding
     * @return \Illuminate\Http\JsonResponse The decoded URL data with 200 status on success, or error with 422 status
     */
    public function decode(DecodeUrlRequest $request, decodeUrl $action)
    {
        try {
            $url = $request->validated();
            $result = $action($url['url']);
            return (new dencodeUrlResource($result))
                ->response()
                ->setStatusCode(200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
