<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\EngineersController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\OtherController;
use App\Http\Controllers\FileController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([ 'middleware' => [ 'api', 'cors' ],'prefix' => 'auth' ], function ($router) {
  Route::post('test', [EngineersController::class, "sendmail" ] );   
  
  Route::post( "erb_loginUser", [EngineersController::class, 'loginUser'] );
  Route::post( "erb_storeUser", [EngineersController::class, 'storeUser'] );
  Route::get( "getAllUsers", [EngineersController::class, 'getAllUsers'] );
  Route::post( "erb_storeLicence", [EngineersController::class, 'storeLicence'] );
  Route::get(  "erb_getLicences", [EngineersController::class, 'getLicences'] );
  Route::get(  "erb_getLicenceById/{id}", [EngineersController::class, 'getLicenceById'] );
  Route::get(  "erb_getRemarks/{licenceId}", [EngineersController::class, 'getLicenceRemarks'] );
  Route::post( "erb_update", [EngineersController::class, 'updateLicence'] );
  Route::post( "erb_update_accounts", [EngineersController::class, 'updateLicencePayment']);
  Route::post( "erb-success-payment", [EngineersController::class, 'paymentSuccess'] );
  Route::get( "erb-getSponsors", [EngineersController::class, 'getSponsorsByUser'] );
  Route::post( "erb_updateSponsor", [EngineersController::class, 'updateSponsor'] );
  Route::get( "erb_engineers/{category}", [EngineersController::class,'fetchErbEngineers'] ); 
  Route::get( "erb_registered", [EngineersController::class, 'fetchEngineers'] );
  Route::get("/payments/{id}",[PaymentsController::class, 'show'] );
  Route::get("/payments",[PaymentsController::class, 'fetchAllPayments'] );
  Route::post( "send-email-notification", [ EngineersController::class, 'sendmail'] );
  
  Route::post('send-otp', [ OTPController::class, 'sendOTP' ]);
  Route::post('verify-otp', [ OTPController::class, 'verifyOTP' ]);

  Route::post( "upload", [FileController::class, "upload" ] );
  Route::post( "save-draft", [EngineersController::class, "storeDraft" ] );
  Route::get( "drafts", [EngineersController::class, "fetchDrafts"] );
  Route::get( "drafts/:id", [EngineersController::class, "getDrafts" ] );
} );

Route::post("/payments/callback",[PaymentsController::class, 'callback'] )->name("payments.callback");










