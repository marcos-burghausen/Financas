import { defineStore } from "pinia";
import { ref, computed } from "vue";

interface DashboardSummary {
  saldoAtual: number;
  saldoInicial: number;
  totalReceitas: number;
  totalDespesas: number;
}

export const useDashboardStore = defineStore("dashboard", () => {
  const summary = ref<DashboardSummary>({
    saldoAtual: 0,
    saldoInicial: 0,
    totalReceitas: 0,
    totalDespesas: 0,
  });

  const saldoPrevisto = computed(() => {
    return summary.value.saldoInicial + summary.value.totalReceitas - summary.value.totalDespesas;
  });

  const saldoDisponivel = computed(() => {
    return summary.value.saldoAtual;
  });

  function setSummary(data: DashboardSummary) {
    summary.value = data;
    sessionStorage.setItem("dashboardSummary", JSON.stringify(data));
  }

  function loadFromSession() {
    const stored = sessionStorage.getItem("dashboardSummary");
    if (stored) {
      try {
        summary.value = JSON.parse(stored);
      } catch {
        console.warn("Erro ao carregar resumo do dashboard");
      }
    }
  }

  function clear() {
    summary.value = {
      saldoAtual: 0,
      saldoInicial: 0,
      totalReceitas: 0,
      totalDespesas: 0,
    };
    sessionStorage.removeItem("dashboardSummary");
  }

  return {
    summary,
    saldoPrevisto,
    saldoDisponivel,
    setSummary,
    loadFromSession,
    clear,
  };
});
