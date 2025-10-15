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
            'recorrencia'       => $this->transformRecorrencia(),
            // 'status_lancamento' => $this->transformStatus(),
            'tipo_parcela'      => $this->input('tipo_parcela') ? strtoupper($this->input('tipo_parcela')) : null,
            'periodicidade'     => $this->input('periodicidade') ? strtoupper($this->input('periodicidade')) : null,
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
            'status_lancamento'    => 'required | in:EFETIVADA,PENDENTE',
            'categoria'            => 'required | string|max:30',
            'subcategoria'         => 'required | string|max:30',
            'observacoes'          => 'nullable | string | max:1000',
            'conta_id'             => 'required | exists:contas,id',
            'mesAno'               => 'required | string | regex:/^\d{4}-\d{2}$/',
            'invoice_id'           => 'nullable | required_if:tipo_lancamento,CARTAO_CREDITO | exists:credit_card_invoices,id',
            'editScope'            => 'nullable|string|in:apenas esta,esta e as próximas,todas,apenas este mês,mês atual e os próximos',
            'fatura'               => 'nullable | required_if:tipo_lancamento,CARTAO_CREDITO | regex:/^[A-Z]{3}\/\d{4}$/',
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
        $map = [
            'Receita' => 'RECEITA',
            'Despesa' => 'DESPESA',
            'Cartão de Crédito' => 'CARTAO_CREDITO',
        ];
        return $map[$this->input('tipo_lancamento')] ?? '';
    }

    private function transformRecorrencia(): string
    {
        $map = [
            'Não recorrente' => 'NAO_RECORRENTE',
            'Parcelado' => 'PARCELADO',
            'Fixa' => 'FIXA',
        ];
        return $map[$this->input('recorrencia')] ?? 'NAO_RECORRENTE';
    }

    // private function transformStatus(): string
    // {
    //     $map = [
    //         'Pendente' => 'PENDENTE',
    //         'Efetivada' => 'EFETIVADA',
    //     ];
    //     return $map[$this->input('status_lancamento')] ?? 'PENDENTE';
    // }
}
