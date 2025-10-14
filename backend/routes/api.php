<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuscaDadosMesCntroller;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserDataController;
use Illuminate\Support\Facades\Route;

// Route::options('{any?}', function () {
//     return response()->json(['message' => 'OK'], 200, [
//         'Access-Control-Allow-Origin' => 'https://mrfinancas.burghausen.dev',
//         'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
//         'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
//     ]);
// })->where('any', '.*');

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

    // Route::post('/save-expense', [ExpenseController::class, 'saveExpense']);
    // Route::get('/get-expense', [ExpenseController::class, 'getExpense']);
    // Route::post('/pay-expense', [ExpenseController::class, 'payExpense']);
    // Route::post('/edit-expense', [ExpenseController::class, 'editExpense']);
    // Route::post('/delete-expense', [ExpenseController::class, 'deleteExpense']);

    Route::get('/lancamentos', [LancamentoController::class, 'getLancamento']);
    Route::post('/lancamentos', [LancamentoController::class, 'saveLancamento']);
    Route::put('/lancamentos/{id}', [LancamentoController::class, 'editLancamento']);
    Route::patch('/lancamentos/{id}', [LancamentoController::class, 'receivedLancamento']);
    Route::delete('/lancamentos/{id}', [LancamentoController::class, 'deleteLancamento']);

    Route::post('/wallet', [WalletsController::class, 'saveWallet']);
    Route::post('/edit-wallets', [WalletsController::class, 'editWallets']);
    Route::post('/add-wallets', [WalletsController::class, 'addWallets']);
    Route::post('/delete-wallets', [WalletsController::class, 'deletWallets']);
    Route::post('/get-wallets', [WalletsController::class, 'getWallets']);
    Route::get('/contas/{conta}/invoices', [WalletsController::class, 'getInvoices']);

    Route::post('/save-category', [CategoryController::class, 'saveCategory']);
    Route::post('/delete-category', [CategoryController::class, 'deleteCategory']);

    Route::post('/buscar-dados-mes', [BuscaDadosMesCntroller::class, 'buscarDadosMes']);

    // Rotas otimizadas para buscar dados sob demanda
    Route::get('/user-data/expenses', [UserDataController::class, 'getExpenses']);
    Route::get('/user-data/revenues', [UserDataController::class, 'getRevenues']);
    Route::get('/user-data/wallets', [UserDataController::class, 'getWallets']);
    Route::post('/user-data/invalidate-cache', [UserDataController::class, 'invalidateCache']);
});
