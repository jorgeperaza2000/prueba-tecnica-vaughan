<?php

namespace App\Services\Api\v1;

use Illuminate\Support\Facades\Http;

class UrlShortenerService
{
    public function getShortUrl(string $url): string
    {
        $response = Http::get('https://tinyurl.com/api-create.php', [
            'url' => $url,
        ]);

        if ($response->successful()) {

            return $response->body();
        }

        throw new \Exception('The shortened URL could not be generated.');
    }
}
