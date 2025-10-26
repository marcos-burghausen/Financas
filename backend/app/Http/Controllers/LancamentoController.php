<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLancamentoRequest;
use App\Services\LancamentoService;
use App\Http\Traits\UserDataTrait;
use App\Http\Traits\ReleasesMonthTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Lancamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LancamentoController extends Controller
{
    use UserDataTrait;

    protected $lancamentoService;

    public function __construct(LancamentoService $lancamentoService)
    {
        $this->lancamentoService = $lancamentoService;
    }

    /**
     * Lista lançamentos do usuário autenticado
     * Filtros suportados:
     * - tipo: receita|despesa|cartao_credito (opcional)
     * - mesAno: 2025-10 (opcional, padrão: mês atual)
     * - status: pendente|realizado (opcional)
     */
    public function getLancamento(Request $request): JsonResponse
    {
        info('Listando lançamentos com filtros: ' . json_encode($request->all()));
        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            // Parâmetros de filtro
            $tipo = $request->query('tipo');
            $mesAno = $request->query('mesAno', date('Y-m'));
            $status = $request->query('status');

            $ano = (int) substr($mesAno, 0, 4);
            $mes = (int) substr($mesAno, 5, 2);

            // Calcular mês anterior
            $mesPrevio = $mes - 1;
            $anoPrevio = $ano;
            if ($mesPrevio < 1) {
                $mesPrevio = 12;
                $anoPrevio--;
            }

            // Se não houver filtro de tipo, excluir CARTAO_CREDITO
            $tiposLancamento = [];
            if (!$tipo) {
                $tiposLancamento = ['RECEITA', 'DESPESA'];
            } else {
                $tiposLancamento = [$tipo];
            }

            // Inicializar arrays de resposta
            $lancamentosReceitas = [];
            $lancamentosDespesas = [];
            $variacaoReceitas = 0;
            $variacaoDespesas = 0;

            // ========== RECEITAS ==========
            if (in_array('RECEITA', $tiposLancamento)) {
                // Receitas do mês atual
                $queryMesAtualReceitas = Lancamento::where('user_id', $user->id)
                    ->whereYear('data_vencimento', $ano)
                    ->whereMonth('data_vencimento', $mes)
                    ->where('tipo_lancamento', 'RECEITA');

                $this->filtraPorStatus($queryMesAtualReceitas, $status);
                $totalMesAtualReceitas = $queryMesAtualReceitas->sum('valor');
                info('Total do mês atual (receitas): ' . $totalMesAtualReceitas);

                // Receitas do mês anterior
                $queryMesAnteriorReceitas = Lancamento::where('user_id', $user->id)
                    ->whereYear('data_vencimento', $anoPrevio)
                    ->whereMonth('data_vencimento', $mesPrevio)
                    ->where('tipo_lancamento', 'RECEITA');

                $this->filtraPorStatus($queryMesAnteriorReceitas, $status);
                $totalMesAnteriorReceitas = $queryMesAnteriorReceitas->sum('valor');
                info('Total do mês anterior (receitas): ' . $totalMesAnteriorReceitas);

                // Calcular variação de receitas
                $variacaoReceitas = 0;
                if ($totalMesAnteriorReceitas > 0) {
                    $variacaoReceitas = (($totalMesAtualReceitas - $totalMesAnteriorReceitas) / $totalMesAnteriorReceitas) * 100;
                } elseif ($totalMesAtualReceitas > 0) {
                    $variacaoReceitas = 100;
                }

                info('Variação receitas: ' . $variacaoReceitas . '%');

                // Obter receitas do mês atual
                $lancamentosReceitas = $queryMesAtualReceitas->orderBy('data_vencimento', 'desc')->get();
            }

            // ========== DESPESAS ==========
            if (in_array('DESPESA', $tiposLancamento)) {
                // Despesas do mês atual
                $queryMesAtualDespesas = Lancamento::where('user_id', $user->id)
                    ->whereYear('data_vencimento', $ano)
                    ->whereMonth('data_vencimento', $mes)
                    ->where('tipo_lancamento', 'DESPESA');

                $this->filtraPorStatus($queryMesAtualDespesas, $status);
                $totalMesAtualDespesas = $queryMesAtualDespesas->sum('valor');
                info('Total do mês atual (despesas): ' . $totalMesAtualDespesas);

                // Despesas do mês anterior
                $queryMesAnteriorDespesas = Lancamento::where('user_id', $user->id)
                    ->whereYear('data_vencimento', $anoPrevio)
                    ->whereMonth('data_vencimento', $mesPrevio)
                    ->where('tipo_lancamento', 'DESPESA');

                $this->filtraPorStatus($queryMesAnteriorDespesas, $status);
                $totalMesAnteriorDespesas = $queryMesAnteriorDespesas->sum('valor');
                info('Total do mês anterior (despesas): ' . $totalMesAnteriorDespesas);

                // Calcular variação de despesas
                $variacaoDespesas = 0;
                if ($totalMesAnteriorDespesas > 0) {
                    $variacaoDespesas = (($totalMesAtualDespesas - $totalMesAnteriorDespesas) / $totalMesAnteriorDespesas) * 100;
                } elseif ($totalMesAtualDespesas > 0) {
                    $variacaoDespesas = 100;
                }

                info('Variação despesas: ' . $variacaoDespesas . '%');

                // Obter despesas do mês atual
                $lancamentosDespesas = $queryMesAtualDespesas->orderBy('data_vencimento', 'desc')->get();
            }

            return response()->json([
                'success' => true,
                'variacaoReceitas' => $variacaoReceitas,
                'variacaoDespesas' => $variacaoDespesas,
                'lancamentosReceitas' => $lancamentosReceitas,
                'lancamentosDespesas' => $lancamentosDespesas,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao listar lançamentos: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Ocorreu um erro ao listar lançamentos.'], 500);
        }
    }

    private function filtraPorStatus($query, $status)
    {
        if (!$status) {
            return; // Se não houver filtro, não filtra
        }

        if (strtolower($status) === 'pendente') {
            return $query->where('status_lancamento', '!=', 'EFETIVADA');
        } elseif (strtolower($status) === 'realizado' || strtolower($status) === 'efetivada') {
            return $query->where('status_lancamento', 'EFETIVADA');
        }
    }

    public function saveLancamento(StoreLancamentoRequest  $request)
    {
        $validatedData = $request->validated();

        /** @var \App\Models\User $user */
        $user = auth()->user();

        try {
            DB::beginTransaction();

            $this->lancamentoService->createLancamento($validatedData, $user);

            DB::commit();

            $tipo = $validatedData['tipo_lancamento'];
            $dataToFetch = [($tipo === 'RECEITA') ? 'revenues' : 'expenses', 'wallets'];

            if ($tipo === 'CARTAO_CREDITO') {
                $dataToFetch = ['expenses', 'wallets'];
            }

            $responseData = $this->getUserData($user, $request->input('mesAno'), $dataToFetch);

            return response()->json([
                'success' => "Lançamento cadastrado com sucesso",
                ...$responseData
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar lançamento: ' . $e->getMessage(), ['exception' => $e]);


            return response()->json(['error' => 'Ocorreu um erro interno ao registrar o lançamento.'], 500);
        }
    }

    public function efetivarLancamento(Request $request, Lancamento $lancamento): JsonResponse
    {

        // 1. Verificação de permissão: garante que o lançamento pertence ao usuário autenticado.
        if ($lancamento->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado.'], 403);
        }
        info('Efetivando lançamento ID: ' . $request->mesAno);

        try {
            DB::beginTransaction();

            // 2. Delega a lógica de negócio para o serviço.
            $this->lancamentoService->efetivarLancamento($lancamento);

            DB::commit();

            // $mesAnoParaRetorno = $request->input('mesAno', $lancamento->data_vencimento->format('Y-m'));
            $tipo = $lancamento->tipo_lancamento;
            $dataToFetch = [($tipo === 'RECEITA') ? 'revenues' : 'expenses', 'wallets'];

            $responseData = $this->getUserData(auth()->user(), $request->mesAno, $dataToFetch);

            return response()->json([
                'success' => 'Lançamento efetivado com sucesso!',
                ...$responseData
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao efetivar lançamento: ' . $e->getMessage(), ['lancamento_id' => $lancamento->id, 'exception' => $e]);
            return response()->json(['error' => 'Ocorreu um erro ao efetivar o lançamento.'], 500);
        }
    }

    /**
     * Edita um lançamento existente
     */
    public function editLancamento(StoreLancamentoRequest $request, $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::find($id);

            if (!$lancamento) {
                return response()->json(['error' => 'Lançamento não encontrado.'], 404);
            }

            // Verificar permissão
            if ($lancamento->user_id !== auth()->id()) {
                return response()->json(['error' => 'Não autorizado.'], 403);
            }

            DB::beginTransaction();

            // ✅ Usar validated() para aplicar transformações do StoreLancamentoRequest
            $lancamento->update($request->validated());

            DB::commit();

            return response()->json([
                'success' => 'Lançamento atualizado com sucesso!',
                'data' => $lancamento
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao editar lançamento: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Ocorreu um erro ao atualizar o lançamento.'], 500);
        }
    }

    /**
     * Marca um lançamento como pago/recebido
     */
    public function receivedLancamento(Request $request, $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::find($id);

            if (!$lancamento) {
                return response()->json(['error' => 'Lançamento não encontrado.'], 404);
            }

            // Verificar permissão
            if ($lancamento->user_id !== auth()->id()) {
                return response()->json(['error' => 'Não autorizado.'], 403);
            }

            DB::beginTransaction();

            // Atualizar status
            $lancamento->status_lancamento = 'REALIZADO';
            $lancamento->data_efetivacao = now();
            $lancamento->save();

            DB::commit();

            return response()->json([
                'success' => 'Status atualizado com sucesso!',
                'data' => $lancamento
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar lançamento: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Ocorreu um erro ao atualizar o lançamento.'], 500);
        }
    }

    /**
     * Deleta um lançamento
     */
    public function deleteLancamento(Request $request, $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::find($id);

            if (!$lancamento) {
                return response()->json(['error' => 'Lançamento não encontrado.'], 404);
            }

            // Verificar permissão
            if ($lancamento->user_id !== auth()->id()) {
                return response()->json(['error' => 'Não autorizado.'], 403);
            }

            DB::beginTransaction();

            $lancamento->delete();

            DB::commit();

            return response()->json([
                'success' => 'Lançamento removido com sucesso!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar lançamento: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Ocorreu um erro ao remover o lançamento.'], 500);
        }
    }

    private function handleCreditCardLancamento($data)
    {
        if (isset($data['id']) && $data['id'] !== '') {
            $this->updateLancamento($data, $data['invoice_id']);
        } else {
            $this->createLancamento($data, $data['invoice_id']);
        }
    }

    private function createLancamento($data, $invoiceId = null)
    {
        $data['credit_card_invoice_id'] = $invoiceId;
        if ($data['recorrencia'] === 'PARCELADO' && isset($data['qtd_parcelas']) && $data['qtd_parcelas'] > 1) {
            $groupId = Str::uuid();

            if ($data['tipo_parcela'] === 'TOTAL') {
                $valorBaseParcela = intdiv($data['valor'], $data['qtd_parcelas']);
                $resto = $data['valor'] % $data['qtd_parcelas'];
            } else {
                $valorBaseParcela = $data['valor'];
                $resto = 0;
            }

            for ($i = 0; $i < $data['qtd_parcelas']; $i++) {
                $lancamento = new Lancamento($data);
                $lancamento->num_parcela = $i + 1;
                $lancamento->data_vencimento = date('Y-m-d', strtotime("+$i month", strtotime($data['data_vencimento'])));
                $lancamento->save();
            }
        } else {
            Lancamento::create($data);
        }
    }

    private function updateLancamento($data, $invoiceId = null)
    {
        $lancamento = Lancamento::find($data['id']);
        if ($lancamento) {
            $data['credit_card_invoice_id'] = $invoiceId;
            $lancamento->update($data);
        }
    }

    public function receive(Request $request)
    {
        $data = $request->all();
        $lancamento = Lancamento::find($data['id']);
        if ($lancamento) {
            $lancamento->pago = $data['pago'];
            $lancamento->save();
        }
    }

    public function delete(Request $request)
    {
        $data = $request->all();
        $lancamento = Lancamento::find($data['id']);
        if ($lancamento) {
            $lancamento->delete();
        }
    }

    public function createRefund(Request $request)
    {
        $data = $request->all();
        $originalLancamento = Lancamento::find($data['id']);

        if ($originalLancamento) {
            $refundLancamento = $originalLancamento->replicate();
            $refundLancamento->valor = $data['valor_estorno'];
            $refundLancamento->descricao = 'Estorno: ' . $originalLancamento->descricao;
            $refundLancamento->is_estorno = true;
            $refundLancamento->original_lancamento_id = $originalLancamento->id;
            $refundLancamento->pago = true;
            $refundLancamento->save();
        }
    }
}
