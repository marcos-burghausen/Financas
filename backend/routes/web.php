<?php

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMai;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-env', function () {
    info(env('APP_URL'));
});
Route::get('/teste-email', function () {
    Mail::to('rafaelburghausen@gmail.com')->send(new \App\Mail\NotificationMail(
        (object)['name' => 'Marcos', 'email' => 'rafaelburghausen@gmail.com'],
        'Atualização',
        'Transação',
        'Fatura #123'
    ));

    return 'OK - verifique a caixa de entrada e os Transactional Logs da Brevo';
});

    

// Route::get('/auth/redirect', [AuthController::class, 'redirect'])->name('auth.redirect');
 
// Route::get('/auth/callback', [AuthController::class, 'callback'])->name('auth.callback');

// Route::middleware('jwt.auth')->group(function () {
//     Route::get('/me', [AuthController::class, 'me']);
// });
