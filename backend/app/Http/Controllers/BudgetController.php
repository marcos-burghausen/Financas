<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Lancamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BudgetController extends Controller
{
    /**
     * Lista orçamentos do usuário autenticado
     * Filtros suportados:
     * - mesAno: 2025-11 (opcional, padrão: mês atual)
     * - categoria: string (opcional)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            $mesAno = $request->query('mesAno', date('Y-m'));
            $categoria = $request->query('categoria');

            Log::info('Listando orçamentos', [
                'user_id' => $user->id,
                'mesAno' => $mesAno,
                'categoria' => $categoria
            ]);

            $query = Budget::forUserAndMonth($user->id, $mesAno);

            if ($categoria) {
                $query->forCategory($categoria);
            }

            $budgets = $query->orderBy('categoria')->get();

            // Adicionar dados calculados para cada orçamento
            $budgetsWithData = $budgets->map(function ($budget) {
                return [
                    'id' => $budget->id,
                    'categoria' => $budget->categoria,
                    'valor_orcado' => $budget->valor_orcado,
                    'gasto_real' => $budget->gasto_real,
                    'saldo_restante' => $budget->saldo_restante,
                    'percentual_gasto' => $budget->percentual_gasto,
                    'status' => $budget->status,
                    'mes_ano' => $budget->mes_ano,
                    'observacao' => $budget->observacao,
                    'transacoes' => $budget->transacoes,
                    'created_at' => $budget->created_at,
                    'updated_at' => $budget->updated_at,
                ];
            });

            // Calcular resumo geral
            $resumo = $this->calcularResumo($user->id, $mesAno);

            return response()->json([
                'success' => true,
                'data' => [
                    'budgets' => $budgetsWithData,
                    'resumo' => $resumo,
                    'mesAno' => $mesAno
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar orçamentos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Criar novo orçamento
     */
    public function store(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            $validated = $request->validate([
                'categoria' => 'required|string|max:30',
                'valor_orcado' => 'required|integer|min:1',
                'mes_ano' => 'required|string|size:7|regex:/^\d{4}-\d{2}$/',
                'observacao' => 'nullable|string|max:1000',
            ]);

            // Verificar se já existe orçamento para esta categoria/mês
            $existente = Budget::forUserAndMonth($user->id, $validated['mes_ano'])
                ->forCategory($validated['categoria'])
                ->first();

            if ($existente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe um orçamento para esta categoria neste mês'
                ], 422);
            }

            $budget = Budget::create([
                'user_id' => $user->id,
                'categoria' => $validated['categoria'],
                'valor_orcado' => $validated['valor_orcado'],
                'mes_ano' => $validated['mes_ano'],
                'observacao' => $validated['observacao'],
            ]);

            Log::info('Orçamento criado', [
                'budget_id' => $budget->id,
                'user_id' => $user->id,
                'categoria' => $budget->categoria,
                'mes_ano' => $budget->mes_ano
            ]);

            // Retornar orçamento com dados calculados
            $budgetWithData = [
                'id' => $budget->id,
                'categoria' => $budget->categoria,
                'valor_orcado' => $budget->valor_orcado,
                'gasto_real' => $budget->gasto_real,
                'saldo_restante' => $budget->saldo_restante,
                'percentual_gasto' => $budget->percentual_gasto,
                'status' => $budget->status,
                'mes_ano' => $budget->mes_ano,
                'observacao' => $budget->observacao,
                'transacoes' => $budget->transacoes,
                'created_at' => $budget->created_at,
                'updated_at' => $budget->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Orçamento criado com sucesso',
                'data' => $budgetWithData
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao criar orçamento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Exibir orçamento específico
     */
    public function show(Budget $budget): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            // Verificar se o orçamento pertence ao usuário
            if ($budget->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orçamento não encontrado'
                ], 404);
            }

            $budgetWithData = [
                'id' => $budget->id,
                'categoria' => $budget->categoria,
                'valor_orcado' => $budget->valor_orcado,
                'gasto_real' => $budget->gasto_real,
                'saldo_restante' => $budget->saldo_restante,
                'percentual_gasto' => $budget->percentual_gasto,
                'status' => $budget->status,
                'mes_ano' => $budget->mes_ano,
                'observacao' => $budget->observacao,
                'transacoes' => $budget->transacoes,
                'created_at' => $budget->created_at,
                'updated_at' => $budget->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $budgetWithData
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar orçamento', [
                'budget_id' => $budget->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Atualizar orçamento
     */
    public function update(Request $request, Budget $budget): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            // Verificar se o orçamento pertence ao usuário
            if ($budget->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orçamento não encontrado'
                ], 404);
            }

            $validated = $request->validate([
                'categoria' => 'sometimes|required|string|max:30',
                'valor_orcado' => 'sometimes|required|integer|min:1',
                'mes_ano' => 'sometimes|required|string|size:7|regex:/^\d{4}-\d{2}$/',
                'observacao' => 'nullable|string|max:1000',
            ]);

            // Se categoria ou mes_ano foram alterados, verificar duplicatas
            if (isset($validated['categoria']) || isset($validated['mes_ano'])) {
                $novaCategoria = $validated['categoria'] ?? $budget->categoria;
                $novoMesAno = $validated['mes_ano'] ?? $budget->mes_ano;

                if ($novaCategoria !== $budget->categoria || $novoMesAno !== $budget->mes_ano) {
                    $existente = Budget::forUserAndMonth($user->id, $novoMesAno)
                        ->forCategory($novaCategoria)
                        ->where('id', '!=', $budget->id)
                        ->first();

                    if ($existente) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Já existe um orçamento para esta categoria neste mês'
                        ], 422);
                    }
                }
            }

            $budget->update($validated);

            Log::info('Orçamento atualizado', [
                'budget_id' => $budget->id,
                'user_id' => $user->id,
                'changes' => $validated
            ]);

            $budgetWithData = [
                'id' => $budget->id,
                'categoria' => $budget->categoria,
                'valor_orcado' => $budget->valor_orcado,
                'gasto_real' => $budget->gasto_real,
                'saldo_restante' => $budget->saldo_restante,
                'percentual_gasto' => $budget->percentual_gasto,
                'status' => $budget->status,
                'mes_ano' => $budget->mes_ano,
                'observacao' => $budget->observacao,
                'transacoes' => $budget->transacoes,
                'created_at' => $budget->created_at,
                'updated_at' => $budget->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Orçamento atualizado com sucesso',
                'data' => $budgetWithData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar orçamento', [
                'budget_id' => $budget->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Remover orçamento
     */
    public function destroy(Budget $budget): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            // Verificar se o orçamento pertence ao usuário
            if ($budget->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orçamento não encontrado'
                ], 404);
            }

            Log::info('Orçamento removido', [
                'budget_id' => $budget->id,
                'user_id' => $user->id,
                'categoria' => $budget->categoria,
                'mes_ano' => $budget->mes_ano
            ]);

            $budget->delete();

            return response()->json([
                'success' => true,
                'message' => 'Orçamento removido com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao remover orçamento', [
                'budget_id' => $budget->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Calcular resumo geral dos orçamentos do mês
     */
    private function calcularResumo(int $userId, string $mesAno): array
    {
        $budgets = Budget::forUserAndMonth($userId, $mesAno)->get();

        $totalOrcado = $budgets->sum('valor_orcado');
        $totalGasto = $budgets->sum(function ($budget) {
            return $budget->gasto_real;
        });

        $saldoRestante = $totalOrcado - $totalGasto;

        // Meta de economia (pode ser configurável no futuro)
        $metaEconomia = (int) ($totalOrcado * 0.1); // 10% do orçamento total

        return [
            'total_orcado' => $totalOrcado,
            'total_gasto' => $totalGasto,
            'saldo_restante' => $saldoRestante,
            'meta_economia' => $metaEconomia,
            'percentual_gasto_geral' => $totalOrcado > 0 ? round(($totalGasto / $totalOrcado) * 100, 2) : 0,
            'total_categorias' => $budgets->count(),
        ];
    }

    /**
     * Obter categorias disponíveis baseadas nos lançamentos do usuário
     */
    public function getCategorias(): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            $categorias = Lancamento::where('user_id', $user->id)
                ->where('tipo_lancamento', 'DESPESA')
                ->distinct()
                ->pluck('categoria')
                ->sort()
                ->values();

            return response()->json([
                'success' => true,
                'data' => $categorias
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar categorias', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
