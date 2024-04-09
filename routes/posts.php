<?php

use App\Http\Controllers\Api\PostsController;
use Illuminate\Support\Facades\Route;

Route::get('posts', [PostsController::class, 'index'])->name('posts.index');

Route::post('posts', [PostsController::class, 'store'])->name('posts.store');

Route::get('posts/{post}', [PostsController::class, 'show'])->name('posts.show');

Route::put('posts/{post}', [PostsController::class, 'update'])->name('posts.update');

Route::delete('posts/{post}', [PostsController::class, 'destroy'])->name('posts.destroy');

Route::post('posts/{post}/like', [PostsController::class, 'like'])->name('posts.like');

Route::post('posts/{post}/comment', [PostsController::class, 'comment'])->name('posts.comment');

Route::get('posts/{post}/comments', [PostsController::class, 'comments'])->name('posts.comments');

Route::get('posts/{post}/likes', [PostsController::class, 'likes'])->name('posts.likes');

Route::get('posts/{post}/comments/{comment}', [PostsController::class, 'commentShow'])->name('posts.comment.show');

Route::put('posts/{post}/comments/{comment}', [PostsController::class, 'commentUpdate'])->name('posts.comment.update');

Route::delete('posts/{post}/comments/{comment}', [PostsController::class, 'commentDestroy'])->name('posts.comment.destroy');