<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLancamentoRequest;
use App\Services\LancamentoService;
use App\Http\Traits\UserDataTrait;
use App\Http\Traits\ReleasesMonthTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Lancamento;
use App\Models\Conta;
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

            // ✅ Primeiro validar os dados para aplicar transformações
            $validatedData = $request->validated();

            // ✅ Verificar se é uma edição recorrente com escopo específico
            $editScope = $request->input('editScope');
            if ($editScope && in_array($lancamento->recorrencia, ['FIXA', 'PARCELADO'])) {
                return $this->handleRecurrentEdit($lancamento, $validatedData, $editScope);
            }

            // ✅ Capturar dados ANTES e DEPOIS (APÓS validação/transformação)
            $statusAnterior = $lancamento->status_lancamento;
            $valorAnterior = $lancamento->valor;
            $contaAnterior = $lancamento->conta_id;
            $tipoAnterior = $lancamento->tipo_lancamento;

            $statusNovo = $validatedData['status_lancamento'];
            $valorNovo = $validatedData['valor'];
            $contaNova = $validatedData['conta_id'];
            $tipoNovo = $validatedData['tipo_lancamento'];

            Log::info("=== EDITANDO LANÇAMENTO {$id} ===");
            Log::info("Status: {$statusAnterior} → {$statusNovo}");
            Log::info("Valor: {$valorAnterior} → {$valorNovo}");
            Log::info("Conta: {$contaAnterior} → {$contaNova}");
            Log::info("Tipo: {$tipoAnterior} → {$tipoNovo}");

            // ✅ Verificar se alguma mudança afeta o saldo das contas
            $afetaSaldo = ($statusAnterior !== $statusNovo) ||
                ($valorAnterior !== $valorNovo) ||
                ($contaAnterior !== $contaNova) ||
                ($tipoAnterior !== $tipoNovo);

            if ($afetaSaldo && in_array($tipoAnterior, ['RECEITA', 'DESPESA'])) {
                Log::info("DETECTADA MUDANÇA QUE AFETA SALDO!");

                // 1. REVERTER o efeito do lançamento antigo (se estava efetivado)
                if ($statusAnterior === 'EFETIVADA' && $contaAnterior) {
                    $contaAntiga = Conta::find($contaAnterior);
                    if ($contaAntiga) {
                        Log::info("Saldo da conta {$contaAnterior} ANTES da reversão: {$contaAntiga->saldo}");

                        if ($tipoAnterior === 'RECEITA') {
                            $contaAntiga->saldo -= $valorAnterior; // Remover receita antiga
                            Log::info("Revertendo RECEITA EFETIVADA: subtraindo {$valorAnterior}");
                        } else { // DESPESA
                            $contaAntiga->saldo += $valorAnterior; // Devolver despesa antiga
                            Log::info("Revertendo DESPESA EFETIVADA: adicionando {$valorAnterior}");
                        }

                        Log::info("Saldo da conta {$contaAnterior} DEPOIS da reversão: {$contaAntiga->saldo}");
                        $contaAntiga->save();
                    }
                }

                // 2. APLICAR o efeito do novo lançamento (se for efetivado)
                if ($statusNovo === 'EFETIVADA' && $contaNova && in_array($tipoNovo, ['RECEITA', 'DESPESA'])) {
                    $contaNova_obj = Conta::find($contaNova);
                    if ($contaNova_obj) {
                        Log::info("Saldo da conta {$contaNova} ANTES da aplicação: {$contaNova_obj->saldo}");

                        if ($tipoNovo === 'RECEITA') {
                            $contaNova_obj->saldo += $valorNovo; // Adicionar receita nova
                            Log::info("Aplicando RECEITA EFETIVADA: adicionando {$valorNovo}");
                        } else { // DESPESA
                            $contaNova_obj->saldo -= $valorNovo; // Subtrair despesa nova
                            Log::info("Aplicando DESPESA EFETIVADA: subtraindo {$valorNovo}");
                        }

                        Log::info("Saldo da conta {$contaNova} DEPOIS da aplicação: {$contaNova_obj->saldo}");
                        $contaNova_obj->save();
                    }
                }
            } else {
                Log::info("Nenhuma mudança afeta o saldo das contas");
            }

            // ✅ Atualizar o lançamento com os dados já validados
            Log::info("Dados validados: ", $validatedData);
            $lancamento->update($validatedData);

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

            // ✅ Se o lançamento estava EFETIVADO, reverter o saldo da conta ANTES de deletar
            if (
                $lancamento->status_lancamento === 'EFETIVADA' &&
                in_array($lancamento->tipo_lancamento, ['RECEITA', 'DESPESA']) &&
                $lancamento->conta_id
            ) {

                $conta = Conta::find($lancamento->conta_id);
                if ($conta) {
                    Log::info("=== DELETANDO LANÇAMENTO EFETIVADO {$id} ===");
                    Log::info("Tipo: {$lancamento->tipo_lancamento}, Valor: {$lancamento->valor}, Conta: {$lancamento->conta_id}");
                    Log::info("Saldo da conta ANTES da exclusão: {$conta->saldo}");

                    // Reverter o efeito do lançamento na conta
                    if ($lancamento->tipo_lancamento === 'RECEITA') {
                        $conta->saldo -= $lancamento->valor; // Remover receita
                        Log::info("Revertendo RECEITA EFETIVADA: subtraindo {$lancamento->valor}");
                    } else { // DESPESA
                        $conta->saldo += $lancamento->valor; // Devolver despesa
                        Log::info("Revertendo DESPESA EFETIVADA: adicionando {$lancamento->valor}");
                    }

                    Log::info("Saldo da conta DEPOIS da exclusão: {$conta->saldo}");
                    $conta->save();
                }
            } else {
                Log::info("Deletando lançamento {$id} - Status: {$lancamento->status_lancamento} (sem impacto no saldo)");
            }

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

                // Configurar campos específicos de parcela
                $lancamento->installment_group_id = $groupId;
                $lancamento->qtd_parcelas = $data['qtd_parcelas']; // Campo correto do banco
                $lancamento->num_parcela = $i + 1; // Campo correto do banco
                $lancamento->data_vencimento = date('Y-m-d', strtotime("+$i month", strtotime($data['data_vencimento'])));

                // Configurar valor da parcela
                $valorParcela = $valorBaseParcela;
                if ($i == 0 && $resto > 0) {
                    $valorParcela += $resto; // Primeira parcela leva o resto
                }
                $lancamento->valor = $valorParcela;

                // Configurar descrição com numeração
                $descricaoBase = $data['descricao'];
                $lancamento->descricao = $descricaoBase . ' (' . ($i + 1) . '/' . $data['qtd_parcelas'] . ')';

                $lancamento->save();

                Log::info("Parcela {$lancamento->num_parcela}/{$data['qtd_parcelas']} criada: {$lancamento->descricao}, valor: {$valorParcela}");
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

    /**
     * Lidar com edições recorrentes com escopo específico
     */
    private function handleRecurrentEdit($lancamento, $validatedData, $editScope): JsonResponse
    {
        try {
            Log::info("=== EDITANDO LANÇAMENTO RECORRENTE {$lancamento->id} COM ESCOPO: {$editScope} ===");

            switch ($editScope) {
                case 'apenas_esta':
                    // Atualizar apenas este lançamento
                    return $this->updateSingleRecurrentLancamento($lancamento, $validatedData);

                case 'esta_e_proximas':
                    // Atualizar este e os próximos da mesma recorrência
                    return $this->updateCurrentAndFutureRecurrentLancamentos($lancamento, $validatedData);

                case 'todas':
                    // Atualizar todos os lançamentos da mesma recorrência
                    return $this->updateAllRecurrentLancamentos($lancamento, $validatedData);

                default:
                    return response()->json(['error' => 'Escopo de edição inválido.'], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao editar lançamento recorrente: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Ocorreu um erro ao atualizar o lançamento recorrente.'], 500);
        }
    }

    /**
     * Atualizar apenas um lançamento recorrente (quebrar recorrência)
     */
    private function updateSingleRecurrentLancamento($lancamento, $validatedData): JsonResponse
    {
        // NÃO quebrar a recorrência - apenas atualizar este lançamento específico
        // Manter installment_group_id, recorrencia, e outras propriedades da série

        // Remover campos que não devem ser alterados para manter a série intacta
        unset($validatedData['recorrencia']);
        unset($validatedData['installment_group_id']);
        unset($validatedData['qtd_parcelas']);
        unset($validatedData['num_parcela']);
        unset($validatedData['tipo_parcela']);
        unset($validatedData['periodicidade']);

        // Aplicar a lógica normal de edição com cálculo de saldo (sem commit pois já há transação ativa)
        $this->updateLancamentoWithBalanceCalculation($lancamento, $validatedData, false);

        // Fazer commit da transação principal
        DB::commit();

        return response()->json([
            'success' => 'Lançamento atualizado com sucesso!',
            'data' => $lancamento
        ], 200);
    }

    /**
     * Atualizar este e os próximos lançamentos recorrentes
     */
    private function updateCurrentAndFutureRecurrentLancamentos($lancamento, $validatedData): JsonResponse
    {
        $grupoId = $lancamento->installment_group_id;
        $dataAtual = $lancamento->data_vencimento;

        if (!$grupoId) {
            return response()->json(['error' => 'Lançamento não possui grupo de recorrência.'], 400);
        }

        // Buscar todos os lançamentos do mesmo grupo a partir desta data
        $lancamentosFuturos = Lancamento::where('installment_group_id', $grupoId)
            ->where('data_vencimento', '>=', $dataAtual)
            ->where('user_id', auth()->id())
            ->get();

        if ($lancamentosFuturos->count() == 0) {
            return response()->json([
                'warning' => 'Nenhum lançamento futuro encontrado para atualizar.',
                'updated_count' => 0
            ], 200);
        }

        DB::beginTransaction();

        try {
            $updatedCount = 0;
            foreach ($lancamentosFuturos as $futuroLancamento) {
                Log::info("Processando lançamento {$futuroLancamento->id} - valor atual: {$futuroLancamento->valor}");

                // Capturar dados ANTES da alteração para calcular saldo
                $statusAnterior = $futuroLancamento->status_lancamento;
                $valorAnterior = $futuroLancamento->valor;
                $contaAnterior = $futuroLancamento->conta_id;
                $tipoAnterior = $futuroLancamento->tipo_lancamento;

                $statusNovo = $validatedData['status_lancamento'];
                $valorNovo = $validatedData['valor'];
                $contaNova = $validatedData['conta_id'];
                $tipoNovo = $validatedData['tipo_lancamento'];

                Log::info("Status: {$statusAnterior} → {$statusNovo}, Valor: {$valorAnterior} → {$valorNovo}");

                // 1. REVERTER o efeito do lançamento anterior (se estava efetivado)
                if ($statusAnterior === 'EFETIVADA' && $contaAnterior && in_array($tipoAnterior, ['RECEITA', 'DESPESA'])) {
                    if ($tipoAnterior === 'RECEITA') {
                        DB::statement('UPDATE contas SET saldo = saldo - ? WHERE id = ?', [$valorAnterior, $contaAnterior]);
                        Log::info("Revertendo RECEITA EFETIVADA: subtraindo {$valorAnterior} da conta {$contaAnterior}");
                    } else { // DESPESA
                        DB::statement('UPDATE contas SET saldo = saldo + ? WHERE id = ?', [$valorAnterior, $contaAnterior]);
                        Log::info("Revertendo DESPESA EFETIVADA: adicionando {$valorAnterior} à conta {$contaAnterior}");
                    }
                }

                // 2. PREPARAR descrição preservando numeração para parcelas
                $descricaoFinal = $validatedData['descricao'];

                // Se for parcelado, preservar a numeração original de cada parcela
                if ($futuroLancamento->recorrencia === 'PARCELADO' && $futuroLancamento->num_parcela && $futuroLancamento->qtd_parcelas) {
                    // Extrair descrição base removendo a numeração atual
                    $descricaoBase = preg_replace('/\s*\(\d+\/\d+\)$/', '', $validatedData['descricao']);
                    // Aplicar a numeração correta desta parcela
                    $descricaoFinal = $descricaoBase . ' (' . $futuroLancamento->num_parcela . '/' . $futuroLancamento->qtd_parcelas . ')';

                    Log::info("Preservando numeração: '{$validatedData['descricao']}' → '{$descricaoFinal}'");
                }

                // ATUALIZAR o lançamento
                $result = DB::update(
                    "UPDATE lancamentos SET valor = ?, descricao = ?, status_lancamento = ?, updated_at = NOW() WHERE id = ?",
                    [$valorNovo, $descricaoFinal, $statusNovo, $futuroLancamento->id]
                );

                // 3. APLICAR o efeito do novo lançamento (se for efetivado)
                if ($statusNovo === 'EFETIVADA' && $contaNova && in_array($tipoNovo, ['RECEITA', 'DESPESA'])) {
                    if ($tipoNovo === 'RECEITA') {
                        DB::statement('UPDATE contas SET saldo = saldo + ? WHERE id = ?', [$valorNovo, $contaNova]);
                        Log::info("Aplicando RECEITA EFETIVADA: adicionando {$valorNovo} à conta {$contaNova}");
                    } else { // DESPESA
                        DB::statement('UPDATE contas SET saldo = saldo - ? WHERE id = ?', [$valorNovo, $contaNova]);
                        Log::info("Aplicando DESPESA EFETIVADA: subtraindo {$valorNovo} da conta {$contaNova}");
                    }
                }

                // COMMIT explícito após cada lançamento completo
                DB::statement('COMMIT');

                Log::info("Lançamento {$futuroLancamento->id} atualizado com sucesso");

                if ($result > 0) {
                    $updatedCount++;
                }
            }
            Log::info("Total de lançamentos atualizados com sucesso: {$updatedCount}");

            return response()->json([
                'success' => 'Lançamentos recorrentes atualizados com sucesso!',
                'updated_count' => $updatedCount
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar lançamentos futuros: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json(['error' => 'Erro ao atualizar lançamentos: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Atualizar todos os lançamentos recorrentes
     */
    private function updateAllRecurrentLancamentos($lancamento, $validatedData): JsonResponse
    {
        $grupoId = $lancamento->installment_group_id;

        if (!$grupoId) {
            return response()->json(['error' => 'Lançamento não possui grupo de recorrência.'], 400);
        }

        // Buscar todos os lançamentos do mesmo grupo
        $todosLancamentos = Lancamento::where('installment_group_id', $grupoId)
            ->where('user_id', auth()->id())
            ->get();

        if ($todosLancamentos->count() == 0) {
            return response()->json([
                'warning' => 'Nenhum lançamento encontrado para atualizar.',
                'updated_count' => 0
            ], 200);
        }

        try {
            $updatedCount = 0;
            foreach ($todosLancamentos as $grupoLancamento) {
                Log::info("Processando lançamento {$grupoLancamento->id} - valor atual: {$grupoLancamento->valor}");

                // Capturar dados ANTES da alteração para calcular saldo
                $statusAnterior = $grupoLancamento->status_lancamento;
                $valorAnterior = $grupoLancamento->valor;
                $contaAnterior = $grupoLancamento->conta_id;
                $tipoAnterior = $grupoLancamento->tipo_lancamento;

                $statusNovo = $validatedData['status_lancamento'];
                $valorNovo = $validatedData['valor'];
                $contaNova = $validatedData['conta_id'];
                $tipoNovo = $validatedData['tipo_lancamento'];

                Log::info("Status: {$statusAnterior} → {$statusNovo}, Valor: {$valorAnterior} → {$valorNovo}");

                // 1. REVERTER o efeito do lançamento anterior (se estava efetivado)
                if ($statusAnterior === 'EFETIVADA' && $contaAnterior && in_array($tipoAnterior, ['RECEITA', 'DESPESA'])) {
                    if ($tipoAnterior === 'RECEITA') {
                        DB::statement('UPDATE contas SET saldo = saldo - ? WHERE id = ?', [$valorAnterior, $contaAnterior]);
                        Log::info("Revertendo RECEITA EFETIVADA: subtraindo {$valorAnterior} da conta {$contaAnterior}");
                    } else { // DESPESA
                        DB::statement('UPDATE contas SET saldo = saldo + ? WHERE id = ?', [$valorAnterior, $contaAnterior]);
                        Log::info("Revertendo DESPESA EFETIVADA: adicionando {$valorAnterior} à conta {$contaAnterior}");
                    }
                }

                // 2. PREPARAR descrição preservando numeração para parcelas
                $descricaoFinal = $validatedData['descricao'];

                // Se for parcelado, preservar a numeração original de cada parcela
                if ($grupoLancamento->recorrencia === 'PARCELADO' && $grupoLancamento->num_parcela && $grupoLancamento->qtd_parcelas) {
                    // Extrair descrição base removendo a numeração atual
                    $descricaoBase = preg_replace('/\s*\(\d+\/\d+\)$/', '', $validatedData['descricao']);
                    // Aplicar a numeração correta desta parcela
                    $descricaoFinal = $descricaoBase . ' (' . $grupoLancamento->num_parcela . '/' . $grupoLancamento->qtd_parcelas . ')';

                    Log::info("Preservando numeração: '{$validatedData['descricao']}' → '{$descricaoFinal}'");
                }

                // ATUALIZAR o lançamento
                $result = DB::update(
                    "UPDATE lancamentos SET valor = ?, descricao = ?, status_lancamento = ?, updated_at = NOW() WHERE id = ?",
                    [$valorNovo, $descricaoFinal, $statusNovo, $grupoLancamento->id]
                );

                // 3. APLICAR o efeito do novo lançamento (se for efetivado)
                if ($statusNovo === 'EFETIVADA' && $contaNova && in_array($tipoNovo, ['RECEITA', 'DESPESA'])) {
                    if ($tipoNovo === 'RECEITA') {
                        DB::statement('UPDATE contas SET saldo = saldo + ? WHERE id = ?', [$valorNovo, $contaNova]);
                        Log::info("Aplicando RECEITA EFETIVADA: adicionando {$valorNovo} à conta {$contaNova}");
                    } else { // DESPESA
                        DB::statement('UPDATE contas SET saldo = saldo - ? WHERE id = ?', [$valorNovo, $contaNova]);
                        Log::info("Aplicando DESPESA EFETIVADA: subtraindo {$valorNovo} da conta {$contaNova}");
                    }
                }

                // COMMIT explícito após cada lançamento completo
                DB::statement('COMMIT');

                Log::info("Lançamento {$grupoLancamento->id} atualizado com sucesso");

                if ($result > 0) {
                    $updatedCount++;
                }
            }

            Log::info("Total de lançamentos atualizados com sucesso: {$updatedCount}");

            return response()->json([
                'success' => 'Todos os lançamentos recorrentes atualizados com sucesso!',
                'updated_count' => $updatedCount
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar todos os lançamentos: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json(['error' => 'Erro ao atualizar lançamentos: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Atualizar lançamento com cálculo de saldo (extraído da lógica existente)
     */
    private function updateLancamentoWithBalanceCalculation($lancamento, $validatedData, $commitTransaction = true): ?JsonResponse
    {
        try {
            Log::info("=== INICIANDO updateLancamentoWithBalanceCalculation ===");
            Log::info("Lançamento ID: {$lancamento->id}, Commit: " . ($commitTransaction ? 'true' : 'false'));

            if ($commitTransaction) {
                DB::beginTransaction();
            }

            $this->updateSingleLancamentoData($lancamento, $validatedData);

            Log::info("updateSingleLancamentoData concluído para lançamento {$lancamento->id}");

            if ($commitTransaction) {
                DB::commit();
                Log::info("Transação individual commitada para lançamento {$lancamento->id}");
                return response()->json([
                    'success' => 'Lançamento atualizado com sucesso!',
                    'data' => $lancamento
                ], 200);
            }

            Log::info("Processamento concluído sem commit para lançamento {$lancamento->id}");
            // Não retornar resposta JSON quando dentro de uma transação
            return null;
        } catch (\Exception $e) {
            Log::error("Erro em updateLancamentoWithBalanceCalculation: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            if ($commitTransaction) {
                DB::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Atualizar um lançamento individual com cálculos de saldo
     */
    private function updateSingleLancamentoData($lancamento, $validatedData): void
    {
        // Capturar dados ANTES e DEPOIS
        $statusAnterior = $lancamento->status_lancamento;
        $valorAnterior = $lancamento->valor;
        $contaAnterior = $lancamento->conta_id;
        $tipoAnterior = $lancamento->tipo_lancamento;

        $statusNovo = $validatedData['status_lancamento'];
        $valorNovo = $validatedData['valor'];
        $contaNova = $validatedData['conta_id'];
        $tipoNovo = $validatedData['tipo_lancamento'];

        Log::info("=== EDITANDO LANÇAMENTO {$lancamento->id} ===");
        Log::info("Status: {$statusAnterior} → {$statusNovo}");
        Log::info("Valor: {$valorAnterior} → {$valorNovo}");
        Log::info("Conta: {$contaAnterior} → {$contaNova}");
        Log::info("Tipo: {$tipoAnterior} → {$tipoNovo}");

        // Verificar se alguma mudança afeta o saldo das contas
        $afetaSaldo = ($statusAnterior !== $statusNovo) ||
            ($valorAnterior !== $valorNovo) ||
            ($contaAnterior !== $contaNova) ||
            ($tipoAnterior !== $tipoNovo);

        if ($afetaSaldo) {
            Log::info("Mudanças detectadas que afetam saldo - aplicando lógica de reversão/aplicação");

            // 1. REVERTER o efeito do lançamento anterior (se estava efetivado)
            if ($statusAnterior === 'EFETIVADA' && $contaAnterior && in_array($tipoAnterior, ['RECEITA', 'DESPESA'])) {
                $contaAntiga = Conta::find($contaAnterior);
                if ($contaAntiga) {
                    Log::info("Saldo da conta {$contaAnterior} ANTES da reversão: {$contaAntiga->saldo}");

                    if ($tipoAnterior === 'RECEITA') {
                        $contaAntiga->saldo -= $valorAnterior; // Reverter receita anterior
                        Log::info("Revertendo RECEITA EFETIVADA: subtraindo {$valorAnterior}");
                    } else { // DESPESA
                        $contaAntiga->saldo += $valorAnterior; // Reverter despesa anterior
                        Log::info("Revertendo DESPESA EFETIVADA: adicionando {$valorAnterior}");
                    }

                    Log::info("Saldo da conta {$contaAnterior} DEPOIS da reversão: {$contaAntiga->saldo}");
                    $contaAntiga->save();
                }
            }

            // 2. APLICAR o efeito do novo lançamento (se for efetivado)
            if ($statusNovo === 'EFETIVADA' && $contaNova && in_array($tipoNovo, ['RECEITA', 'DESPESA'])) {
                $contaNova_obj = Conta::find($contaNova);
                if ($contaNova_obj) {
                    Log::info("Saldo da conta {$contaNova} ANTES da aplicação: {$contaNova_obj->saldo}");

                    if ($tipoNovo === 'RECEITA') {
                        $contaNova_obj->saldo += $valorNovo; // Adicionar receita nova
                        Log::info("Aplicando RECEITA EFETIVADA: adicionando {$valorNovo}");
                    } else { // DESPESA
                        $contaNova_obj->saldo -= $valorNovo; // Subtrair despesa nova
                        Log::info("Aplicando DESPESA EFETIVADA: subtraindo {$valorNovo}");
                    }

                    Log::info("Saldo da conta {$contaNova} DEPOIS da aplicação: {$contaNova_obj->saldo}");
                    $contaNova_obj->save();
                }
            }
        } else {
            Log::info("Nenhuma mudança afeta o saldo das contas");
        }

        // Atualizar o lançamento
        Log::info("Dados validados: ", $validatedData);
        $lancamento->update($validatedData);
    }
}
