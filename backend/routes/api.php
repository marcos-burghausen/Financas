<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuscaDadosMesCntroller;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RevenueController;
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

Route::post('/auth', [AuthController::class, 'auth']);
Route::get('/auth/redirect', [AuthController::class, 'facebookRedirect']);
Route::get('/auth/callback', [AuthController::class, 'callback']);

Route::post('/create', [RegisterController::class, 'create']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('/me', [AuthController::class, 'me']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/save-expense', [ExpenseController::class, 'saveExpense']);
    Route::get('/get-expense', [ExpenseController::class, 'getExpense']);
    Route::post('/pay-expense', [ExpenseController::class, 'payExpense']);
    Route::post('/edit-expense', [ExpenseController::class, 'editExpense']);
    Route::post('/delete-expense', [ExpenseController::class, 'deleteExpense']);

    Route::get('/revenue', [RevenueController::class, 'getRevenue']);
    Route::post('/revenue', [RevenueController::class, 'saveRevenue']);
    Route::put('/revenue/{id}', [RevenueController::class, 'editRevenue']);
    Route::patch('/revenue/{id}/receive', [RevenueController::class, 'receivedRevenue']);
    Route::delete('/revenue/{id}', [RevenueController::class, 'deleteRevenue']);

    Route::post('/save-wallet', [WalletsController::class, 'saveWallet']);
    Route::post('/edit-wallets', [WalletsController::class, 'editWallets']);
    Route::post('/add-wallets', [WalletsController::class, 'addWallets']);
    Route::post('/delete-wallets', [WalletsController::class, 'deletWallets']);
    Route::post('/get-wallets', [WalletsController::class, 'getWallets']);

    Route::post('/save-category', [CategoryController::class, 'saveCategory']);
    Route::post('/delete-category', [CategoryController::class, 'deleteCategory']);

    Route::post('/buscar-dados-mes', [BuscaDadosMesCntroller::class, 'buscarDadosMes']);
});
