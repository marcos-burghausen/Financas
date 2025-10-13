<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveWalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara os dados para validação.
     */
    protected function prepareForValidation(): void
    {
        // Converte os valores monetários de string para inteiros (centavos)
        $this->merge([
            'saldo_inicial' => $this->transformMonetaryValue($this->input('saldo_inicial')),
            'limite'        => $this->transformMonetaryValue($this->input('limite')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'id'       => ['nullable', 'integer', Rule::exists('contas', 'id')->where('user_id', $userId)],
            'name'     => [
                'required',
                'min:2',
                'max:20',
                Rule::unique('contas')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId)
                        ->where('tipo_conta', $this->input('tipo_conta'));
                })->ignore($this->input('id')),
            ],
            'tipo_conta'              => ['required', Rule::in(['Carteira', 'Conta Corrente', 'Poupança', 'Investimento', 'Outro', 'Cartão de Crédito'])],
            'color'                   => 'nullable|string',
            'conta_pai_id'            => ['nullable', 'integer', Rule::exists('contas', 'id')->where('user_id', $userId)],
            'icon'                    => 'nullable|string',
            'incluir_em_soma_inicial' => 'nullable|boolean',
            'saldo_inicial'           => 'nullable|integer',
            'descricao'               => 'nullable|string|max:50',
            'limite'                  => 'required_if:tipo_conta,Cartão de Crédito|nullable|integer',
            'dia_fechamento'          => 'required_if:tipo_conta,Cartão de Crédito|nullable|integer|min:1|max:31',
            'dia_vencimento'          => 'required_if:tipo_conta,Cartão de Crédito|nullable|integer|min:1|max:31',
        ];
    }

    /**
     * Transforma uma string monetária (ex: "1.234,56") em um inteiro de centavos.
     */
    private function transformMonetaryValue(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return (int) round((float) $value * 100);
    }
}
