<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/fileupload', 'FileUploadController@index')->name('fileupload.index');
    Route::get('/get-all', 'FileUploadController@getAll')->name('fileupload.getAll');
    Route::post('/upload', 'FileUploadController@store')->name('fileupload.store');
    Route::get('/hasil', 'HasilController@index')->name('hasil.index');
});
