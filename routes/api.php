<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\offerwalls;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\TaskController;

use \Illuminate\Http\Request;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('create_user', [UserController::class, 'create_task_detail']);
Route::post('new_user', [UserController::class, 'new_user']);

Route::post('/userid', [UserController::class, 'getUserId']);
Route::get('/payout', [UserController::class, 'makePayout']);


Route::get('/all_promocodes', [\App\Http\Controllers\PromocodeController::class, 'allpromocodes']);
Route::post('/add_promocode', [\App\Http\Controllers\PromocodeController::class, 'newpromocode']);
Route::get('delete_promocode/{id}', [\App\Http\Controllers\PromocodeController::class, 'deletepromocode']);
Route::post('update_promocode/{id}', [\App\Http\Controllers\PromocodeController::class, 'updatepromocode']);
Route::post('claim_promocode', [\App\Http\Controllers\PromocodeController::class, 'claim_promocode']);

// Tasks 
Route::get('tasks/{user_id}', [offerwalls::class, 'offerwalls_counts']);
Route::get('tasks-today/{user_id}', [TaskController::class, 'getTodayTasks']);
Route::get('tasks-day-by-day/{user_id}', [TaskController::class, 'getTasksDayByDay']);
Route::post('claim_task', [TaskController::class, 'claimTask']);
// Offers
Route::get('offers/{user_id}', [offerwalls::class, 'getAllOffers']);

Route::get('popup_ad/{user_id}', [offerwalls::class, 'popup_ad']);
Route::get('video_ad/{user_id}', [offerwalls::class, 'video_ad']);
Route::get('new_giveaway_entry/{user_id}', [offerwalls::class, 'new_giveaway_entry']);
Route::get('check_giveaway_entry/{user_id}', [offerwalls::class, 'check_giveaway_entry']);
Route::get('giveaway_winner', [offerwalls::class, 'draw_giveaway']);
Route::get('giveaway_winners', [offerwalls::class, 'giveaway_winners']);



Route::get('leaderboard', [\App\Http\Controllers\offerwalls::class, 'leaderboard'])->name('leaderboard');
