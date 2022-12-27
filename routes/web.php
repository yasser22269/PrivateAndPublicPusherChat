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

\Illuminate\Support\Facades\Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/chat', [App\Http\Controllers\PublicChatsController::class, 'index']);
Route::get('/messages', [App\Http\Controllers\PublicChatsController::class, 'fetchMessages']);
Route::post('/messages', [App\Http\Controllers\PublicChatsController::class, 'sendMessage']);



Route::group(['prefix' => 'Private','middleware' =>['auth:web'] ],function () {
Route::get('/chat', [App\Http\Controllers\PrivateChatsController::class, 'index']);
Route::get('/messages/{id}', [App\Http\Controllers\PrivateChatsController::class, 'fetchMessages']);
Route::post('/messages', [App\Http\Controllers\PrivateChatsController::class, 'sendMessage']);
});
