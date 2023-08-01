<?php

namespace App\Http\Controllers;

use App\Custom\Jwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json('Usuario e/ou senha invalidos', 401);
        }

        $user = Auth::user();

        $token = Jwt::create($user->email);
        // return response()->json($token);
        return response()->json([
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'id' => $user->id
            ]
        ]);
    }
}
