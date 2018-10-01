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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')
  ->name('home');

Route::get('/admin', 'AdminController@index')
  ->middleware('is_admin')
  ->name('admin');

// Angler routes
Route::get('/angler', 'AnglerController@index');
Route::get('/angler/create', 'AnglerController@create');
Route::get('/angler/{id}', 'AnglerController@show');
Route::get('/angler/{id}/edit', 'AnglerController@edit');
Route::post('/angler', 'AnglerController@store');
Route::put('/angler', 'AnglerController@update');

// Lake routes
Route::get('/lake', 'LakeController@index');
Route::get('/lake/create', 'LakeController@create');
Route::get('/lake/{id}', 'LakeController@show');
Route::get('/lake/{id}/edit', 'LakeController@edit');
Route::post('/lake', 'LakeController@store');
Route::put('/lake', 'LakeController@update');

// Fish routes
Route::get('/fish', 'FishController@index');
Route::get('/fish/{id}', 'FishController@show');

Route::get('/fish/breed/create', 'FishBreedController@create');
Route::get('/fish/breed/{id}/edit', 'FishBreedController@edit');
Route::post('/fish/breed', 'FishBreedController@store');
Route::put('/fish/breed', 'FishBreedController@update');

Route::get('/fish/family/create', 'FishFamilyController@create');
Route::get('/fish/family/{id}/edit', 'FishFamilyController@edit');
Route::post('/fish/family', 'FishFamilyController@store');
Route::put('/fish/family', 'FishFamilyController@update');

// Lure routes
Route::get('/lure', 'LureController@index');
Route::get('/lure/create', 'LureController@create');
Route::get('/lure/{id}', 'LureController@show');
Route::get('/lure/{id}/edit', 'LureController@edit');
Route::post('/lure', 'LureController@store');
Route::put('/lure', 'LureController@update');

// Record routes
Route::get('/record', 'RecordController@index');
Route::get('/record/create', 'RecordController@create');
Route::get('/record/{id}', 'RecordController@show');
Route::get('/record/{id}/edit', 'RecordController@edit');
Route::post('/record', 'RecordController@store');
Route::put('/record', 'RecordController@update');

// Expedition routes
Route::get('/expedition', 'ExpeditionController@index');
Route::get('/expedition/create', 'ExpeditionController@create');
Route::get('/expedition/{id}', 'ExpeditionController@show');
Route::get('/expedition/{id}/edit', 'ExpeditionController@edit');
Route::post('/expedition', 'ExpeditionController@store');
Route::put('/expedition', 'ExpeditionController@update');

// Crew routes
Route::get('/crew/create', 'CrewController@create');
Route::get('/crew/{id}/edit', 'CrewController@edit');
Route::post('/crew', 'CrewController@store');
Route::put('/crew', 'CrewController@update');

// Post routes
Route::get('/post/create', 'PostController@create');
Route::get('/post/{id}', 'PostController@show');
Route::get('/post/{id}/edit', 'PostController@edit');
Route::post('/post', 'PostController@store');
Route::put('/post', 'PostController@update');