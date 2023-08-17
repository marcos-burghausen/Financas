<?php

namespace App\Http\Controllers;

use App\Enums\Actions;
use App\Http\Controllers\Controller;
use App\Models\Log as LogModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{

    /**
     * @param string $entity_id     random user id key
     * @param string $username      cpf_cnpj of the associate
     * @param string $timestamp     request time
     * @param string $user_agent    browser name
     * @param string $ip            ip id
     * @param Actions $action       identified action
     * @param array $parameters     additional request details
     * @return void
     */

    public static function addsLog(string $username, Actions $action): void
    {
        try {
            LogModel::create([
                'email' => $username,
                'timestamp' => date('d/m/Y - H:i:s', $_SERVER['REQUEST_TIME']),
                // 'user_agent' => $request->header('user-agent'),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                //'ip' => $request->ip(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'action' => $action->getAction(),
            ]);
        } catch (Exception $err) {
            Log::error('Error adding log to database', [
                'error' => $err
            ]);
        }
    }
}
