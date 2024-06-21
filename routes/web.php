<?php

use App\Http\Controllers\FileController;
use Faker\Core\File;
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

Route::get('/', [FileController::class, 'index'])->name('home');
Route::get('/upload', [FileController::class, 'upload'])->name('upload');
