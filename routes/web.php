<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistController;

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