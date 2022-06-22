<?php

use Illuminate\Support\Facades\Route;

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

/*Route::get('/', function () {
    return view('welcome');
});*/

Auth::routes();

Route::view('/', 'welcome')->name('home');

// Statuses routes
Route::get('statuses', [\App\Http\Controllers\StatusesController::class, 'index'])->name('statuses.index');
Route::post('statuses', [\App\Http\Controllers\StatusesController::class, 'store'])->name('statuses.store')->middleware('auth');

// Statuses likes routes
Route::post('statuses/{status}/likes', [\App\Http\Controllers\StatusLikesController::class, 'store'])->name('statuses.likes.store')->middleware('auth');
Route::delete('statuses/{status}/likes', [\App\Http\Controllers\StatusLikesController::class, 'destroy'])->name('statuses.likes.destroy')->middleware('auth');

// Statuses Comments routes
Route::post('statuses/{status}/comments', [\App\Http\Controllers\StatusCommentsController::class, 'store'])->name('statuses.comments.store')->middleware('auth');

// Comments Likes routes
Route::post('comments/{comment}/likes', [\App\Http\Controllers\CommentLikesController::class, 'store'])->name('comments.likes.store')->middleware('auth');
Route::delete('comments/{comment}/likes', [\App\Http\Controllers\CommentLikesController::class, 'destroy'])->name('comments.likes.destroy')->middleware('auth');

// Users routes
Route::get('@{user}', [\App\Http\Controllers\UsersController::class, 'show'])->name('users.show'); // busca a traves del nombre definido en el modelo

// Users statuses routes
Route::get('users/{user}/statuses', [\App\Http\Controllers\UsersStatusController::class, 'index'])->name('users.statuses.index');

// Friendships routes
Route::post('friendships/{recipient}', [\App\Http\Controllers\FriendshipsController::class, 'store'])->name('friendships.store')->middleware('auth');
Route::delete('friendships/{user}', [\App\Http\Controllers\FriendshipsController::class, 'destroy'])->name('friendships.destroy')->middleware('auth');

// Accept Friendships routes
Route::get('friends/requests', [\App\Http\Controllers\AcceptFriendshipsController::class, 'index'])->name('accept-friendships.index')->middleware('auth');
Route::post('accept-friendships/{sender}', [\App\Http\Controllers\AcceptFriendshipsController::class, 'store'])->name('accept-friendships.store')->middleware('auth');
Route::delete('accept-friendships/{sender}', [\App\Http\Controllers\AcceptFriendshipsController::class, 'destroy'])->name('accept-friendships.destroy')->middleware('auth');


