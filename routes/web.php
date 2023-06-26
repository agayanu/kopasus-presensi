<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\RegistNotController;
use App\Http\Controllers\ChangePassController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PresenceSetController;

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

Route::get('/', [RegistController::class, 'index'])->name('regist');
Route::get('regist-student', [RegistController::class, 'student'])->name('regist.student');
Route::post('/', [RegistController::class, 'store']);

Route::get('login', [AuthController::class, 'showFormLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('presence')->group(function() {
        Route::get('/', [PresenceController::class, 'index'])->name('presence');
        Route::get('data', [PresenceController::class, 'data'])->name('presence.data');
        Route::post('/', [PresenceController::class, 'update']);
        Route::get('{id}/print', [PresenceController::class, 'print'])->name('presence.print');
        Route::get('download', [PresenceController::class, 'download'])->name('presence.download');
        Route::get('scan', [PresenceController::class, 'scan'])->name('presence.scan');
        Route::post('scan', [PresenceController::class, 'presence_form']);
        Route::get('{key}/presence', [PresenceController::class, 'presence'])->name('presence.presence');
        Route::get('show', [PresenceController::class, 'show'])->name('presence.show');
        Route::get('show-data', [PresenceController::class, 'show_data'])->name('presence.show-data');
    });

    Route::prefix('regist-not')->group(function() {
        Route::get('/', [RegistNotController::class, 'index'])->name('regist-not');
        Route::get('data', [RegistNotController::class, 'data'])->name('regist-not.data');
        Route::get('download', [RegistNotController::class, 'download'])->name('regist-not.download');
    });

    Route::prefix('change-pass')->group(function() {
        Route::get('/', [ChangePassController::class, 'index'])->name('change-pass');
        Route::post('/', [ChangePassController::class, 'update']);
    });

    Route::prefix('user')->group(function() {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('data', [UserController::class, 'data'])->name('user.data');
        Route::post('/', [UserController::class, 'store']);
        Route::post('update', [UserController::class, 'update'])->name('user.update');
        Route::post('/{id}/reset', [UserController::class, 'reset'])->name('user.reset');
        Route::post('/{id}/delete', [UserController::class, 'destroy'])->name('user.delete');
    });

    Route::prefix('presence-set')->group(function() {
        Route::post('clear', [PresenceSetController::class, 'clear'])->name('presence-set.clear');
    });
});