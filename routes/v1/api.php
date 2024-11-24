<?php

use App\Http\Middleware\v1\BearerAuthorization;
use Illuminate\Support\Facades\Route;

Route::middleware([BearerAuthorization::class])->group(function () {

    return response('TODO: implement short-urls', 200);
});
