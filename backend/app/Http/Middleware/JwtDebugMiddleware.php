<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtDebugMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('=== JWT DEBUG MIDDLEWARE ===');
        Log::info('URL: ' . $request->url());
        Log::info('Method: ' . $request->method());

        $token = $request->bearerToken();
        Log::info('Bearer Token recebido: ' . ($token ? 'SIM' : 'NÃO'));

        if ($token) {
            Log::info('Token (primeiros 50 chars): ' . substr($token, 0, 50) . '...');

            try {
                $payload = JWTAuth::setToken($token)->getPayload();
                Log::info('Token válido! User ID: ' . $payload->get('sub'));
            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                Log::error('Token EXPIRADO!');
                Log::error('Detalhes: ' . $e->getMessage());
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                Log::error('Token INVÁLIDO!');
                Log::error('Detalhes: ' . $e->getMessage());
                Log::error('Trace: ' . $e->getTraceAsString());
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                Log::error('Erro JWT: ' . $e->getMessage());
                Log::error('Trace: ' . $e->getTraceAsString());
            }
        } else {
            Log::warning('Nenhum token Bearer encontrado no header Authorization!');
            $authHeader = $request->header('Authorization');
            Log::info('Authorization header: ' . ($authHeader ?? 'NULL'));
        }

        Log::info('=== FIM JWT DEBUG ===');

        return $next($request);
    }
}
