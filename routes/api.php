<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
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

// Retorna todas as instituições financeiras
Route::get('/instituicoes', [ApiController::class,'getInstituicoes']);

// Retorna todos os convênios financeiros
Route::get('/convenios', [ApiController::class,'getConvenios']);

// Retorna todos os convênios financeiros
Route::Post('/simulacao', [ApiController::class,'simulacaoCredito']);
