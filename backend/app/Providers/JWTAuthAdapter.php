<?php

namespace App\Providers;

use Tymon\JWTAuth\Providers\Auth\Illuminate as JWTIlluminateAuthAdapter;

class JWTAuthAdapter extends JWTIlluminateAuthAdapter
{
    public function byCredentials(array $credentials)
    {
        // Se o usuário está tentando fazer login com Facebook, não verificamos a senha
        if (isset($credentials['facebook_id'])) {
            return $this->auth->getProvider()->retrieveByCredentials($credentials);
        }

        // Caso contrário, use o comportamento padrão
        return parent::byCredentials($credentials);
    }
}