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

Route::group(['middleware' => 'auth'], function(){

  // Angler routes
  Route::get('/angler', 'AnglerController@index');
  Route::get('/angler/create', 'AnglerController@create');
  Route::get('/angler/{angler}', 'AnglerController@show');
  Route::get('/angler/{angler}/edit', 'AnglerController@edit');
  Route::post('/angler', 'AnglerController@store');
  Route::put('/angler', 'AnglerController@update');

  // Lake routes
  Route::get('/lake', 'LakeController@index');
  Route::get('/lake/create', 'LakeController@create');
  Route::get('/lake/{lake}', 'LakeController@show');
  Route::get('/lake/{lake}/edit', 'LakeController@edit');
  Route::post('/lake', 'LakeController@store');
  Route::put('/lake', 'LakeController@update');

  Route::get('/lake/{lake}/visits', 'LakeVisitController@index');

  // Fish routes
  Route::get('/fish', 'FishController@index');
  Route::get('/fish/{id}', 'FishController@show');

  Route::get('/fish/breed/create', 'FishBreedController@create');
  Route::get('/fish/breed/{fishBreed}/edit', 'FishBreedController@edit');
  Route::post('/fish/breed', 'FishBreedController@store');
  Route::put('/fish/breed', 'FishBreedController@update');

  Route::get('/fish/family/create', 'FishFamilyController@create');
  Route::get('/fish/family/{fishFamily}/edit', 'FishFamilyController@edit');
  Route::post('/fish/family', 'FishFamilyController@store');
  Route::put('/fish/family', 'FishFamilyController@update');

  // Lure routes
  Route::get('/lure', 'LureController@index');
  Route::get('/lure/create', 'LureController@create');
  Route::get('/lure/{lure}', 'LureController@show');
  Route::get('/lure/{lure}/edit', 'LureController@edit');
  Route::post('/lure', 'LureController@store');
  Route::put('/lure', 'LureController@update');

  // Record routes
  Route::get('/record', 'RecordController@index');
  Route::get('/record/create', 'RecordController@create');
  Route::get('/record/{record}', 'RecordController@show');
  Route::get('/record/{record}/edit', 'RecordController@edit');
  Route::post('/record', 'RecordController@store');
  Route::put('/record', 'RecordController@update');

  // Expedition routes
  Route::get('/expedition', 'ExpeditionController@index');
  Route::get('/expedition/create', 'ExpeditionController@create');
  Route::get('/expedition/{expedition}', 'ExpeditionController@show');
  Route::get('/expedition/{expedition}/edit', 'ExpeditionController@edit');
  Route::post('/expedition', 'ExpeditionController@store');
  Route::put('/expedition', 'ExpeditionController@update');

  // Crew routes
  Route::get('/crew/create', 'CrewController@create');
  Route::get('/crew/{crew}/edit', 'CrewController@edit');
  Route::post('/crew', 'CrewController@store');
  Route::put('/crew', 'CrewController@update');

  // Post routes
  Route::get('/post/create', 'PostController@create');
  Route::get('/post/{post}', 'PostController@show');
  Route::get('/post/{post}/edit', 'PostController@edit');
  Route::post('/post', 'PostController@store');
  Route::put('/post', 'PostController@update');

});