<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletsController extends Controller
{
    public function addWallets(Request $request)
    {
        $user = User::find($request->user_id);
        $carteiras = $user->carteiras;
        array_push($carteiras, $request->carteira);
        $user->carteiras = $carteiras;
        $saved = $user->save();
        if ($saved) {
            return response()->json('carteira add com sucesso');
        }
        return response()->json('Erro ao adicionar carteira');
    }

    public function editWallets(Request $request)
    {
        $user = User::find($request->user_id);
        $carteiras = $user->carteiras;
        $carteiras[$request->key] = $request->carteira;
        $user->carteiras = $carteiras;
        $saved = $user->save();
        if ($saved) {
            return response()->json('carteira editada com sucesso');
        }
        return response()->json('Erro ao editar carteira');
    }

    public function deleteWallets(Request $request)
    {
        $user = User::find($request->user_id);
        $carteiras = $user->carteiras;
        if (!in_array($request->carteira, $carteiras)) return response()->json('Carteira não encontrado');
        $user->carteiras = array_diff($carteiras, [$request->carteira]);
        $saved = $user->save();
        if ($saved) {
            return response()->json('carteira excluida com sucesso');
        }
        return response()->json('Erro ao excluir carteira');
    }

    public function getWallets(Request $request)
    {
        $user = User::find($request->user_id);
        $carteiras = $user->carteiras;
        return response()->json($carteiras);
    }
}
