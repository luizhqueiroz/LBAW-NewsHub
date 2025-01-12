<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\StaticController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MovieController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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

// User
Route::controller(UserController::class)->group(function () {
    Route::get('/users/{id}', 'show')->name('user.show')->middleware('auth');
    Route::get('/users/{id}/edit', 'edit')->name('user.edit')->middleware('auth');
    Route::put('/users/{id}', 'update')->name('user.update')->middleware('auth');
    Route::delete('/users/{id}', 'destroy')->name('user.delete')->middleware('auth');
});

// News
Route::controller(NewsController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/news/top', 'getTopNews')->name('news.top');
    Route::get('/news/recent', 'getRecentNews')->name('news.recent');
    Route::get('/news/{id}', 'show')->name('news.show');
    Route::post('/news', 'store')->name('news.store')->middleware('userAuth');
    Route::put('/news/{id}', 'update')->name('news.update')->middleware('auth');
    Route::delete('/news/{id}', 'destroy')->name('news.delete')->middleware('auth');
});

// Comment
Route::controller(CommentController::class)->group(function () {
    Route::post('/news/{id}/comments', 'store')->name('comment.store')->middleware('userAuth');
    Route::put('/comments/{id}', 'update')->name('comment.update')->middleware('auth');
    Route::delete('/comments/{id}', 'destroy')->name('comment.delete')->middleware('auth');
});

// Tag
Route::controller(TagsController::class)->group(function () {
    Route::get('/tags', 'showTags')->name('tags.show'); // Display tags page
    Route::post('/tags/toggle-follow/{tagId}', 'toggleFollowTag')->name('tags.toggle-follow');
    Route::post('/tags/ask', 'askTag')->name('tags.ask'); // Ask adm to create a new tag
    Route::post('/tags/create', 'createTag')->name('tags.store'); // Create a new tag
    Route::delete('/tags/delete/{id}', 'deleteTag')->name('tags.delete'); // Delete a tag
    Route::post('/tags/accept/{id}', 'acceptTag')->name('tags.accept'); // Accept tag request
    Route::delete('/tags/reject/{id}', 'rejectTag')->name('tags.reject'); // Reject tag request
});

// Admin
Route::controller(AdminController::class)->group(function () {
    Route::get('/admin/users', 'showUsers')->name('admin.users.show')->middleware('adminAuth');
    Route::get('/admin/{id}', 'show')->name('admin.show')->middleware('adminAuth');
    Route::get('/admin/{id}/edit', 'edit')->name('admin.edit')->middleware('adminAuth');
    Route::put('/admin/{id}', 'update')->name('admin.update')->middleware('adminAuth');
    Route::post('/admin/users', 'storeUser')->name('admin.user.store')->middleware('adminAuth');
    Route::delete('/admin/users/{id}', 'deleteUser')->name('admin.user.delete')->middleware('adminAuth');
    Route::post('/admin/users/{id}/block', 'blockUser')->name('admin.user.block')->middleware('adminAuth');
    Route::post('/admin/users/{id}/unblock', 'unblockUser')->name('admin.user.unblock')->middleware('adminAuth');
});


// Search
Route::controller(SearchController::class)->group(function () {
    Route::get('/search/users', 'searchUser')->name('search.user')->middleware('auth');
    Route::get('/api/news', 'searchNews')->name('api.search.news');
    Route::get('/api/comments', 'searchComments')->name('api.search.comments');
});

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});


Route::controller(MailController::class)->group(function () {
    Route::get('/recover', 'showRecoverForm')->name('recover');
    Route::post('/recover', 'send')->name('send.recover');
});

Route::controller(PasswordResetController ::class)->group(function () {
    Route::get('/password/reset','showResetForm')->name('password.reset');
    Route::post('/password/reset','reset');

});

// Statics
Route::controller(StaticController::class)->group(function () {
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'storeContact')->name('contact.store');
    Route::get('/faq', 'faq')->name('faq');
});

// Like
Route::controller(LikeController::class)->group(function () {
    Route::post('/news/{id}/like', 'toggleNewsLike')->name('news.like')->middleware('userAuth');
    Route::post('/comments/{id}/like', 'toggleCommentLike')->name('comment.like')->middleware('userAuth');
});

// Follows
Route::controller(FollowController::class)->group(function () {
    Route::get('/users/{user}/follows', 'showFollows')->name('users.follows')->middleware('auth');
    Route::post('/users/{user}/follow', 'followUser')->name('users.follow')->middleware('userAuth');
    Route::post('/users/{user}/unfollow', 'unfollowUser')->name('users.unfollow')->middleware('userAuth');
});

// Notifications
Route::controller(NotificationController::class)->group(function () {
    Route::get('/notifications', 'index')->name('notifications.index')->middleware('userAuth');
    Route::post('/notifications/{id}/read', 'markAsRead')->name('notifications.read')->middleware('userAuth');
    Route::delete('/notifications/{id}', 'destroy')->name('notifications.delete')->middleware('userAuth');
    Route::get('/notifications/data', 'userData')->name('notifications.data')->middleware('userAuth');
});

// Movies
Route::controller(MovieController::class)->group(function () {
    Route::get('/movies/search', 'search')->name('movies.search');
    Route::get('/movies/trending', 'trending')->name('movies.trending');
    Route::get('/movies/{id}', 'details')->name('movies.details');
});