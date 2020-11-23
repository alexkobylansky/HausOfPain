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

Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::post('/videos/push-to-vimeo/{fileHash}', 'ExerciseVideoController@pushToVimeo');

    Route::any('/upload/exercise-video/{any?}', function () {
        $response = app('exercise-video-server')->serve();

        return $response->send();
    })->where('fileHash', '.*');
});

Route::middleware('auth:web')->group(function() {
    Route::any('/app/upload/group-video/{any?}}', function () {
        $response = app('group-video-server')->serve();

        return $response->send();
    })->where('any', '.*');

    Route::any('/app/upload/1to1-video/{any?}', function () {
        $response = app('1to1-video-server')->serve();

        return $response->send();
    })->where('any', '.*');
});
