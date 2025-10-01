// frontend/src/store/creditCard.ts
import http from "@/services/http";
import type { CreditCardInvoice } from "@/types/transactions.types";
import { defineStore } from "pinia";
import { useDataStore } from './data';

interface CreditCardState {
  invoices: CreditCardInvoice[]
  isLoading: boolean
}

export const useCreditCardStore = defineStore("creditCard", {
  state: (): CreditCardState => ({
    invoices: [],
    isLoading: false,
  }),

  actions: {
    async fetchInvoices(cardAccountId: number) {
      if (!cardAccountId) {
        this.invoices = [];
        return;
      }
      this.isLoading = true;
      try {
        const { data } = await http.get(`/contas/${cardAccountId}/invoices`);
        this.invoices = data.invoices;
      } catch (error) {
        console.error("Erro ao buscar faturas:", error);
        this.invoices = [];
      } finally {
        this.isLoading = false;
      }
    },

    async payInvoice(payload: { invoice_id: number; conta_pagamento_id: number; mesAno: string }) {
      this.isLoading = true;
      const dataStore = useDataStore();
      try {
        const { data } = await http.post("/lancamento/pagar-fatura", payload);
        dataStore.setData(data); // Atualiza dados globais
        const invoice = this.invoices.find(inv => inv.id === payload.invoice_id);
        if (invoice) {
          invoice.status = "Paga";
        }
      } catch (error) {
        console.error("Erro ao pagar fatura:", error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async createRefund(refundData: { id: number; valor_estorno: number }) {
    try {
      const response = await http.post('/create-refund', refundData);
      // Lógica para atualizar o estado após o estorno
      return response;
    } catch (error) {
      console.error('Erro ao criar estorno:', error);
    }
  }
}
});