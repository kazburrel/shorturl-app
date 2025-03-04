<h1 align="center">URL Shortener API</h1>

## About

A simple, efficient URL shortening service built with Laravel. This RESTful API provides a stateless architecture for converting long URLs into short, shareable links.

### Features

- Convert long URLs into short, easy-to-share links
- Retrieve original URLs from shortened links 
- RESTful API design
- Stateless architecture using Laravel's Cache system
- Deterministic URL encoding (same URL always produces same short code)

### Tech Stack

- PHP 8.2
- Laravel 12
- Docker with Laravel Sail

## Quick Start

1. Clone the repository:
   ```bash
   git clone https://github.com/kazburrel/shorturl-app.git
   cd url-shortener
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Start the application:
   ```bash
   ./vendor/bin/sail up -d
   ```

5. Test the API:
   ```bash
   # Encode a URL
   curl -X POST http://localhost/api/v1/encode \
        -H "Content-Type: application/json" \
        -d '{"url": "https://example.com"}'

   # Decode a URL
   curl -X POST http://localhost/api/v1/decode \
        -H "Content-Type: application/json" \
        -d '{"url": "http://localhost/abc123"}'
   ```

## Testing

Run the test suite using:
```bash
./vendor/bin/sail test
```

