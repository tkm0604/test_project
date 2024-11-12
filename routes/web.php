<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
})->name('top');


// ダッシュボードのルート
Route::get('/dashboard', function (Request $request) {
    $user = Auth::user();

    // X認証ユーザーはメール認証をスキップ
    if ($user && $user->twitter_id !== null) {
        return app(PostController::class)->index($request);
    }

    // 通常ユーザーにはメール認証を適用
    if ($user && !$user->hasVerifiedEmail()) {
        return redirect('/verify-email');
    }

    return app(PostController::class)->index($request);
})->middleware(['auth'])->name('dashboard');

//お問い合わせ
Route::get('contact/create',[ContactController::class, 'create'])->name('contact.create');
Route::post('contact/store',[ContactController::class, 'store'])->name('contact.store');

// ログイン後の通常のユーザー画面
Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('post/mypost', [PostController::class, 'mypost'])->name('post.mypost');
    Route::get('post/mycomment', [PostController::class, 'mycomment'])->name('post.mycomment');
    Route::resource('post', PostController::class);
    Route::post('post/comment/store', [CommentController::class, 'store'])->name('comment.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 管理者用画面
    Route::middleware(['can:admin'])->group(function() {
        Route::get('profile/index', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/adedit/{user}', [ProfileController::class, 'adedit'])->name('profile.adedit');
        Route::patch('/profile/adupdate/{user}', [ProfileController::class, 'adupdate'])->name('profile.adupdate');
        Route::delete('profile/{user}', [ProfileController::class, 'addestroy'])->name('profile.addestroy');
        Route::patch('roles/{user}/attach', [RoleController::class, 'attach'])->name('role.attach');
        Route::patch('roles/{user}/detach', [RoleController::class, 'detach'])->name('role.detach');
    });
});

// X(Twitter)の認証用ルート
Route::get('login/x', [SocialAuthController::class, 'redirectToProvider']);
Route::get('login/x/callback', [SocialAuthController::class, 'handleProviderCallback']);






require __DIR__.'/auth.php';
