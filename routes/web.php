<?php

use App\Http\Controllers\ActorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WatchHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::resource('movies', MovieController::class);
Route::resource('genres', GenreController::class);
Route::resource('actors', ActorController::class);
Route::resource('subscriptions', SubscriptionController::class);
Route::resource('watch-histories', WatchHistoryController::class);
