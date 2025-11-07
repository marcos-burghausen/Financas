<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLancamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Garante que o usuário está autenticado.
        return true;
    }

    /**
     * Prepara os dados para validação.
     * É aqui que a mágica da transformação acontece!
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'valor'             => $this->transformValor(),
            'tipo_lancamento'   => $this->transformTipoLancamento(),
            'data_vencimento'   => $this->input('data_vencimento'), // Mantém formato YYYY-MM-DD
            'data_lancamento'   => $this->input('data_lancamento'), // Mantém formato YYYY-MM-DD
            'data_efetivacao'   => $this->input('data_efetivacao'), // Mantém formato YYYY-MM-DD
            'recorrencia'       => $this->transformRecorrencia(),
            'status_lancamento' => $this->transformStatus(),
            'tipo_parcela'      => $this->input('tipo_parcela') ? strtoupper($this->input('tipo_parcela')) : null,
            'periodicidade'     => $this->input('periodicidade') ? strtoupper($this->input('periodicidade')) : null,
            // Renomear fatura para fatura_vigente se for cartão de crédito
            'fatura_vigente'    => $this->input('fatura_vigente') ?? $this->input('fatura'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id'                   => 'nullable | integer',
            'installment_group_id' => 'nullable | uuid',
            'descricao'            => 'required | string|max:50',
            'valor'                => 'required | integer|min:0.01',
            'tipo_lancamento'      => 'required | in:RECEITA,DESPESA,CARTAO_CREDITO',
            'recorrencia'          => 'required | in:NAO_RECORRENTE,PARCELADO,FIXA',
            'qtd_parcelas'         => 'nullable | integer | min:2 | required_if:recorrencia,PARCELADO',
            'tipo_parcela'         => 'nullable | string | in:TOTAL,PARCELA',
            'num_parcela'          => 'nullable | integer | min:1',
            'periodicidade'        => 'nullable | string | in:MENSAL,DIARIO,SEMANAL,QUINZENAL,TRIMESTRAL,ANUAL',
            'data_vencimento'      => 'required | date',
            'data_lancamento'      => 'required | date',
            'data_efetivacao'      => 'nullable | date',
            'subcategoria'         => 'required | string | max:30',
            // Status não é obrigatório para CARTAO_CREDITO (vinculado à fatura)
            'status_lancamento'    => 'nullable | required_unless:tipo_lancamento,CARTAO_CREDITO | in:EFETIVADA,PENDENTE',
            'categoria'            => 'required | string|max:30',
            'subcategoria'         => 'required | string|max:30',
            'observacoes'          => 'nullable | string | max:1000',
            'conta_id'             => 'required | exists:contas,id',
            // mesAno não é obrigatório para CARTAO_CREDITO (usa fatura_vigente)
            'mesAno'               => 'nullable | required_unless:tipo_lancamento,CARTAO_CREDITO | string | regex:/^\d{4}-\d{2}$/',
            'invoice_id'           => 'nullable | required_if:tipo_lancamento,CARTAO_CREDITO | exists:credit_card_invoices,id',
            'editScope'            => 'nullable|string|in:apenas_esta,esta_e_proximas,todas',
            'fatura_vigente'       => 'nullable | required_if:tipo_lancamento,CARTAO_CREDITO | regex:/^\d{2}\/\d{4}$/',
            'cartao_credito_id'    => 'nullable | required_if:tipo_lancamento,CARTAO_CREDITO | exists:contas,id',
        ];
    }

    // --- MÉTODOS DE TRANSFORMAÇÃO ---

    private function transformValor(): int
    {
        $valor = $this->input('valor'); // Ex: "1.234,56"
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        return (int) round((float) $valor * 100);
    }

    private function transformTipoLancamento(): string
    {
        $valor = $this->input('tipo_lancamento');

        // Se já estiver em formato MAIÚSCULO (CARTAO_CREDITO), retorna como está
        if (in_array($valor, ['RECEITA', 'DESPESA', 'CARTAO_CREDITO'])) {
            return $valor;
        }

        // Senão, transforma do formato PT
        $map = [
            'Receita' => 'RECEITA',
            'Despesa' => 'DESPESA',
            'Cartão de Crédito' => 'CARTAO_CREDITO',
        ];
        return $map[$valor] ?? '';
    }

    private function transformRecorrencia(): string
    {
        $valor = $this->input('recorrencia');

        // Se já estiver em formato MAIÚSCULO (FIXA, PARCELADO, etc), retorna como está
        if (in_array($valor, ['NAO_RECORRENTE', 'PARCELADO', 'FIXA'])) {
            return $valor;
        }

        // Senão, transforma do formato PT
        $map = [
            'Não recorrente' => 'NAO_RECORRENTE',
            'Parcelado' => 'PARCELADO',
            'Fixa' => 'FIXA',
        ];
        return $map[$valor] ?? 'NAO_RECORRENTE';
    }

    private function transformStatus(): string
    {
        $valor = $this->input('status_lancamento');

        // Se já estiver em formato MAIÚSCULO (PENDENTE, EFETIVADA), retorna como está
        if (in_array($valor, ['PENDENTE', 'EFETIVADA'])) {
            return $valor;
        }

        // Senão, transforma do formato PT
        $map = [
            'Pendente' => 'PENDENTE',
            'Efetivada' => 'EFETIVADA',
        ];
        return $map[$valor] ?? 'PENDENTE';
    }
}
