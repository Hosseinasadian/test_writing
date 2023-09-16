<?php

use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UploadImageController;
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

Route::get('/', [\App\Http\Controllers\HomeController::class,'index'])->name('home');
Route::get('/single/{post}', [\App\Http\Controllers\SingleController::class,'index'])->name('single');
Route::post('/single/{post}/comment', [\App\Http\Controllers\SingleController::class,'comment'])
    ->middleware('auth:web')
    ->name('single.comment');

Route::prefix('admin')->middleware('admin')->group(function (){
    Route::resource('post',\App\Http\Controllers\Admin\PostController::class)->except('show');
    Route::resource('tag', TagController::class)->except(['show']);
    Route::post('upload',[UploadImageController::class,'upload'])->name('upload');
});

Auth::routes();
