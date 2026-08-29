<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Fishinglog\Http\Controllers\Admin\AdminController;
use Fishinglog\Http\Controllers\Angler\AnglerController;
use Fishinglog\Http\Controllers\Angler\AnglerProfileController;
use Fishinglog\Http\Controllers\Angler\AnglerStatsController;
use Fishinglog\Http\Controllers\CrewController;
use Fishinglog\Http\Controllers\ExpeditionController;
use Fishinglog\Http\Controllers\FishBreedController;
use Fishinglog\Http\Controllers\FishController;
use Fishinglog\Http\Controllers\FishFamilyController;
use Fishinglog\Http\Controllers\FishingZoneController;
use Fishinglog\Http\Controllers\LakeController;
use Fishinglog\Http\Controllers\LakeVisitController;
use Fishinglog\Http\Controllers\LureController;
use Fishinglog\Http\Controllers\PostController;
use Fishinglog\Http\Controllers\ProfileController;
use Fishinglog\Http\Controllers\RecordController;
use Fishinglog\Http\Controllers\SearchController;

use Fishinglog\Http\Controllers\MapController;
use Fishinglog\Http\Controllers\PhotoController;
use Fishinglog\Http\Controllers\ExplorerController;

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

Route::get('login', [Fishinglog\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [Fishinglog\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [Fishinglog\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Fallback direct storage delivery for uploaded media (ensures photos display across all platforms)
Route::get('/storage/{path}', function ($path) {
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('public')->response($path);
})->where('path', '.*')->name('storage.local');

Route::get('/register/invited', [Fishinglog\Http\Controllers\Admin\AdminInviteController::class, 'showInvitedRegistration'])->name('register.invited')->middleware('signed:relative');
Route::post('/register/invited', [Fishinglog\Http\Controllers\Admin\AdminInviteController::class, 'processInvitedRegistration'])->name('register.invited.process')->middleware('signed:relative');

Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('home');
    Route::get('/edit', [ProfileController::class, 'edit']);
    Route::put('/', [ProfileController::class, 'update']);
});


Route::prefix('admin')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin');
    Route::post('/notifications/mark-all-read', [AdminController::class, 'markAllNotificationsRead'])->name('admin.notifications.mark_read');
    Route::post('/notifications/{id}/mark-read', [AdminController::class, 'markNotificationRead'])->name('admin.notifications.mark_single_read');
    Route::post('/sync/trigger', [AdminController::class, 'triggerSync'])->name('admin.sync.trigger');
    Route::post('/sync/baseline', [AdminController::class, 'triggerBaselineSync'])->name('admin.sync.baseline');
    Route::post('/sync/mark-synced', [AdminController::class, 'markAllSynced'])->name('admin.sync.mark_synced');
    Route::post('/weather/sync', [AdminController::class, 'triggerWeatherSync'])->name('admin.weather.sync');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/invite', [Fishinglog\Http\Controllers\Admin\AdminInviteController::class, 'invite'])->name('admin.users.invite');
    Route::post('/users/quick-add', [Fishinglog\Http\Controllers\Admin\AdminQuickAddController::class, 'store'])->name('admin.users.quick-add');
    Route::post('/users/link', [AdminController::class, 'linkAngler'])->name('admin.users.link');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::post('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('admin.users.verify');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/trash', [AdminController::class, 'trash'])->name('admin.trash');
    Route::post('/trash/restore', [AdminController::class, 'restore'])->name('admin.trash.restore');
    Route::delete('/trash/force-delete', [AdminController::class, 'forceDelete'])->name('admin.trash.force-delete');
});

Route::group(['middleware' => 'auth'], function () {

    // Global Omnibox & Command Palette Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Angler routes
    Route::prefix('angler')->group(function () {
        Route::get('/', [AnglerController::class, 'index']);
        Route::get('/stats', [AnglerStatsController::class, 'index'])->name('angler.stats');
        Route::get('/create', [AnglerController::class, 'create']);
        Route::get('/{angler}', [AnglerProfileController::class, 'show']);
        Route::get('/{angler}/edit', [AnglerController::class, 'edit']);
        Route::post('/', [AnglerController::class, 'store']);
        Route::put('/', [AnglerController::class, 'update']);
        Route::delete('/{angler}', [AnglerController::class, 'destroy']);
        Route::get('/{angler}/profile', [AnglerProfileController::class, 'show']);

        Route::post('/avatar', [AnglerController::class, 'updateAvatar'])->name('angler.avatar.update');
    });

    // Offline Map & Explorer routes
    Route::get('/map/offline', [MapController::class, 'offline'])->name('map.offline');
    Route::get('/map/explorer', [ExplorerController::class, 'index'])->name('map.explorer');

    // Fishing Zone routes
    Route::prefix('fishing-zone')->group(function () {
        Route::get('/', [FishingZoneController::class, 'index'])->name('fishing-zone.index');
        Route::get('/{fishingZone}', [FishingZoneController::class, 'show'])->name('fishing-zone.show');
    });

    // Lake routes
    Route::prefix('lake')->group(function () {
        Route::get('/', [LakeController::class, 'index'])->name('lakes.index');
        Route::get('/create', [LakeController::class, 'create']);
        Route::get('/{lake}', [LakeController::class, 'show']);
        Route::get('/{lake}/edit', [LakeController::class, 'edit']);
        Route::post('/', [LakeController::class, 'store']);
        Route::put('/', [LakeController::class, 'update']);
        Route::delete('/{lake}', [LakeController::class, 'destroy']);
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
        Route::get('/category/{category}', [LureController::class, 'categoryShow'])->name('lure.category');
        Route::get('/model/{model}', [LureController::class, 'modelShow'])->name('lure.model');
        Route::get('/{lure}', [LureController::class, 'show']);
        Route::get('/{lure}/edit', [LureController::class, 'edit']);
        Route::post('/import-catalog', [LureController::class, 'importCatalog'])->name('lure.import-catalog');
        Route::post('/batch', [LureController::class, 'storeBatch'])->name('lure.batch');
        Route::post('/quick', [LureController::class, 'storeQuick'])->name('lure.quick');
        Route::post('/', [LureController::class, 'store']);
        Route::put('/', [LureController::class, 'update']);
        Route::delete('/{lure}', [LureController::class, 'destroy']);
    });



    // Record routes
    Route::prefix('record')->group(function () {
        Route::get('/', [RecordController::class, 'index'])->name('record.index');
        Route::get('/directory', [RecordController::class, 'directory'])->name('record.directory');
        Route::get('/quick', [RecordController::class, 'quick']);
        Route::get('/offline-review', [RecordController::class, 'offlineReview'])->name('record.offline-review');
        Route::get('/create', [RecordController::class, 'create']);
        Route::get('/{record}', [RecordController::class, 'show'])->name('record.show');
        Route::get('/{record}/edit', [RecordController::class, 'edit'])->name('record.edit');

        Route::post('/', [RecordController::class, 'store']);
        Route::put('/', [RecordController::class, 'update']);
        Route::delete('/{record}', [RecordController::class, 'destroy']);
    });

    // Plural route aliases to prevent 404s on /records/{id}
    Route::get('/records/{record}', [RecordController::class, 'show']);
    Route::get('/records', function () {
        return redirect('/catches');
    });

    // Expedition routes
    Route::prefix('expedition')->group(function () {
        Route::get('/', [ExpeditionController::class, 'index']);
        Route::get('/create', [ExpeditionController::class, 'create']);
        Route::get('/{expedition}', [ExpeditionController::class, 'show']);
        Route::get('/{expedition}/edit', [ExpeditionController::class, 'edit']);
        Route::post('/', [ExpeditionController::class, 'store']);
        Route::put('/', [ExpeditionController::class, 'update']);
        Route::delete('/{expedition}', [ExpeditionController::class, 'destroy']);
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

    // Photo management routes
    Route::prefix('photos')->group(function () {
        Route::post('/', [PhotoController::class, 'store'])->name('photos.store');
        Route::delete('/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
        Route::post('/{photo}/cover', [PhotoController::class, 'setCover'])->name('photos.cover');
        Route::post('/{photo}/avatar', [PhotoController::class, 'setAsAvatar'])->name('photos.avatar');
    });

});