import { ref } from "vue";
import http from "@/services/http";
import type { Lancamento, TransactionsData, WalletData } from "@/types";
import { useExpensesStore } from "@/store/expenses";
import { useRevenuesStore } from "@/store/revenues";
import { useWalletsStore } from "@/store/wallets";
import { useMesAno } from "./useMesAno";

interface BuscarDadosMesResponse {
    expenses: TransactionsData;
    revenues: TransactionsData;
    wallets: WalletData;
    mesAno: string;
}

export function useLancamentos(tipo: "receita" | "despesa") {
    const formulario = ref(false);
    const selectedRelease = ref<Lancamento | undefined>();
    const loading = ref(false);
    const error = ref<string | null>(null);
    
    const useExpenses = useExpensesStore();
    const useRevenues = useRevenuesStore();
    const useWallets = useWalletsStore();
    const { mesAno } = useMesAno();
    
    // Cache para evitar múltiplas requisições
    const cache = new Map<string, { data: BuscarDadosMesResponse; timestamp: number }>();
    const CACHE_DURATION = 5 * 60 * 1000; // 5 minutos
    
    const openCreateForm = () => { 
        selectedRelease.value = undefined;
        formulario.value = true; 
    };
    
    const openEditForm = (lancamento: Lancamento) => {
        selectedRelease.value = lancamento;
        formulario.value = true;
    };
    
    const closeForm = () => { 
        formulario.value = false;
        selectedRelease.value = undefined;
    };
    
    const updateData = async (forceRefresh = false) => {
        if (loading.value) return;
        
        const cacheKey = `dados_${mesAno.value}`;
        const cached = cache.get(cacheKey);
        
        // Retorna do cache se válido e não forçar refresh
        if (!forceRefresh && cached && Date.now() - cached.timestamp < CACHE_DURATION) {
            if (tipo === "receita") {
                useRevenues.setRevenuesData(cached.data.revenues);
            } else {
                useExpenses.setExpensesData(cached.data.expenses);
            }
            useWallets.setWalletsData(cached.data.wallets);
            return;
        }
        
        loading.value = true;
        error.value = null;
        
        try {
            const res = await http.post<BuscarDadosMesResponse>("/buscar-dados-mes", { 
                mesAno: mesAno.value 
            });
            
            // Atualiza stores
            if (tipo === "receita") {
                useRevenues.setRevenuesData(res.data.revenues);
            } else {
                useExpenses.setExpensesData(res.data.expenses);
            }
            useWallets.setWalletsData(res.data.wallets);
            
            // Atualiza cache
            cache.set(cacheKey, {
                data: res.data,
                timestamp: Date.now(),
            });
        } catch (err: any) {
            error.value = err.message || "Erro ao atualizar dados";
            console.error("Erro ao atualizar dados:", err);
            throw err;
        } finally {
            loading.value = false;
        }
    };
    
    const invalidateCache = (mes?: string) => {
        if (mes) {
            cache.delete(`dados_${mes}`);
        } else {
            cache.clear();
        }
    };
    
    return { 
        formulario, 
        selectedRelease, 
        loading,
        error,
        openCreateForm,
        openEditForm, 
        closeForm, 
        updateData,
        invalidateCache,
    };
}
