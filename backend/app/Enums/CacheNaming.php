<?php

namespace App\Enums;

enum CacheNaming: string
{
    case NAME = "nome";
    case EMAIL = "email";
    case CLOSED = "encerrada";
    case VERIFIED = "verified";
    case CPF_CNPJ = "cpf_cnpj";
    case CELL_PHONE = "celular";
    case BANK_BRANCH = "agencia";
}
