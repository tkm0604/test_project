<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return view('welcome');
})->name('top');


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard',[PostController::class,'index'])
->middleware(['auth','verified'])
->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('post/mypost',[PostController::class,'mypost'])->name('post.mypost');
Route::get('post/mycomment',[PostController::class,'mycomment'])->name('post.mycomment');
Route::resource('post', PostController::class);
Route::post('post/comment/store',[CommentController::class, 'store'])->name('comment.store');


require __DIR__.'/auth.php';
