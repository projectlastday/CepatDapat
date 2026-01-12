<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\MainController@info');

Route::get('/home', 'App\Http\Controllers\MainController@home');
Route::get('/katalog', 'App\Http\Controllers\MainController@katalog');

Route::get('/login', 'App\Http\Controllers\MainController@login');
Route::post('/login', 'App\Http\Controllers\MainController@aksi_login');
Route::get('/logout', 'App\Http\Controllers\MainController@aksi_logout');

Route::get('/pasang_lelang', 'App\Http\Controllers\MainController@pasang_lelang');
Route::post('/pasang_lelang', 'App\Http\Controllers\MainController@aksi_pasang_lelang');

Route::get('/moderasi', 'App\Http\Controllers\MainController@moderasi');
Route::post('/moderasi/aksi', 'App\Http\Controllers\MainController@aksi_moderasi');
Route::post('/moderasi/suspend', 'App\Http\Controllers\MainController@aksi_suspend');

Route::get('/lelangku', 'App\Http\Controllers\MainController@lelangku');
Route::post('/lelangku/mulai', 'App\Http\Controllers\MainController@aksi_mulai_lelang');

Route::post('/katalog/tawar', 'App\Http\Controllers\MainController@aksi_tawar');

Route::get('/api/katalog-updates', 'App\Http\Controllers\MainController@get_updates');