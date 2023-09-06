<?php

namespace App\Enums;

enum CacheNaming: string
{
    case NAME = "nome";
    case EMAIL = "email";
    case CLOSED = "encerrada";
    case VERIFIED = "verified";
}
