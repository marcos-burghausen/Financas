<?php

namespace App\Http\Controllers;

use App\Enums\Actions;
use App\Enums\CacheKeys;
use App\Enums\CacheNaming;
use App\Enums\Errors;
use App\Http\Traits\GroupReleasesTrait;
use App\Http\Traits\ReleasesMonthTrait;
use App\Http\Traits\TotalByCategoryTrait;
use App\Utils\FinancasCache;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    use ReleasesMonthTrait, GroupReleasesTrait, TotalByCategoryTrait;

    public function auth(Request $request)
    {
        $request->validate(
            [
                'email' => [
                    'required',
                    'email'
                ],
                'password' => [
                    'required',
                ],
            ],
            [
                'required'      => 'O campo :attribute é obrigatório',
                'email.email'         => 'O email precisa ter um formato de email válido',
                // 'password.required'   => 'O campo senha é obrigatório',
            ],
            [
                'password' => 'senha'
            ]
        );
        $credentials = $request->all(['email', 'password']);

        //autenticação (email e senha)
        $token = auth('api')->attempt($credentials);
        if (!$token) {
            LogController::addsLog($request->email, Actions::USER_OR_PASSWORD_INVALID);
            return Errors::INVALID_USERNAME_OR_PASSWORD->response();
        }

        //usuário autenticado com sucesso
        /* O metodo put espera 3 valores
            o primeiro valor é o valor da chave como se fosse o nome de uma variavel
            o segundo valor é valor da chave
            o terceiro é o tempo em segundos que a informação vai permanecer no banco
        */
        FinancasCache::put(CacheKeys::FLOW_TITLE->append($request->email), [
            CacheNaming::EMAIL->value => $request->email,
        ], 30);

        $token = $this->respondWithToken($token);

        LogController::addsLog($request->email, Actions::LOGIN);

        return response()->json($token->original);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        if ($user = auth()->user()) {

            $expenses = auth()->user()->expenses()->get();
            $expensesMonth = $this->releasesMonth($expenses, date('m'));
            $valuePayExpenses = $this->valuePending($expenses, date('m'), "PAGA");
            $valuePendingExpenses = $this->valuePending($expenses, date('m'), "AGUARDANDO");
            $valueTotalExpensesMonth = $this->valueReleasesMonth($expenses, date('m'));
            $expensesGroupByMonth = $this->groupByMonth($expenses);
            $expensesAddTotalVelueMonth = $this->addTotalValueMonth($expensesGroupByMonth);
            $totalByCategoryExpnses = $this->totalByCategory($expenses);
            $expensesData = [
                'expenses' => $expenses,
                'expensesMonth' => $expensesMonth,
                'valuePayExpenses' => $valuePayExpenses,
                'valuePendingExpenses' => $valuePendingExpenses,
                'valueTotalExpensesMonth' => $valueTotalExpensesMonth,
                'expensesGroupByMonth' => $expensesGroupByMonth,
                'expensesAddTotalVelueMonth' => $expensesAddTotalVelueMonth,
                'totalByCategoryExpnses' => $totalByCategoryExpnses,
            ];

            $revenues = auth()->user()->revenues()->get();
            $revenuesMonth = $this->releasesMonth($revenues, date('m'));
            $valueReceived = $this->valuePending($revenues, date('m'), "RECEBIDA");
            $valuePendingRevenues = $this->valuePending($revenues, date('m'), "AGUARDANDO");
            $valueTotalRevenuesMonth = $this->valueReleasesMonth($revenues, date('m'));
            $revenuesGroupByMonth = $this->groupByMonth($revenues);
            $revenuesAddTotalVelueMonth = $this->addTotalValueMonth($revenuesGroupByMonth);
            $revenuesData = [
                'revenues' => $revenues,
                'revenuesMonth' => $revenuesMonth,
                'valueReceivedRevenues' => $valueReceived,
                'valuePendingRevenues' => $valuePendingRevenues,
                'valueTotalRevenuesMonth' => $valueTotalRevenuesMonth,
                'revenuesGroupByMonth' => $revenuesGroupByMonth,
                'revenuesAddTotalVelueMonth' => $revenuesAddTotalVelueMonth,
            ];

            $totalCreditCard = 5000;
            $totalBalance = 5000;

            LogController::addsLog($user->email, Actions::ME);



            return response()->json([
                'user' => $user,
                'expensesData' => $expensesData,
                'revenuesData' => $revenuesData,
                'totalBalance'  => $totalBalance,
                'totalCreditCard'  => $totalCreditCard,

            ]);
        }

        return Errors::ERROR_WHILE_GETTING_USER_DATA->response();
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['msg' => 'Logout foi realizado com sucesso!']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $new_token = auth('api')->refresh();
        LogController::addsLog(auth()->user()->email, Actions::REFRESH_TOKEN);
        return $this->respondWithToken($new_token);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 30
        ], 200);
    }
}
