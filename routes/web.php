<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\offerwalls;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('offerwalls/adgatemedia/postback/',[offerwalls::class,'adgatemediapostback']);
Route::get('offerwalls/adgem/',[offerwalls::class,'adgem']);
Route::get('postback/lootably/',[offerwalls::class,'lootably']);
