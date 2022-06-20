<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('users')->group(function () {
	Route::get('/login', 'App\Http\Controllers\UsersController@getLogin');
	Route::post('/login', 'App\Http\Controllers\UsersController@checkLogin');
	Route::get('/logout', 'App\Http\Controllers\UsersController@getLogout');
	Route::get('/', 'App\Http\Controllers\UsersController@index')->middleware('checkuserslogin');
});

Route::group(['middleware' => ['checkuserslogin']], function() {
    Route::get('luckdraw/', 'App\Http\Controllers\LuckydrawController@showLuckdraw');	
    Route::post('luckdraw/take', 'App\Http\Controllers\LuckydrawController@takeLuckdraw');	
});