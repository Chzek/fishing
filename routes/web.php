<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Fishinglog\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::prefix('profile')->group(function(){
  Route::get('/', 'ProfileController@show')
    ->name('home');
  Route::get('/edit', 'ProfileController@edit');
});

Route::get('/admin', [AdminController::class, 'index'])
  ->middleware('is_admin')
  ->name('admin');

Route::group(['middleware' => 'auth'], function(){

  // Angler routes
  Route::prefix('angler')->group(function(){
    Route::get('/', 'AnglerController@index');
    Route::get('/create', 'AnglerController@create');
    Route::get('/{angler}', 'AnglerController@show');
    Route::get('/{angler}/edit', 'AnglerController@edit');
    Route::post('/', 'AnglerController@store');
    Route::put('/', 'AnglerController@update');
    Route::get('/{angler}/profile', [Fishinglog\Http\Controllers\Angler\AnglerProfileController::class, 'show']);
  });

  // Lake routes
  Route::prefix('lake')->group(function(){
    Route::get('/', 'LakeController@index');
    Route::get('/create', 'LakeController@create');
    Route::get('/{lake}', 'LakeController@show');
    Route::get('/{lake}/edit', 'LakeController@edit');
    Route::post('/', 'LakeController@store');
    Route::put('/', 'LakeController@update');
  });

  Route::get('/lake/{lake}/visits', 'LakeVisitController@index');

  // Fish routes
  Route::prefix('fish')->group(function(){
    Route::get('/', 'FishController@index');
    Route::get('/{id}', 'FishController@show');

    Route::prefix('breed')->group(function(){
      Route::get('/create', 'FishBreedController@create');
      Route::get('/{fishBreed}/edit', 'FishBreedController@edit');
      Route::post('/', 'FishBreedController@store');
      Route::put('/', 'FishBreedController@update');
    });

    Route::prefix('family')->group(function(){
      Route::get('/create', 'FishFamilyController@create');
      Route::get('/{fishFamily}/edit', 'FishFamilyController@edit');
      Route::post('/', 'FishFamilyController@store');
      Route::put('/', 'FishFamilyController@update');
    });
  });

  // Lure routes
  Route::prefix('lure')->group(function(){
    Route::get('/', 'LureController@index');
    Route::get('/create', 'LureController@create');
    Route::get('/{lure}', 'LureController@show');
    Route::get('/{lure}/edit', 'LureController@edit');
    Route::post('/', 'LureController@store');
    Route::put('/', 'LureController@update');
  });

  // Record routes
  Route::prefix('record')->group(function(){
    Route::get('/', 'RecordController@index');
    Route::get('/create', 'RecordController@create');
    Route::get('/{record}', 'RecordController@show');
    Route::get('/{record}/edit', 'RecordController@edit');
    Route::post('/', 'RecordController@store');
    Route::put('/', 'RecordController@update');
  });

  // Expedition routes
  Route::prefix('expedition')->group(function(){
    Route::get('/', 'ExpeditionController@index');
    Route::get('/create', 'ExpeditionController@create');
    Route::get('/{expedition}', 'ExpeditionController@show');
    Route::get('/{expedition}/edit', 'ExpeditionController@edit');
    Route::post('/', 'ExpeditionController@store');
    Route::put('/', 'ExpeditionController@update');
  });

  // Crew routes
  Route::prefix('crew')->group(function(){
    Route::get('/create', 'CrewController@create');
    Route::get('/{crew}/edit', 'CrewController@edit');
    Route::post('/', 'CrewController@store');
    Route::put('/', 'CrewController@update');
  });

  // Post routes
  Route::prefix('post')->group(function(){
    Route::get('/create', 'PostController@create');
    Route::get('/{post}', 'PostController@show');
    Route::get('/{post}/edit', 'PostController@edit');
    Route::post('/', 'PostController@store');
    Route::put('/', 'PostController@update');
  });

});