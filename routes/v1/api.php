<?php

use App\Http\Controllers\Api\v1\ShortUrlsController;
use App\Http\Middleware\v1\BearerAuthorization;
use Illuminate\Support\Facades\Route;

Route::middleware([BearerAuthorization::class])->group(function () {

    Route::post('/short-urls', [ShortUrlsController::class, 'shortUrl']);
});
