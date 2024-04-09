<?php

use App\Http\Controllers\Api\AccountsConfirmationsController;
use App\Http\Controllers\Api\Admin\AdminPostsController;
use App\Http\Controllers\Api\CommentsController;
use App\Http\Controllers\Api\FriendsController;
use App\Http\Controllers\Api\PostsController;
use App\Http\Controllers\Api\PremissionsController;
use App\Http\Controllers\Api\PremissionsForUserController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\LikesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
const API_V = 'v1';

// private routes
Route::group(['middleware' => 'auth:sanctum', 'prefix' => API_V], function () {
    // accounts confirmations
    Route::group(["prefix" => "user"], function () {
        Route::get("/accounts-confirmations", [AccountsConfirmationsController::class, 'index'])->name('account-confirmation.index');

        Route::get("/accounts-confirmations/{limit}/{page}", [AccountsConfirmationsController::class, 'index'])->name('account-confirmation.index');

        Route::get("/account-confirmation", [AccountsConfirmationsController::class, 'show'])->name('account-confirmation.show');

        Route::post("/account-confirmation", [AccountsConfirmationsController::class, 'store'])->name('account-confirmation.store');

        Route::put("/account-confirmation/{id}", [AccountsConfirmationsController::class, 'accept'])->name('account-confirmation.accept');

        Route::delete("/account-confirmation/{id}", [AccountsConfirmationsController::class, 'unaccepted'])->name('account-confirmation.unaccepted');

        Route::delete("/account-confirmation/{id}/destroy", [AccountsConfirmationsController::class, 'destroy'])->name('account-confirmation.destroy');

        Route::get("/account-confirmation/{searchByUserId}", [AccountsConfirmationsController::class, 'searchByUserId'])->name('account-confirmation.searchByUserId');

        Route::get("/account-confirmation/{searchByName}/ByName", [AccountsConfirmationsController::class, 'searchByName'])->name('account-confirmation.searchByName');
    });

    // user routes
    Route::group(["prefix" => "user"], function () {
        Route::get('/{limit}/{page}', [UserController::class, 'index'])->name('user.index');

        Route::get('/', [UserController::class, 'getUser'])->name('user.get');

        Route::put('/', [UserController::class, 'update'])->name('user.update');

        Route::delete('/', [UserController::class, 'destroy'])->name('user.destroy');

        Route::put("/password", [UserController::class, 'resetPassword'])->name("user.resetPassword");

        Route::get('/{user}', [UserController::class, 'show'])->name('user.show');
    });

    // users routes
    Route::group(["prefix" => "users"], function () {
        Route::get("/{query_params}", [UserController::class, 'searchUsers'])->name('user.search');
        
        Route::get("/{query_params}/Id", [UserController::class, 'searchUsersById'])->name('user.search.id');
    });

    // posts routes
    Route::group(["prefix" => 'posts'], function () {
        Route::get('/{limit}/{page}', [PostsController::class, 'index'])->name('posts.index');

        Route::post('/', [PostsController::class, 'store'])->name('posts.store');

        Route::get('/{post}', [PostsController::class, 'show'])->name('posts.show');

        Route::put('/{post}', [PostsController::class, 'update'])->name('posts.update');

        Route::delete('/{post}', [PostsController::class, 'destroy'])->name('posts.destroy');

    });

    // comments routes
    Route::group(["prefix" => 'comments'], function () {
        Route::get('/{postId}', [CommentsController::class, 'index'])->name('comments.index');

        Route::post('/', [CommentsController::class, 'store'])->name('comments.store');

        Route::delete('/{comment}', [CommentsController::class, 'destroy'])->name('comments.destroy');
    });

    // likes routes
    Route::group(["prefix" => 'likes'], function () {
        Route::get('/{postId}', [LikesController::class, 'index'])->name('likes.index');

        Route::post('/', [LikesController::class, 'store'])->name('likes.store');

        Route::delete('/{like}', [LikesController::class, 'destroy'])->name('likes.destroy');

    });

    // friends routes
    Route::group(['prefix' => 'friends'], function () {
        Route::get("/", [FriendsController::class, 'index'])->name('friends.index');

        Route::get("/requests", [FriendsController::class, 'requests'])->name('friends.requests');

        Route::post("/", [FriendsController::class, 'store'])->name('friends.store');

        Route::get("/{friend}", [FriendsController::class, 'show'])->name('friends.show');

        Route::put("/{friend}", [FriendsController::class, 'update'])->name('friends.update');

        Route::delete("/{friend}", [FriendsController::class, 'destroy'])->name('friends.destroy');
    });

    // premissions routes
    Route::group(['prefix' => 'premissions'], function () {
        Route::get("/", [PremissionsController::class, 'index']);

        Route::post("/", [PremissionsController::class, 'store']);

        Route::get("/{premission}", [PremissionsController::class, 'show']);

        Route::put("/{premission}", [PremissionsController::class, 'update']);

        Route::delete("/{premission}", [PremissionsController::class, 'destroy']);
    });

    // user premissions routes
    Route::group(['prefix' => 'user/premissions'], function () {
        Route::post("/", [PremissionsForUserController::class, 'store']);

        Route::delete("/{userId}/{premissionId}", [PremissionsForUserController::class, 'destroy']);
    });

    // admin routes
    Route::group(['prefix' => 'admin'], function () {
        Route::get('posts/byId/{query_params}', [AdminPostsController::class, 'searchById'])->name('posts.searchById');

        Route::get('posts/byUserId/{query_params}', [AdminPostsController::class, 'searchByUserId'])->name('posts.searchByUserId');

        Route::get('posts/list/{limit}/{page}', [AdminPostsController::class, 'index'])->name('posts.index');

        Route::get('posts/search/{query_params}', [AdminPostsController::class, 'searchPosts'])->name('posts.searchPosts');

        Route::delete('posts/delete/{post}', [AdminPostsController::class, 'destroy'])->name('posts.destroy');

        Route::get('counts', [AdminPostsController::class, 'getAnlsAdmin'])->name('posts.getAnlsAdmin');
    });

});

// auth routes
Route::group(['prefix' => API_V], function () {
    require __DIR__ . '/auth.php';
});
