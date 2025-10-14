// frontend/src/store/lancamentos.ts
import http from '@/services/http';
import type { Lancamento } from '@/types/transactions.types';
import { defineStore } from 'pinia';
import { useDataStore } from './data';

export const useLancamentoStore = defineStore('lancamentos', {
  actions: {
    async saveLancamento(payload: Partial<Lancamento>) {
      const dataStore = useDataStore();
      try {
        // O backend espera o valor em centavos
        if (payload.valor) {
            // payload.valor = Math.round(Number(payload.valor) * 100);
            payload.valor = String(Math.round(Number(payload.valor) * 100));
        }
        
        const { data } = await http.post('/lancamento', payload);

        // O backend deve retornar os dados atualizados para popular a store
        // dataStore.setData(data);
        if (data.totalCreditCard !== undefined) {
          dataStore.setTotalCreditCard(data.totalCreditCard);
        }
        if (data.totalBalance !== undefined) {
          dataStore.setTotalBalance(data.totalBalance);
        }

      } catch (error) {
        console.error("Erro ao salvar lançamento:", error);
        throw error;
      }
    },
  },
});