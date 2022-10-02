<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;

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
    return view('login');
});

Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin');

Route::get('home', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::get('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout')->middleware('auth');

Route::get('register', [RegisterController::class, 'register'])->name('register');
Route::post('register/action', [RegisterController::class, 'actionregister'])->name('actionregister');

Route::get('diary/{id}/edit', [DiaryController::class, 'edit'])->name('diary.edit');
Route::resource('diary', DiaryController::class);

Route::get("diary/{id}/archive_update", [DiaryController::class, 'archive_update'])->name('diary.archive_update');

Route::get('archive/{id}/archive', [ArchiveController::class, 'edit'])->name('archive.edit');
Route::resource('archive', ArchiveController::class);

Route::get("archive/{id}/archive_update", [ArchiveController::class, 'archive_update'])->name('archive.archive_update');




