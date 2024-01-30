<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\TestTypeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\AuthController;

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

Route::post('login', [AuthController::class,'login']);
Route::post('logout', [AuthController::class,'logout']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('verify_token',[AuthController::class,'verifyToken']);

    Route::group(['middleware' => ['role:superAdmin']], static function () {
        Route::get('statistics',[StatisticsController::class,'prices']);
    });

    Route::apiResource('laboratory',LaboratoryController::class);
    Route::apiResource('test-type',TestTypeController::class);
    Route::post('laboratory/{laboratory}',[LaboratoryController::class,'updateMedia']);
    Route::get('loginCytomine',[LaboratoryController::class,'loginCytomine']);

    Route::apiResource('registration',RegistrationController::class);
});

