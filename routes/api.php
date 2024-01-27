<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\TestTypeController;
use App\Http\Controllers\RegistrationController;

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

Route::apiResource('laboratory',LaboratoryController::class);
Route::apiResource('test-type',TestTypeController::class);
Route::post('laboratory/{laboratory}',[LaboratoryController::class,'updateMedia']);

Route::apiResource('registration',RegistrationController::class);
