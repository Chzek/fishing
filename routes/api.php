<?php

use Fishinglog\Http\Controllers\Api\v1\AnglerApiController;
use Fishinglog\Http\Controllers\Api\v1\LakeApiController;
use Fishinglog\Http\Controllers\Api\v1\RecordApiController;
use Fishinglog\Http\Controllers\Api\v1\ReferenceApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/reference-data', [ReferenceApiController::class, 'index']);

    Route::get('/records', [RecordApiController::class, 'index']);
    Route::post('/records', [RecordApiController::class, 'store']);
    Route::get('/records/{record}', [RecordApiController::class, 'show']);

    Route::get('/lakes', [LakeApiController::class, 'index']);
    Route::get('/lakes/{lake}', [LakeApiController::class, 'show']);

    Route::get('/anglers', [AnglerApiController::class, 'index']);
    Route::get('/anglers/{angler}', [AnglerApiController::class, 'show']);
});
