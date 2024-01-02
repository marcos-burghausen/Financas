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
     * 100 ao 199 -> Login
     * 200 ao 299 -> Usuario
     * 300 ao 399 -> Despesas
     * 400 ao 499 -> Receitas
     * 500 ao 599 -> Categorias
     * ...
     * 
     * podemos ir adicionando conforme adicionamos mais features ao código
     */

    case EXAMPLE_ERROR                  = "SP000";          //Um erro inesperado aconteceu.

    case USER_ALREADY_REGISTERED        = "SP001";          //Usuario já cadastrado.
    case USER_CREATE_FAILED             = "SP002";          //Falha na criação do usuario.

    case INVALID_USERNAME_OR_PASSWORD   = "SP100";          //Usuario ou senha invalidos.

    case ERROR_WHILE_GETTING_USER_DATA  = "SP200";          //Erro ao obter dados do usuario.


    case ERROR_REGISTERING_EXPENSE      = "SP300";          //Erro ao cadastrar despesa.
    case ERROR_DELETING_EXPENSE         = "SP301";          //Erro ao excluir despesa.
    case ERROR_UPDATING_EXPENSE         = "SP302";          //Erro ao atualizar despesa.
    case ERROR_FETCHING_EXPENSE         = "SP303";          //Erro ao buscar despesa.
    case ERROR_PAY_EXPENSE              = "SP304";          //Erro ao pagar despesa.

    case ERROR_REGISTERING_REVENUE      = "SP400";          //Erro ao cadastrar receita.
    case ERROR_DELETING_REVENUE         = "SP401";          //Erro ao excluir receita.
    case ERROR_UPDATING_REVENUE         = "SP402";          //Erro ao atualizar receita.
    case ERROR_FETCHING_REVENUE         = "SP403";          //Erro ao buscar receita.
    case ERROR_PAY_REVENUE              = "SP404";          //Erro ao pagar receita.

    case ERROR_REGISTER_CATEGORY        = "SP500";          //Erro ao registrar categoria.
    case ERROR_DELETE_CATEGORY          = "SP501";          //Erro ao deletar categoria.

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
