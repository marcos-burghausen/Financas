import { budgetService } from "@/services/budgetService";
import type { Budget, BudgetData, BudgetFormData } from "@/types/budget.types";
import { defineStore } from "pinia";
import { computed, ref } from "vue";

export const useBudgetStore = defineStore("budget", () => {
  // Estado padrão
  const getDefaultBudgetData = (): BudgetData => ({
    budgets: [],
    totalOrcamento: 0,
    totalGasto: 0,
    saldoRestante: 0,
    percentualGasto: 0,
    metaEconomia: 0,
  });

  // Estado reativo
  const budgetData = ref<BudgetData>(getDefaultBudgetData());
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Computed properties
  const summary = computed(() => {
    const totalOrcamento = budgetData.value.budgets.reduce((acc, b) => acc + b.orcado, 0);
    const totalGasto = budgetData.value.budgets.reduce((acc, b) => acc + b.gasto, 0);
    const saldoRestante = totalOrcamento - totalGasto;
    const percentualGasto = totalOrcamento > 0 ? (totalGasto / totalOrcamento) * 100 : 0;
    const metaEconomia = budgetData.value.metaEconomia || totalOrcamento * 0.15;

    return {
      totalOrcamento,
      totalGasto,
      saldoRestante,
      percentualGasto,
      metaEconomia,
    };
  });

  const budgetsByMonth = computed(() => {
    return (mesAno: string) => {
      return budgetData.value.budgets.filter(b => b.mes_ano === mesAno);
    };
  });

  // Helper para converter dados da API para o formato do frontend
  function convertApiBudgetToFrontend(apiBudget: any): Budget {
    const percentual = apiBudget.percentual_gasto || 0;
    let color = "success";
    if (percentual >= 100) color = "error";
    else if (percentual >= 80) color = "warning";

    const categoryIcons: Record<string, string> = {
      "Alimentação": "mdi-food",
      "Transporte": "mdi-car",
      "Saúde": "mdi-medical-bag",
      "Educação": "mdi-school",
      "Lazer": "mdi-gamepad-variant",
      "Moradia": "mdi-home",
      "Vestuário": "mdi-tshirt-crew",
      "Utilidades": "mdi-tools",
      "Investimentos": "mdi-trending-up",
      "Outros": "mdi-dots-horizontal",
    };

    return {
      id: apiBudget.id,
      categoria: apiBudget.categoria,
      orcado: apiBudget.valor_orcado, // já vem em centavos da API
      gasto: apiBudget.gasto_real, // já vem em centavos da API
      restante: apiBudget.saldo_restante, // já vem em centavos da API
      percentual: percentual,
      icon: categoryIcons[apiBudget.categoria] || "mdi-dots-horizontal",
      color: color,
      observacao: apiBudget.observacao || "",
      mes_ano: apiBudget.mes_ano,
      transacoes: apiBudget.transacoes?.map((t: any) => ({
        id: t.id,
        descricao: t.descricao,
        valor: t.valor,
        data: t.data_vencimento,
        categoria: apiBudget.categoria,
      })) || [],
    };
  }

  // Actions da API
  async function fetchBudgets(mesAno?: string, categoria?: string): Promise<void> {
    loading.value = true;
    error.value = null;

    try {
      const response = await budgetService.getBudgets(mesAno, categoria);
      
      if (response.success && response.data) {
        const budgets = response.data.budgets.map(convertApiBudgetToFrontend);
        const resumo = response.data.resumo;

        budgetData.value = {
          budgets,
          totalOrcamento: resumo.total_orcado,
          totalGasto: resumo.total_gasto,
          saldoRestante: resumo.saldo_restante,
          percentualGasto: resumo.percentual_gasto_geral,
          metaEconomia: resumo.meta_economia,
        };

        localStorage.setItem("budgetData", JSON.stringify(budgetData.value));
      } else {
        throw new Error(response.message || "Erro ao buscar orçamentos");
      }
    } catch (err: any) {
      error.value = err.message || "Erro ao carregar orçamentos";
      console.error("Erro ao buscar orçamentos:", err);
    } finally {
      loading.value = false;
    }
  }

  async function createBudget(budgetFormData: BudgetFormData): Promise<Budget | null> {
    loading.value = true;
    error.value = null;

    try {
      const response = await budgetService.createBudget(budgetFormData);
      
      if (response.success && response.data) {
        const newBudget = convertApiBudgetToFrontend(response.data);
        budgetData.value.budgets.push(newBudget);
        updateSummary();
        localStorage.setItem("budgetData", JSON.stringify(budgetData.value));
        return newBudget;
      } else {
        throw new Error(response.message || "Erro ao criar orçamento");
      }
    } catch (err: any) {
      error.value = err.message || "Erro ao criar orçamento";
      console.error("Erro ao criar orçamento:", err);
      return null;
    } finally {
      loading.value = false;
    }
  }

  async function updateBudget(budgetId: number, budgetFormData: Partial<BudgetFormData>): Promise<boolean> {
    loading.value = true;
    error.value = null;

    try {
      const response = await budgetService.updateBudget(budgetId, budgetFormData);
      
      if (response.success && response.data) {
        const updatedBudget = convertApiBudgetToFrontend(response.data);
        const index = budgetData.value.budgets.findIndex(b => b.id === budgetId);
        
        if (index !== -1) {
          budgetData.value.budgets[index] = updatedBudget;
          updateSummary();
          localStorage.setItem("budgetData", JSON.stringify(budgetData.value));
        }
        return true;
      } else {
        throw new Error(response.message || "Erro ao atualizar orçamento");
      }
    } catch (err: any) {
      error.value = err.message || "Erro ao atualizar orçamento";
      console.error("Erro ao atualizar orçamento:", err);
      return false;
    } finally {
      loading.value = false;
    }
  }

  async function deleteBudget(budgetId: number): Promise<boolean> {
    loading.value = true;
    error.value = null;

    try {
      const response = await budgetService.deleteBudget(budgetId);
      
      if (response.success) {
        budgetData.value.budgets = budgetData.value.budgets.filter(b => b.id !== budgetId);
        updateSummary();
        localStorage.setItem("budgetData", JSON.stringify(budgetData.value));
        return true;
      } else {
        throw new Error(response.message || "Erro ao excluir orçamento");
      }
    } catch (err: any) {
      error.value = err.message || "Erro ao excluir orçamento";
      console.error("Erro ao excluir orçamento:", err);
      return false;
    } finally {
      loading.value = false;
    }
  }

  async function fetchCategorias(): Promise<string[]> {
    try {
      const response = await budgetService.getCategorias();
      
      if (response.success && response.data) {
        return response.data;
      } else {
        throw new Error(response.message || "Erro ao buscar categorias");
      }
    } catch (err: any) {
      error.value = err.message || "Erro ao buscar categorias";
      console.error("Erro ao buscar categorias:", err);
      return [];
    }
  }

  // Actions locais (mantidas para compatibilidade)
  function setBudgetData(data: BudgetData): void {
    budgetData.value = {
      budgets: data?.budgets ?? [],
      totalOrcamento: data?.totalOrcamento ?? 0,
      totalGasto: data?.totalGasto ?? 0,
      saldoRestante: data?.saldoRestante ?? 0,
      percentualGasto: data?.percentualGasto ?? 0,
      metaEconomia: data?.metaEconomia ?? 0,
    };
    localStorage.setItem("budgetData", JSON.stringify(budgetData.value));
  }

  function setBudgets(budgets: Budget[]): void {
    if (budgetData.value) {
      budgetData.value.budgets = budgets;
      updateSummary();
      localStorage.setItem("budgetData", JSON.stringify(budgetData.value));
    }
  }

  function addBudget(budget: Budget): void {
    budgetData.value.budgets.push(budget);
    updateSummary();
    localStorage.setItem("budgetData", JSON.stringify(budgetData.value));
  }

  function removeBudget(budgetId: number): void {
    budgetData.value.budgets = budgetData.value.budgets.filter(b => b.id !== budgetId);
    updateSummary();
    localStorage.setItem("budgetData", JSON.stringify(budgetData.value));
  }

  function updateSummary(): void {
    const totalOrcamento = budgetData.value.budgets.reduce((acc, b) => acc + b.orcado, 0);
    const totalGasto = budgetData.value.budgets.reduce((acc, b) => acc + b.gasto, 0);
    const saldoRestante = totalOrcamento - totalGasto;
    const percentualGasto = totalOrcamento > 0 ? (totalGasto / totalOrcamento) * 100 : 0;
    const metaEconomia = totalOrcamento * 0.15;

    budgetData.value.totalOrcamento = totalOrcamento;
    budgetData.value.totalGasto = totalGasto;
    budgetData.value.saldoRestante = saldoRestante;
    budgetData.value.percentualGasto = percentualGasto;
    budgetData.value.metaEconomia = metaEconomia;
  }

  function loadFromSession(): void {
    const stored = localStorage.getItem("budgetData");
    if (stored) {
      try {
        const data = JSON.parse(stored);
        setBudgetData(data);
      } catch (e) {
        console.warn("Erro ao carregar dados do orçamento do localStorage:", e);
        clear();
      }
    }
  }

  function clear(): void {
    budgetData.value = getDefaultBudgetData();
    localStorage.removeItem("budgetData");
  }

  // Utility functions
  function getBudgetByCategory(categoria: string, mesAno?: string): Budget | undefined {
    return budgetData.value.budgets.find(b => 
      b.categoria === categoria && 
      (!mesAno || b.mes_ano === mesAno)
    );
  }

  function getCategoriesWithBudget(mesAno?: string): string[] {
    return budgetData.value.budgets
      .filter(b => !mesAno || b.mes_ano === mesAno)
      .map(b => b.categoria);
  }

  function updateBudgetSpent(categoria: string, valor: number, mesAno?: string): void {
    const budget = getBudgetByCategory(categoria, mesAno);
    if (budget) {
      budget.gasto += valor;
      budget.restante = budget.orcado - budget.gasto;
      budget.percentual = budget.orcado > 0 ? (budget.gasto / budget.orcado) * 100 : 0;
      
      // Atualizar cor baseada no percentual
      if (budget.percentual >= 100) budget.color = "error";
      else if (budget.percentual >= 80) budget.color = "warning";
      else budget.color = "success";
      
      updateSummary();
      localStorage.setItem("budgetData", JSON.stringify(budgetData.value));
    }
  }

  return {
    // State
    budgetData,
    loading,
    error,
    
    // Computed
    summary,
    budgetsByMonth,
    
    // API Actions
    fetchBudgets,
    createBudget,
    updateBudget,
    deleteBudget,
    fetchCategorias,
    
    // Local Actions (for compatibility)
    setBudgetData,
    setBudgets,
    addBudget,
    removeBudget,
    updateSummary,
    loadFromSession,
    clear,
    
    // Utilities
    getBudgetByCategory,
    getCategoriesWithBudget,
    updateBudgetSpent,
  };
});
