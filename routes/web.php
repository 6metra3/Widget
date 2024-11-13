<?php

use App\Http\Controllers\DBController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/contacts/{contact_id}', [DBController::class, 'getContactData']);

Route::get('/messages/{contact_id}', [WidgetController::class, 'getMessages']);

Route::post('/send/{contact_id}', [MessageController::class, 'sendMessage']);




Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
