<?php

use App\Http\Resources\Recruit as RecruitResource;
use App\PlayerRecruit;
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


Route::get('/', 'CompanyController@index');
Route::get('/debug', 'CompanyController@debug');
Route::get('/update', 'FactionController@index');

Route::get('/recruit', 'RecruitController@index');
