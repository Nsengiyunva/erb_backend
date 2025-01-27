<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\EngineersController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\OtherController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([ 'middleware' => [ 'api', 'cors' ],'prefix' => 'auth' ], function ($router) {
  Route::post('/test', [OtherController::class, "index" ] );   
  
  Route::post( "erb_loginUser", [EngineersController::class, 'loginUser'] );
  Route::post( "erb_storeUser", [EngineersController::class, 'storeUser'] );
  Route::post( "erb_storeLicence", [EngineersController::class, 'storeLicence'] );
  Route::get(  "erb_getLicences", [EngineersController::class, 'getLicences'] );
  Route::get(  "erb_getLicenceById/{id}", [EngineersController::class, 'getLicenceById'] );
  Route::get(  "erb_getRemarks/{licenceId}", [EngineersController::class, 'getLicenceRemarks'] );
  Route::post( "erb_update", [EngineersController::class, 'updateLicence'] );
  Route::post( "erb-success-payment", [EngineersController::class, 'paymentSuccess'] );
  Route::get( "erb-getSponsors", [EngineersController::class, 'getSponsorsByUser'] );
  Route::post( "erb_updateSponsor", [EngineersController::class, 'updateSponsor'] );
  Route::get( "erb_engineers/{category}", [EngineersController::class,'fetchErbEngineers'] ); 
  Route::get( "erb_registered", [EngineersController::class, 'fetchEngineers'] );
  Route::get( "erb_users", [EngineersController::class, 'fetchRegistered'] );
} );

Route::post("/payments/callback",[PaymentsController::class, 'callback'] )->name("payments.callback");










