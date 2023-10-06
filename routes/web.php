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
    Route::delete('/hapus', 'FileUploadController@delete')->name('fileupload.delete');

    Route::get('/hasil', 'HasilController@index')->name('hasil.index');
    Route::get('/prediksi/{params}', 'HasilController@predictDOStatus')->name('hasil.prediksi');
    Route::get('/akurasi', 'HasilController@getAcuracy')->name('hasil.acuracy');
    // Route::get('/get-prediksi', 'HasilController@getPredictionDO')->name('hasil.prediksi_get');
});
