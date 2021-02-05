<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/performers',function() {
    $filename = join(DIRECTORY_SEPARATOR,[base_path(),'data.json']);
    $contents = file_get_contents($filename);
    if (request()->expectsJson()) {
        return response()->json(json_decode($contents));
    }
});
