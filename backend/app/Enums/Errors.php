<?php

namespace App\Enums;

enum Errors: string
{
    /**
     * As Enums devem seguir esse padrão:
     * case NOME_DO_ERRO = "SP000"
     * 
     * Devem estar ordenadas conforme o SP000 e de pereferência estarem agrupadas de acordo 
     * com o escopo delas, por exemplo as de cadastro devem estar todas uma em baixo da outra 
     * e não uma no início das enums e a outra no fim.
     * 
     * Ranges das enums:
     * 
     * 001 ao 100 -> Cadastro
     * 101 ao 200 -> Tokens
     * 201 ao 300 -> Login
     * 401 ao 500 -> senha
     * ...
     * 
     * podemos ir adicionando conforme adicionamos mais features ao código
     */

    case EXAMPLE_ERROR = "SP000";

    case USER_ALREADY_REGISTERED = "SP001";
    case USER_NOT_FOUND = "SP002";
    case ACTION_AVAILABLE_ONLY_FOR_ACTIVE_ACCOUNTS = "SP003";
    case ERROR_WHILE_GETTING_USER_DATA = "SP004";
    case ERROR_WHILE_GETTING_USER_AGENCIA = "SP005";
    case USER_DOES_NOT_HAVE_CELL_PHONE_AND_EMAIL_REGISTERED = "SP006";
    case USER_CREATE_FAILED = "SP007";
    case IMAGE_STORE_FAILED = "SP008";
    case USER_NOT_REGISTERED = "SP009";

    case TOKEN_SEND_FAILURE = "SP101";
    case TOKEN_RESEND_FAILURE = "SP102";
    case TOKEN_MAX_RESENDS_REACHED = "SP103";
    case TOKEN_MAX_ATTEMPTS_REACHED = "SP104";
    case TOKEN_EXPIRED = "SP105";
    case TOKEN_INCORRECT = "SP106";
    case TOKEN_BLOCKED = "SP107";
    case TOKEN_NOT_VERIFIED = "SP108";

    case USER_DATA_EXPIRED = "SP301";
    case USER_DATA_FETCH_FAIL = "SP302";
    case USER_BANK_BRANCH_FETCH_FAIL = "SP303";

    case PASSWORD_INCORRECT = "SP401";
    case PASSWORD_ALREADY_USED = "SP402";

    /**
     * This method returns a Laravel Response, so you don't need to duplicate code
     * along the application
     */
    public function response(array $content = [], int $http = 409)
    {
        return response([
            'error_code' => $this->value,
            ...$content
        ], $http);
    }
}
