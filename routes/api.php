<?php

use Illuminate\Http\Request;

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

Route::middleware('api')->get('/collection', function () {
    return response()->json(['message' => 'Jobs API', 'status' => 'Connected']);;
});

Route::middleware('api')->get('/collection/{id}', function ($id) {
    return response()->json(['collectionId' => $id, 'status' => 'Connected']);;
});

Route::middleware('api')->get('/anime/{id}', function ($id) {
    return response()->json(['message' => $id, 'status' => 'Connected']);;
});

Route::middleware('api')->get('/playlist/{provider}/{user}', function ($provider, $user) {
    return response()->json(['message' => $id, 'status' => 'Connected']);;
});