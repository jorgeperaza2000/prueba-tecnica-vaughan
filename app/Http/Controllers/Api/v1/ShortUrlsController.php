<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\ShortUrlsRequest;
use App\Services\Api\v1\UrlShortenerService;
use Illuminate\Http\JsonResponse;

class ShortUrlsController extends Controller
{
    protected $urlShortenerService;

    public function __construct(UrlShortenerService $urlShortenerService)
    {
        $this->urlShortenerService = $urlShortenerService;
    }

    public function shortUrl(ShortUrlsRequest $request): JsonResponse
    {
        try {
            $shortenedUrl = $this->urlShortenerService->getShortUrl($request->url);

            return response()->json(['url' => $shortenedUrl], 200);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
