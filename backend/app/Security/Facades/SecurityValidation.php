<?php

namespace app\Security\Facades;

use Illuminate\Support\Facades\Facade;

class SecurityValidation extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'SecurityValidation';
    }
}
