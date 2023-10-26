<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
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
            $saved = $user->save();
            if (!$saved) return response()->json(Errors::ERROR_REGISTER_CATEGORY->response());
            return response()->json([
                'msg' => 'categoria despesa adicionada com sucesso',
                'categoriasDespesas' => $categoriasDespesas,
                'user' => $user
            ]);
        } else {
            $categoriasReceitas = $user->categoriasReceitas;
            $categoriasReceitas[] = $newCategory;
            $user->categoriasReceitas = $categoriasReceitas;
            $saved = $user->save();
            if (!$saved) return response()->json(Errors::ERROR_REGISTER_CATEGORY->response());
            return response()->json([
                'msg' => 'categoria receita adicionada com sucesso',
                'categoriasReceitas' => $categoriasReceitas,
                'user' => $user
            ]);
        }
    }

    public function deleteCategory(Request $request)
    {
        $data = $request->validate(
            [
                'color'        => 'string|nullable',
                'icon'         => 'string|nullable',
                'edit'         => 'required|boolean',
                'name'         => 'required|min:3|string',
                'typeCategory' => 'required|string',
            ]
        );

        /** @var User $user */
        $user = auth()->user();

        $categorias = [];
        if ($data['typeCategory'] === 'despesa') {
            $categorias = $user->categoriasDespesas;
            foreach ($categorias as $key => $categoria) {
                if ($categoria['name'] === $data['name']) {
                    unset($categorias[$key]);
                }
            }
            $user->categoriasDespesas = $categorias;
            $saved = $user->save();
            if (!$saved) return response()->json(Errors::ERROR_DELETE_CATEGORY->response());
            return response()->json([
                'msg' => 'categoria excluida com sucesso',
                'categoriasDespesas' => $categorias
            ]);
        } else {
            $categorias = $user->categoriasReceitas;
            foreach ($categorias as $key => $categoria) {
                if ($categoria['name'] === $data['name']) {
                    unset($categorias[$key]);
                }
            }
            $user->categoriasReceitas = $categorias;
            $saved = $user->save();
            if (!$saved) return response()->json(Errors::ERROR_DELETE_CATEGORY->response());
            return response()->json([
                'msg' => 'categoria excluida com sucesso',
                'categoriasReceitas' => $categorias
            ]);
        }
    }
}
