<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Fishinglog\Http\Controllers\Admin\AdminController;
use Fishinglog\Http\Controllers\Angler\AnglerController;
use Fishinglog\Http\Controllers\Angler\AnglerProfileController;
use Fishinglog\Http\Controllers\CrewController;
use Fishinglog\Http\Controllers\ExpeditionController;
use Fishinglog\Http\Controllers\FishBreedController;
use Fishinglog\Http\Controllers\FishController;
use Fishinglog\Http\Controllers\FishFamilyController;
use Fishinglog\Http\Controllers\LakeController;
use Fishinglog\Http\Controllers\LakeVisitController;
use Fishinglog\Http\Controllers\LureController;
use Fishinglog\Http\Controllers\PostController;
use Fishinglog\Http\Controllers\ProfileController;
use Fishinglog\Http\Controllers\RecordController;

use Fishinglog\Http\Controllers\MapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('home');
    Route::get('/edit', [ProfileController::class, 'edit']);
});

Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('is_admin')
    ->name('admin');

Route::group(['middleware' => 'auth'], function () {

    // Angler routes
    Route::prefix('angler')->group(function () {
        Route::get('/', [AnglerController::class, 'index']);
        Route::get('/create', [AnglerController::class, 'create']);
        Route::get('/{angler}', [AnglerController::class, 'show']);
        Route::get('/{angler}/edit', [AnglerController::class, 'edit']);
        Route::post('/', [AnglerController::class, 'store']);
        Route::put('/', [AnglerController::class, 'update']);
        Route::get('/{angler}/profile', [AnglerProfileController::class, 'show']);
        Route::post('/avatar', [AnglerController::class, 'updateAvatar'])->name('angler.avatar.update');
    });

    // Offline Map routes
    Route::get('/map/offline', [MapController::class, 'offline'])->name('map.offline');

    // Lake routes
    Route::prefix('lake')->group(function () {
        Route::get('/', [LakeController::class, 'index'])->name('lakes.index');
        Route::get('/create', [LakeController::class, 'create']);
        Route::get('/{lake}', [LakeController::class, 'show']);
        Route::get('/{lake}/edit', [LakeController::class, 'edit']);
        Route::post('/', [LakeController::class, 'store']);
        Route::put('/', [LakeController::class, 'update']);
    });

    Route::get('/lake/{lake}/visits', [LakeVisitController::class, 'index']);

    // Fish routes
    Route::prefix('fish')->group(function () {
        Route::get('/', [FishController::class, 'index']);
        Route::get('/{id}', [FishController::class, 'show']);

        Route::prefix('breed')->group(function () {
            Route::get('/create', [FishBreedController::class, 'create']);
            Route::get('/{fishBreed}/edit', [FishBreedController::class, 'edit']);
            Route::post('/', [FishBreedController::class, 'store']);
            Route::put('/', [FishBreedController::class, 'update']);
        });

        Route::prefix('family')->group(function () {
            Route::get('/create', [FishFamilyController::class, 'create']);
            Route::get('/{fishFamily}/edit', [FishFamilyController::class, 'edit']);
            Route::post('/', [FishFamilyController::class, 'store']);
            Route::put('/', [FishFamilyController::class, 'update']);
        });
    });

    // Lure routes
    Route::prefix('lure')->group(function () {
        Route::get('/', [LureController::class, 'index']);
        Route::get('/create', [LureController::class, 'create']);
        Route::get('/{lure}', [LureController::class, 'show']);
        Route::get('/{lure}/edit', [LureController::class, 'edit']);
        Route::post('/', [LureController::class, 'store']);
        Route::put('/', [LureController::class, 'update']);
    });

    // Record routes
    Route::prefix('record')->group(function () {
        Route::get('/', [RecordController::class, 'index']);
        Route::get('/quick', [RecordController::class, 'quick']);
        Route::get('/create', [RecordController::class, 'create']);
        Route::get('/{record}', [RecordController::class, 'show']);
        Route::get('/{record}/edit', [RecordController::class, 'edit']);
        Route::post('/', [RecordController::class, 'store']);
        Route::put('/', [RecordController::class, 'update']);
    });

    // Expedition routes
    Route::prefix('expedition')->group(function () {
        Route::get('/', [ExpeditionController::class, 'index']);
        Route::get('/create', [ExpeditionController::class, 'create']);
        Route::get('/{expedition}', [ExpeditionController::class, 'show']);
        Route::get('/{expedition}/edit', [ExpeditionController::class, 'edit']);
        Route::post('/', [ExpeditionController::class, 'store']);
        Route::put('/', [ExpeditionController::class, 'update']);
    });

    // Crew routes
    Route::prefix('crew')->group(function () {
        Route::get('/create', [CrewController::class, 'create']);
        Route::get('/{crew}/edit', [CrewController::class, 'edit']);
        Route::post('/', [CrewController::class, 'store']);
        Route::put('/', [CrewController::class, 'update']);
    });

    // Post routes
    Route::prefix('post')->group(function () {
        Route::get('/create', [PostController::class, 'create']);
        Route::get('/{post}', [PostController::class, 'show']);
        Route::get('/{post}/edit', [PostController::class, 'edit']);
        Route::post('/', [PostController::class, 'store']);
        Route::put('/', [PostController::class, 'update']);
    });

});