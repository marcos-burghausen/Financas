<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function saveCategory(Request $request)
    {
        $data = $request->validate(
            [
                'color'        => 'string|nullable',
                'icon'         => 'string|nullable',
                'edit'         => 'required|boolean',
                'name'         => 'required|min:3|string',
                'typeCategory' => 'required|string',
            ],
            [
                'name.required'            => 'O campo nome da categoria é obrigatório',
                'name.min'                 => 'O campo nome da categoria deve ter pelo menos 3 caracteres',
            ]
        );
        $newCategory = $request->all(['color', 'edit', 'icon', 'name', 'typeCategory']);
        /** @var User $user */
        $user = auth()->user();
        if ($newCategory['typeCategory'] === 'despesa') {
            $categoriasDespesas = $user->categoriasDespesas;
            $categoriasDespesas[] = $newCategory;
            $user->categoriasDespesas = $categoriasDespesas;
            $user->save();
            return response()->json([
                'msg' => 'categoria despesa adicionada com sucesso',
                'categoriasDespesas' => $categoriasDespesas,
                'user' => $user
            ]);
        } else {
            $categoriasReceitas = $user->categoriasReceitas;
            $categoriasReceitas[] = $newCategory;
            $user->categoriasReceitas = $categoriasReceitas;
            $user->save();
            return response()->json([
                'msg' => 'categoria receita adicionada com sucesso',
                'categoriasReceitas' => $categoriasReceitas,
                'user' => $user
            ]);
        }
    }
}
