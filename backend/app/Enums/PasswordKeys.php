<?php

namespace App\Enums;

enum PasswordKeys: string
{
    case PASSWORD = "password";
    case CREATED_AT = "created_at";
    case EXPIRE_AT = "expire_at";

    public function append(string|int $content)
    {
        return $this->value . $content;
    }
}
