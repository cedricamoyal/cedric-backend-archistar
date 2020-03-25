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


// Create the following API routes:

// Add a new property
// POST http://cedric-backend-archistar.test/api/properties?suburb=sub 1&state=state 1&counrty=counrty 1
Route::post('properties', 'PropertyController@store');

// Add/Update an analytic to a property
// PUT http://cedric-backend-archistar.test/api/property/1/analytic/2
Route::put('property/{property}/analytic/{analytic}', 'PropertyController@updateAnalyticToProperty');

// Get all analytics for an inputted property
// GET http://cedric-backend-archistar.test/api/properties/1/analytics
Route::get('properties/{property}/analytics', 'PropertyController@showAnalyticsOfProperty');

// Get a summary of all property analytics for an inputted suburb (min value, max value, median value, percentage properties with a value, percentage properties without a value)
// Get a summary of all property analytics for an inputted state (min value, max value, median value, percentage properties with a value, percentage properties without a value)
// Get a summary of all property analytics for an inputted country (min value, max value, median value, percentage properties with a value, percentage properties without a value)


// Other routes

// GET http://cedric-backend-archistar.test/api/properties
Route::get('properties', 'PropertyController@index');

// GET http://cedric-backend-archistar.test/api/properties/1
Route::get('properties/{property}', 'PropertyController@show');


// GET http://cedric-backend-archistar.test/api/analytic_types
Route::get('analytic_types', 'AnalyticController@index');
