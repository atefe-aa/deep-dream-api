<?php

use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\PriceController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\TestTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CounsellorController;
use App\Http\Controllers\Operator\ScanController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('verify_token', [AuthController::class, 'verifyToken']);

    Route::apiResource('test-type', TestTypeController::class);

    Route::apiResource('registration', RegistrationController::class);

    Route::apiResource('counsellor', CounsellorController::class);

    Route::group(['middleware' => ['role:superAdmin']], static function () {
        Route::apiResource('price', PriceController::class);
        Route::apiResource('laboratory', LaboratoryController::class);
        Route::post('laboratory/{laboratory}', [LaboratoryController::class, 'updateMedia']);

        Route::get('statistics', [StatisticsController::class, 'prices']);
    });

    Route::group([
        'middleware' => ['role:superAdmin|operator'],
        'prefix' => 'scan'
    ], static function () {
        Route::post('full-slide', [ScanController::class, 'fullSlide']);
        Route::post('areas', [ScanController::class, 'scanAreas']);
    });

});

