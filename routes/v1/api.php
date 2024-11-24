<?php

use App\Http\Middleware\v1\BearerAuthorization;
use Illuminate\Support\Facades\Route;

Route::get('/short-urls', function () {
    return response('TODO: implement short-urls', 200);
})->middleware(BearerAuthorization::class);
