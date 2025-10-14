import { ref } from "vue";
import http from "@/services/http";
import type { WalletData } from "@/types";

export function useWalletsData() {
  const walletsData = ref<WalletData | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);
  
  const cache = new Map<string, { data: WalletData; timestamp: number }>();
  const CACHE_DURATION = 5 * 60 * 1000; // 5 minutos

  async function fetchWallets(mesAno: string) {
    const cacheKey = `wallets_${mesAno}`;
    const cached = cache.get(cacheKey);

    // Retorna do cache se válido
    if (cached && Date.now() - cached.timestamp < CACHE_DURATION) {
      walletsData.value = cached.data;
      return cached.data;
    }

    loading.value = true;
    error.value = null;

    try {
      const response = await http.get("/user-data/wallets", {
        params: { mesAno },
      });

      walletsData.value = response.data.wallets;
      
      // Armazena no cache
      cache.set(cacheKey, {
        data: response.data.wallets,
        timestamp: Date.now(),
      });

      return response.data.wallets;
    } catch (err: any) {
      error.value = err.message || "Erro ao buscar carteiras";
      console.error("Erro ao buscar carteiras:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  function invalidateCache(mesAno?: string) {
    if (mesAno) {
      cache.delete(`wallets_${mesAno}`);
    } else {
      cache.clear();
    }
  }

  return {
    walletsData,
    loading,
    error,
    fetchWallets,
    invalidateCache,
  };
}
