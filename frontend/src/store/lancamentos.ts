// frontend/src/store/lancamentos.ts
import http from '@/services/http';
import type { Lancamento } from '@/types/transactions.types';
import { defineStore } from 'pinia';
import { useData } from './data';

export const useLancamentoStore = defineStore('lancamentos', {
  actions: {
    async saveLancamento(payload: Partial<Lancamento>) {
      const dataStore = useData();
      try {
        // O backend espera o valor em centavos
        if (payload.valor) {
            payload.valor = Math.round(Number(payload.valor) * 100);
        }
        
        const { data } = await http.post('/lancamento', payload);

        // O backend deve retornar os dados atualizados para popular a store
        dataStore.setData(data);

      } catch (error) {
        console.error("Erro ao salvar lançamento:", error);
        throw error;
      }
    },
  },
});