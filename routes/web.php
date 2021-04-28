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

/* / */
Route::get('/', 'CompanyController@index');
Route::get('/debug', 'CompanyController@debug');
Route::get('/update', 'FactionController@index');

/* /recruit/ */
Route::get('/recruit', 'RecruitController@index');
Route::get('/recruit/update', 'RecruitController@batch');

/* /time/ */
Route::get('/time', 'TimeController@index');

//TODO: Move to API.php routes
Route::get('/time/get_times', 'TimeController@getTimes');

Route::post('/time', 'TimeController@store');

//TODO: Move to API.php routes
Route::post('/time/api_key', 'PlayerController@addApiKey');

//TODO: Move to API.php routes
Route::delete('/time', 'TimeController@destroy');
Route::delete('/time/destroyAll', 'TimeController@destroyAll');
