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
     * 001 ao 099 -> Cadastro
     * 100 ao 199 -> Tokens
     * 200 ao 299 -> Login
     * 300 ao 399 -> senha
     * 400 ao 499 -> despesas
     * 500 ao 599 -> receitas
     * ...
     * 
     * podemos ir adicionando conforme adicionamos mais features ao código
     */

    case EXAMPLE_ERROR                  = "SP000";

    case USER_ALREADY_REGISTERED        = "SP001";          //usuario ja cadastrado
    case ERROR_WHILE_GETTING_USER_DATA  = "SP002";          //erro ao obter dados do usuario
        // case USER_CREATE_FAILED             = "SP002";          //falha na criação do usuario

    case INVALID_USERNAME_OR_PASSWORD   = "SP200";          //usuario ou senha invalidos


    case ERROR_REGISTERING_EXPENSE      = "SP400";          //erro ao cadastrar despesa
    case ERROR_DELETING_EXPENSE         = "SP401";          //erro ao excluir despesa
    case ERROR_UPDATING_EXPENSE         = "SP402";          //erro ao atualizar despesa
    case ERROR_FETCHING_EXPENSE         = "SP403";          //erro ao buscar despesa
    case ERROR_PAY_EXPENSE              = "SP404";          //erro ao pagar despesa

    case ERROR_REGISTERING_REVENUE      = "SP500";          //erro ao cadastrar receita
    case ERROR_DELETING_REVENUE         = "SP501";          //erro ao excluir receita
    case ERROR_UPDATING_REVENUE         = "SP502";          //erro ao atualizar receita
    case ERROR_FETCHING_REVENUE         = "SP503";          //erro ao buscar receita
    case ERROR_PAY_REVENUE              = "SP504";          //erro ao pagar receita

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
