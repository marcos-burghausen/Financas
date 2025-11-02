<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SanctumAuthController;
use App\Http\Controllers\BuscaDadosMesCntroller;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationSettingsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\ManutencaoController;
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

// ============================================================================
// NOVAS ROTAS SANCTUM (usar estas)
// ============================================================================
// Rota pública de login (FORA do middleware auth:sanctum)
Route::post('/login', [SanctumAuthController::class, 'login']);
Route::post('/sanctum/login', [SanctumAuthController::class, 'login']); // Alias para compatibilidade

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sanctum/me', [SanctumAuthController::class, 'me']);
    Route::post('/sanctum/logout', [SanctumAuthController::class, 'logout']);
    Route::post('/sanctum/logout-all', [SanctumAuthController::class, 'logoutAll']);

    // Rotas protegidas
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);

    Route::post('/lancamentos', [LancamentoController::class, 'saveLancamento']);
    Route::get('/lancamentos', [LancamentoController::class, 'getLancamento']);
    Route::put('/lancamentos/{id}', [LancamentoController::class, 'editLancamento']);
    Route::patch('/lancamentos/{id}', [LancamentoController::class, 'receivedLancamento']);
    Route::delete('/lancamentos/{id}', [LancamentoController::class, 'deleteLancamento']);

    Route::post('/wallet', [WalletsController::class, 'saveWallet']);
    Route::get('/wallet', [WalletsController::class, 'getWallets']);
    Route::post('/edit-wallets', [WalletsController::class, 'editWallets']);
    Route::post('/add-wallets', [WalletsController::class, 'addWallets']);
    Route::post('/delete-wallets', [WalletsController::class, 'deletWallets']);
    Route::post('/get-wallets', [WalletsController::class, 'getWallets']);
    Route::get('/contas/{conta}/invoices', [WalletsController::class, 'getInvoices']);

    Route::post('/save-category', [CategoryController::class, 'saveCategory']);
    Route::post('/delete-category', [CategoryController::class, 'deleteCategory']);

    Route::post('/buscar-dados-mes', [BuscaDadosMesCntroller::class, 'buscarDadosMes']);

    // Rotas de perfil do usuário
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::get('/user/stats', [UserController::class, 'getStats']);

    // Rotas otimizadas para buscar dados sob demanda
    Route::get('/user-data/expenses', [UserDataController::class, 'getExpenses']);
    Route::get('/user-data/revenues', [UserDataController::class, 'getRevenues']);
    Route::get('/user-data/wallets', [UserDataController::class, 'getWallets']);
    Route::post('/user-data/invalidate-cache', [UserDataController::class, 'invalidateCache']);

    // Rotas de notificações
    Route::get('/notification-settings', [NotificationSettingsController::class, 'show']);
    Route::put('/notification-settings', [NotificationSettingsController::class, 'update']);
    Route::post('/notification-settings/test-vencimento', [NotificationSettingsController::class, 'testVencimento']);
    Route::post('/notification-settings/test-limite-cartao', [NotificationSettingsController::class, 'testLimiteCartao']);
    Route::post('/notification-settings/test-estorno', [NotificationSettingsController::class, 'testEstorno']);
    Route::get('/notification-settings/stats', [NotificationSettingsController::class, 'stats']);

    // Rotas de Veículos
    Route::apiResource('veiculos', VeiculoController::class);
    Route::apiResource('manutencoes', ManutencaoController::class)->parameters([
        'manutencoes' => 'manutencao'
    ]);

    // Rotas de roles e permissões
    Route::get('/roles', [RoleController::class, 'index']); // Listar todas as roles
    Route::get('/me/permissions', [RoleController::class, 'myPermissions']); // Minhas permissões
    Route::get('/users/{user}/roles', [RoleController::class, 'userRoles'])->middleware('role:ADMIN,FULL'); // Ver roles de um usuário
    Route::post('/users/{user}/roles', [RoleController::class, 'assignToUser'])->middleware('role:ADMIN,FULL'); // Atribuir roles
    Route::delete('/users/{user}/roles', [RoleController::class, 'removeFromUser'])->middleware('role:ADMIN,FULL'); // Remover role

    // Rotas de administração
    Route::middleware('role:ADMIN,FULL')->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'listUsers']); // Listar todos os usuários
        Route::get('/stats', [AdminController::class, 'getStats']); // Estatísticas do sistema
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus']); // Ativar/desativar usuário
        Route::put('/users/{user}', [AdminController::class, 'updateUser']); // Atualizar usuário
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser']); // Deletar usuário
        Route::get('/activity-logs', [AdminController::class, 'getActivityLogs']); // Logs de atividades
    });
});

// ============================================================================
// ROTAS JWT ANTIGAS (manter temporariamente para compatibilidade)
// Remover depois que frontend estiver usando Sanctum
// ============================================================================
Route::post('/auth', [AuthController::class, 'auth']);
Route::get('/auth/redirect', [AuthController::class, 'facebookRedirect']);
Route::get('/auth/callback', [AuthController::class, 'callback']);

Route::middleware(['jwt.debug', 'jwt.auth'])->group(function () {
    Route::post('/me', [AuthController::class, 'me']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
