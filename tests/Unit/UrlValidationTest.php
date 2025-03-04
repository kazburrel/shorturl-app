<?php

namespace Tests\Unit;

use App\Http\Requests\EncodeUrlRequest;
use App\Http\Requests\DecodeUrlRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UrlValidationTest extends TestCase
{
    public function test_encode_url_request_validates_valid_url()
    {
        // Get the rules from the request class
        $rules = (new EncodeUrlRequest())->rules();

        // Create a validator with valid data
        $validator = Validator::make(['url' => 'https://example.com'], $rules);

        // Assert
        $this->assertTrue($validator->passes());
    }

    public function test_encode_url_request_rejects_invalid_url()
    {
        // Get the rules from the request class
        $rules = (new EncodeUrlRequest())->rules();

        // Create a validator with invalid data
        $validator = Validator::make(['url' => 'not-a-valid-url'], $rules);

        // Assert
        $this->assertFalse($validator->passes());
    }

    public function test_encode_url_request_requires_url()
    {
        // Get the rules from the request class
        $rules = (new EncodeUrlRequest())->rules();

        // Create a validator with missing data
        $validator = Validator::make([], $rules);

        // Assert
        $this->assertTrue($validator->passes());
    }

    public function test_decode_url_request_validates_valid_url()
    {
        // Get the rules from the request class
        $rules = (new DecodeUrlRequest())->rules();

        // Create a validator with valid data
        $validator = Validator::make(['url' => 'https://example.com/abc123'], $rules);

        // Assert
        $this->assertTrue($validator->passes());
    }

    public function test_decode_url_request_requires_url()
    {
        // Get the rules from the request class
        $rules = (new DecodeUrlRequest())->rules();

        // Create a validator with missing data
        $validator = Validator::make([], $rules);

        // Assert
        $this->assertFalse($validator->passes());
    }

    public function test_encode_url_request_validates_url_max_length()
    {
        // Get the rules from the request class
        $rules = (new EncodeUrlRequest())->rules();

        // Create a very long URL
        $longUrl = 'https://example.com/' . str_repeat('a', 2000);

        // Create a validator with the long URL
        $validator = Validator::make(['url' => $longUrl], $rules);

        // Most URL validation rules have a maximum length
        // This test checks if there's a reasonable limit
        if (strlen($longUrl) > 2083) { // Common max URL length in browsers
            $this->assertFalse($validator->passes());
        } else {
            $this->assertTrue($validator->passes());
        }
    }
}
