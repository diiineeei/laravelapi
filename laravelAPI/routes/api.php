<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

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

Route::get('/data-import', [APIController::class, 'dataImportAPI']);
Route::get('/employees/{page?}', [APIController::class, 'getEmployees']);
Route::post('/value/{matricula}', [APIController::class, 'updateHourValue']);
Route::post('/hours/{matricula}', [APIController::class, 'storeHours']);
Route::get('/value/{matricula}/{mes}', [APIController::class, 'getValueByMatriculaAndMonth']);

