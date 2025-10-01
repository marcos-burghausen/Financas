<?php

namespace App\Http\Controllers;

use App\Models\CreditCardInvoice;
use App\Models\Lancamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LancamentoController extends Controller
{
    public function saveLancamento(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();

        if ($data['tipo'] === 'CartaoCredito') {
            $this->handleCreditCardLancamento($data);
        } else {
            if (isset($data['id']) && $data['id'] !== '') {
                $this->updateLancamento($data);
            } else {
                $this->createLancamento($data);
            }
        }
    }

    private function handleCreditCardLancamento($data)
    {
        $invoice = CreditCardInvoice::firstOrCreate(
            [
                'conta_id' => $data['conta_id'],
                'mes' => $data['mes_fatura'],
                'ano' => $data['ano_fatura'],
            ],
            [
                'valor_total' => 0,
                'data_fechamento' => now(),
                'data_vencimento' => now(),
                'paga' => false,
            ]
        );

        if (isset($data['id']) && $data['id'] !== '') {
            $this->updateLancamento($data, $invoice->id);
        } else {
            $this->createLancamento($data, $invoice->id);
        }
    }

    private function createLancamento($data, $invoiceId = null)
    {
        $data['credit_card_invoice_id'] = $invoiceId;
        if ($data['parcelado']) {
            for ($i = 0; $i < $data['qtd_parcelas']; $i++) {
                $lancamento = new Lancamento($data);
                $lancamento->parcela_atual = $i + 1;
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
