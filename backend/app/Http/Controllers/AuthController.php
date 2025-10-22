<?php

namespace App\Http\Controllers;

use App\Enums\Actions;
use App\Enums\CacheKeys;
use App\Enums\CacheNaming;
use App\Enums\Errors;
use App\Http\Requests\AuthRequest;
use App\Http\Traits\ReleasesMonthTrait;
use App\Http\Traits\UserDataTrait;
use App\Mail\NotificationMail;
use App\Models\Conta;
use App\Models\User;
use App\Utils\FinancasCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ReleasesMonthTrait, UserDataTrait;

    public function auth(AuthRequest $request)
    {
        $data = $request->validated();

        $credentials = $data->only(['email', 'password']);

        info($credentials);
        //autenticação (email e senha)
        $token = auth('api')->attempt($credentials);
        if (!$token) {
            LogController::addsLog($request->email, Actions::USER_OR_PASSWORD_INVALID);
            return Errors::INVALID_USERNAME_OR_PASSWORD->response();
        }

        //usuário autenticado com sucesso
        FinancasCache::put(CacheKeys::FLOW_TITLE->append($data->email), [
            CacheNaming::EMAIL->value => $data->email,
        ], 30);

        $token = $this->respondWithToken($token);
        $user = auth()->user();
        $mesAno = $data->query('mesAno', now()->format('Y-m'));

        // Cache por 10 minutos - Usando getUserData granular para buscar apenas o necessário
        $cacheKey = "login_data_user_{$user->id}_month_{$mesAno}";
        $loginData = cache()->remember($cacheKey, 600, function () use ($user, $mesAno, $token) {
            // Busca apenas o resumo do dashboard no login (performance)
            $userData = $this->getUserData($user, $mesAno, ['summary']);

            return [
                'token' => $token->original,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->user_type,
                ],
                'mesAno' => $mesAno,
                'summary' => $userData['summary'],
            ];
        });

        LogController::addsLog($data->email, Actions::LOGIN);
        Mail::to('rafaelburghausen@gmail.com')->queue(new NotificationMail($user, 'Login', 'Login', $user->name));

        return response()->json($loginData);
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
     * Retorna dados completos do usuário autenticado.
     * Usa getUserData granular para buscar todas as seções necessárias.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();

        if (!$user) {
            return Errors::ERROR_WHILE_GETTING_USER_DATA->response();
        }

        // Busca todos os dados do usuário usando a função granular
        // Se não passar seções, retorna tudo
        $userData = $this->getUserData($user);

        LogController::addsLog($user->email, Actions::ME);

        return response()->json($userData);
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
            'tokenType' => 'bearer',
            'expiresIn' => auth()->factory()->getTTL() * 30 * 2,
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
                $carteira->tipoConta      = "Pessoal";
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

            // Busca dados completos do usuário usando getUserData granular
            $userData = $this->getUserData($user);

            // Cache e log (similar ao seu método original)
            FinancasCache::put(CacheKeys::FLOW_TITLE->append($user->email), [
                CacheNaming::EMAIL->value => $user->email,
            ], 30);

            $token = $this->respondWithToken($token);

            LogController::addsLog($user->email, Actions::SOCIAL_LOGIN);

            return response()->json([
                'token' => $token->original,
                'user'  => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->user_type,
                ],
                'userData' => $userData
            ]);
        } catch (\Throwable $th) {
            Log::error('Erro na autenticação social: ' . $th->getMessage());
            LogController::addsLog('unknown', Actions::SOCIAL_AUTH_ERROR);
            return Errors::SOCIAL_AUTHENTICATION_FAILED->response();
        }
    }

    // private function getUserData($user)
    // {
    //     // Expenses (Despesas)
    //     $categoriasDespesas = $user->categories()->whereIn('type', ['ambas', 'despesa'])->get(['id', 'name', 'color', 'icon', 'editable', 'type']);
    //     $subcategoriasDespesas = $user->subcategories()->whereIn('type', ['ambas', 'despesa'])->get(['id', 'category_id', 'name', 'color', 'icon', 'editable', 'type']);
    //     foreach ($categoriasDespesas as $categoria) {
    //         $subcategories = []; // Temporary array for subcategories
    //         foreach ($subcategoriasDespesas as $subcategoria) {
    //             if ($categoria->id == $subcategoria->category_id) {
    //                 $subcategories[] = [
    //                     'id' => $subcategoria->id,
    //                     'name' => $subcategoria->name,
    //                     'color' => $subcategoria->color,
    //                     'icon' => $subcategoria->icon,
    //                     'editable' => $subcategoria->editable,
    //                     'type' => $subcategoria->type
    //                 ];
    //             }
    //         }
    //         $categoria->subcategories = $subcategories; // Assign to a custom attribute
    //     }

    //     // Revenues (Receitas)
    //     $categoriasReceitas = $user->categories()->whereIn('type', ['ambas', 'receita'])->get(['id', 'name', 'color', 'icon', 'editable', 'type']);
    //     $subcategoriasReceitas = $user->subcategories()->whereIn('type', ['ambas', 'receita'])->get(['id', 'category_id', 'name', 'color', 'icon', 'editable', 'type']);
    //     foreach ($categoriasReceitas as $categoria) {
    //         $subcategories = []; // Temporary array for subcategories
    //         foreach ($subcategoriasReceitas as $subcategoria) {
    //             if ($categoria->id == $subcategoria->category_id) {
    //                 $subcategories[] = [
    //                     'id' => $subcategoria->id,
    //                     'name' => $subcategoria->name,
    //                     'color' => $subcategoria->color,
    //                     'icon' => $subcategoria->icon,
    //                     'editable' => $subcategoria->editable,
    //                     'type' => $subcategoria->type
    //                 ];
    //             }
    //         }
    //         $categoria->subcategories = $subcategories; // Assign to a custom attribute
    //     }

    //     return [
    //         'expenses' => [
    //             ...$this->classifiesReleases($user->expenses()->get(), 'Expenses'),
    //             "categories" => [
    //                 ...$categoriasDespesas,
    //             ],
    //         ],
    //         "revenues" => [
    //             ...$this->classifiesReleases($user->revenues()->get(), 'Revenues'),
    //             "categories" => [
    //                 ...$categoriasReceitas,
    //             ],
    //         ],
    //         'wallets'         => [
    //             'contas'             => $user->contas()->get(['id', 'name', 'icon', 'saldo', 'saldoInicial', 'descricao', 'tipo', 'incluirEmSomaInicial']),
    //             'contasNames'       => $user->contas()->pluck("name"),
    //             'saldoInicial'       => $this->obterSaldoInicial($user),
    //             // 'saldoAtual' => $this->obterSaldoAtual($user),
    //             "categories" => [
    //                 ...$user->categories()->where('type', 'contas')->get(['id', 'name', 'color', 'icon', 'editable', 'type']),
    //             ],
    //         ],
    //         'mesAno' => date('Y-m'),
    //     ];
    // }
}
