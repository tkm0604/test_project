<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
})->name('top');


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

//お問い合わせ
Route::get('contact/create',[ContactController::class, 'create'])->name('contact.create');
Route::post('contact/store',[ContactController::class, 'store'])->name('contact.store');

// ログイン後の通常のユーザー画面
Route::middleware(['verified'])->group(function(){
    Route::get('/dashboard',[PostController::class,'index'])
    ->middleware(['auth','verified'])
    ->name('dashboard');
    Route::get('post/mypost',[PostController::class,'mypost'])->name('post.mypost');
    Route::get('post/mycomment',[PostController::class,'mycomment'])->name('post.mycomment');
    Route::resource('post', PostController::class);
    Route::post('post/comment/store',[CommentController::class, 'store'])->name('comment.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // 管理者用画面
    Route::middleware(['auth','can:admin'])->group(function(){
        Route::get('profile/index',[ProfileController::class,'index'])->name('profile.index');
    });
});




// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Route::middleware(['auth','can:admin'])->group(function(){
//     Route::get('profile/index',[ProfileController::class,'index'])->name('profile.index');
// });




require __DIR__.'/auth.php';
