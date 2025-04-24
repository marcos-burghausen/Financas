<?php

namespace App\Http\Controllers;

use App\Enums\Actions;
use App\Enums\CacheKeys;
use App\Enums\CacheNaming;
use App\Enums\Errors;
use App\Http\Traits\GroupReleasesTrait;
use App\Http\Traits\ReleasesMonthTrait;
use App\Http\Traits\TotalByCategoryTrait;
use App\Mail\NotificationMail;
use App\Models\Conta;
use App\Models\User;
use App\Utils\FinancasCache;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ReleasesMonthTrait;

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
                'email.email'         => 'O email precisa ter um formato de válido',
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
        $user = auth()->user();
        $data = $this->getUserData($user);

        LogController::addsLog($request->email, Actions::LOGIN);

        Mail::to('rafaelburghausen@gmail.com')->queue(new NotificationMail($user, 'Login', 'Login', $user->name));

        return response()->json([
            'token'     => $token->original,
            'userData'  => $user = [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'type'  => $user->user_type,
            ],
            'data' => $data
        ]);
    }

    public function authSocial(Request $request)
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();

            // Procura o usuário pelo email ou cria um novo
            $authUser = User::firstOrCreate(
                ['email' => $user->email],
                [
                    'name' => $user->name,
                    'facebook_id' => $user->id,
                    // Outros campos que você queira preencher
                ]
            );

            // Gera o token JWT
            $token = auth('api')->login($authUser);

            if (!$token) {
                LogController::addsLog($user->email, Actions::SOCIAL_AUTH_FAILED);
                return Errors::AUTHENTICATION_FAILED->response();
            }

            // Cache e log (similar ao seu método original)
            FinancasCache::put(CacheKeys::FLOW_TITLE->append($user->email), [
                CacheNaming::EMAIL->value => $user->email,
            ], 30);

            $tokenResponse = $this->respondWithToken($token);

            LogController::addsLog($user->email, Actions::SOCIAL_LOGIN);

            return response()->json($tokenResponse->original);
        } catch (\Exception $e) {
            // Log do erro e retorno de uma resposta de erro
            LogController::addsLog('unknown', Actions::SOCIAL_AUTH_ERROR);
            return Errors::AUTHENTICATION_FAILED->response();
        }
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

            $revenuesData = $this->classifiesReleases(auth()->user()->revenues()->get(), 'Revenues');
            $expensesData = $this->classifiesReleases(auth()->user()->expenses()->get(), 'Expenses');
            // $walletsNames = Conta::pluck('name')->toArray();
            // $walletsData = Conta::select('name', 'saldo')->get();
            $walletsData = auth()->user()->contas()->get();
            $walletsName = $walletsData->pluck('name');
            $user['walletsName'] = $walletsName;
            $wallets = [];
            foreach ($walletsData as $wallet) {
                $wallets[$wallet['name']] = [
                    'name' => $wallet['name'],
                    'valor' => $wallet['valor'],
                    'icon' => $wallet['icon'],
                    'tipo' => $wallet['tipo'],
                ];
            }
            // return response(['revenuesData' => $revenuesData, 'expensesData' => $expensesData]);


            $totalCreditCard = 5000;
            $totalBalance = 5000;

            LogController::addsLog($user->email, Actions::ME);

            return response()->json([
                'user' => $user,
                'expensesData' => $expensesData,
                'revenuesData' => $revenuesData,
                'wallets' => $wallets,
                // 'totalBalance'  => $totalBalance,
                // 'totalCreditCard'  => $totalCreditCard,

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
            'expires_in' => auth()->factory()->getTTL() * 30 * 2,
            'iat' => time(),
            'expires' => time() + 30 * 60,
        ], 200);
    }

    public function facebookRedirect()
    {
        $redirectUrl = Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl();
        return response()->json(['redirect_url' => $redirectUrl]);
    }

    public function callback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            DB::beginTransaction();
            $user = User::firstOrCreate(
                ['email' => $facebookUser->email],
                [
                    'name' => $facebookUser->name,
                    'facebook_id' => $facebookUser->id,
                    'categoriasDespesas' => null,  // Isso acionará o mutator
                    'categoriasReceitas' => null,  // Isso acionará o mutator
                ]
            );
            if ($user->wasRecentlyCreated) {
                $carteira            = new Conta;
                $carteira->user_id   = $user->id;
                $carteira->name      = "Pessoal";
                $carteira->icon      = "cash";
                $carteira->descricao = "Carteira de uso pessoal";
                $carteira->tipo      = "Pessoal";
                $carteira->save();
            }
            DB::commit();

            // Se o usuário já existia, atualize apenas o facebook_id se necessário
            if ($user->wasRecentlyCreated == false && !$user->facebook_id) {
                $user->facebook_id = $facebookUser->id;
                $user->save();
            }

            // Gera o token JWT sem necessidade de senha
            $token = auth('api')->login($user);
            $userData = $this->getUserData($user);

            // Cache e log (similar ao seu método original)
            FinancasCache::put(CacheKeys::FLOW_TITLE->append($user->email), [
                CacheNaming::EMAIL->value => $user->email,
            ], 30);

            $token = $this->respondWithToken($token);

            LogController::addsLog($user->email, Actions::SOCIAL_LOGIN);
            return response()->json([
                'token' => $token->original,
                'user'  => $user,
                'userData' => $userData
            ]);
        } catch (\Throwable $th) {
            Log::error('Erro na autenticação social: ' . $th->getMessage());
            LogController::addsLog('unknown', Actions::SOCIAL_AUTH_ERROR);
            return Errors::SOCIAL_AUTHENTICATION_FAILED->response();
        }
    }

    private function getUserData($user)
    {
        $categoriasDespesas = $user->categories()->whereIn('type', ['ambas', 'despesa'])->get(['id', 'name', 'color', 'icon', 'editable', 'type']);
        $subcategoriasDespesas = $user->subcategories()->whereIn('type', ['ambas', 'despesa'])->get(['id', 'category_id', 'name', 'color', 'icon', 'editable', 'type']);
        foreach ($categoriasDespesas as $categoria) {
            $categoria->subcategories = [];
            foreach ($subcategoriasDespesas as $subcategoria) {
                if ($categoria->id == $subcategoria->category_id) {
                    $subcategoriaData[] = [
                        'id' => $subcategoria->id,
                        'name' => $subcategoria->name,
                        'color' => $subcategoria->color,
                        'icon' => $subcategoria->icon,
                        'editable' => $subcategoria->editable,
                        'type' => $subcategoria->type
                    ];
                    $categoria->subcategories = $subcategoriaData;
                }
            }
        }

        $categoriasReceitas = $user->categories()->whereIn('type', ['ambas', 'receita'])->get(['id', 'name', 'color', 'icon', 'editable', 'type']);
        $subcategoriasReceitas = $user->subcategories()->whereIn('type', ['ambas', 'receita'])->get(['id', 'category_id', 'name', 'color', 'icon', 'editable', 'type']);
        foreach ($categoriasReceitas as $categoria) {
            $categoria->subcategories = [];
            foreach ($subcategoriasReceitas as $subcategoria) {
                if ($categoria->id == $subcategoria->category_id) {
                    $subcategoriaData = [
                        'id' => $subcategoria->id,
                        'name' => $subcategoria->name,
                        'color' => $subcategoria->color,
                        'icon' => $subcategoria->icon,
                        'editable' => $subcategoria->editable,
                        'type' => $subcategoria->type
                    ];
                    $categoria->subcategories = $subcategoriaData;
                }
            }
        }



        return [
            'expenses' => [
                ...$this->classifiesReleases($user->expenses()->get(), 'Expenses'),
                "categories" => [
                    ...$categoriasDespesas,
                ],
            ],
            "revenues" => [
                ...$this->classifiesReleases($user->revenues()->get(), 'Revenues'),
                "categories" => [
                    ...$categoriasReceitas,
                ],
            ],
            'wallets'         => [
                'mes_ano_referencia' => date('Y-m'),
                'contas'            => $user->contas()->get(['id', 'name', 'icon', 'saldo', 'saldo_inicial', 'descricao', 'tipo', 'incluir_em_soma_inicial']),
                'contasNames'       => $user->contas()->pluck("name"),
                'saldoInicial'       => $this->obterSaldoInicial($user),
                // 'saldoAtual' => $this->obterSaldoAtual($user),
                "categorias" => [
                    ...$user->categories()->where('type', 'contas')->get(['id', 'name', 'color', 'icon', 'editable', 'type']),
                ],
            ],
        ];
    }
}

// Grupo 1
// Sidney Rodrigo Klock
// Joel Fabiano Hansen
// Gilmar Rodrigo Knorst
// Luciane Schumacher
// Grupo 2
// Felipe Augusto da Silva
// Matheus Vogel de Faria
// Marcelo Felipe Wolf
// Grasiela Cristina Faccin
// Grupo 3
// Felipe Wunder
// Matheus Dresch
// Andrei Martini
// Viliam Gabriel Metz
// Grupo 4
// Simone Thomazin Pereira
// Vinícius Marcelo Becker
// Moises Weber
// Daniel Rodrigo Klauck
// Renan Felipe Schuck
// Grupo 5
// Thiago Patzdorf Sleman
// Jessica Camila Saldivia Bueno
// Marcos Rafael Burghausen
// Gean Rafael Spiering

// import pandas as pd
// df_pessoas = pd.read_parquet('pessoas.parquet')
// df_pessoas.head()
// df_contas = pd.read_parquet('contas.parquet')
// df_contas.head()
// df = df_pessoas.merge(df_contas, how='left', left_on='cpf_cnpj', right_on='cpf_cnpj')
// df.head()

// Quantos associados existem no total e qual o percentual de correntistas?
//  Objetivo: validar a leitura da base e a contagem de registros com filtros simples.


// Quantos associados possuem cônjuge registrado e qual a média de contas que eles têm?
//  Objetivo: praticar o uso de joins e agregações com múltiplas tabelas.


// Qual a distribuição dos associados por faixa de risco?
//  Objetivo: aplicar contagem e agrupamento por categoria.


// Qual a média e o desvio padrão do indicador ISA e do MC por faixa de
