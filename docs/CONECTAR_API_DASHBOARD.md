# 🔌 Conectar Dashboard com API Real

## 📋 Guia de Integração

### Estrutura Atual (Mock)

```typescript
// Dados hardcoded
summary.value = {
  receitasMes: 850000,
  despesasMes: 520000,
  saldoAtual: 330000,
  ...
}

// Carregados em onMounted
onMounted(() => {
  loadDashboardData(); // ← Dados mock
})
```

---

## 🔄 Como Integrar com API Real

### Passo 1: Criar um Service para Dashboard

**Arquivo**: `src/services/dashboardService.ts`

```typescript
import axios from "axios";

const API = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000/api",
  headers: {
    Authorization: `Bearer ${localStorage.getItem("sanctum_token")}`,
  },
});

export const dashboardService = {
  // Obter resumo do dashboard
  async getSummary(mesAno?: string) {
    try {
      const response = await API.get("/dashboard/summary", {
        params: { mes_ano: mesAno },
      });
      return response.data;
    } catch (error) {
      console.error("Erro ao obter resumo:", error);
      throw error;
    }
  },

  // Obter dados dos gráficos
  async getChartData(mesAno?: string) {
    try {
      const response = await API.get("/dashboard/charts", {
        params: { mes_ano: mesAno },
      });
      return response.data;
    } catch (error) {
      console.error("Erro ao obter gráficos:", error);
      throw error;
    }
  },

  // Obter transações recentes
  async getRecentTransactions(limit = 5) {
    try {
      const response = await API.get("/dashboard/transactions", {
        params: { limit },
      });
      return response.data;
    } catch (error) {
      console.error("Erro ao obter transações:", error);
      throw error;
    }
  },

  // Obter alertas
  async getAlerts() {
    try {
      const response = await API.get("/dashboard/alerts");
      return response.data;
    } catch (error) {
      console.error("Erro ao obter alertas:", error);
      throw error;
    }
  },
};
```

---

### Passo 2: Atualizar DashboardView.vue

**Importar o serviço**:

```typescript
import { dashboardService } from "@/services/dashboardService";
import { useUserStore } from "@/store";

const userStore = useUserStore();
```

**Atualizar loadDashboardData()**:

```typescript
const loadDashboardData = async () => {
  try {
    loading.value = true;

    // Obter mês/ano selecionado (do userStore ou padrão atual)
    const mesAno = userStore.getMesAno(); // ou props se tiver filtro

    // Carregar dados em paralelo
    const [summaryData, chartData, transactions, alerts] = await Promise.all([
      dashboardService.getSummary(mesAno),
      dashboardService.getChartData(mesAno),
      dashboardService.getRecentTransactions(5),
      dashboardService.getAlerts(),
    ]);

    // Atualizar dados
    summary.value = summaryData;

    // Configurar gráficos com dados reais
    chartOptions.value.bar = chartData.barOptions;
    chartSeries.value.bar = chartData.barSeries;
    chartOptions.value.pie = chartData.pieOptions;
    chartSeries.value.pie = chartData.pieSeries;

    // Transações
    recentTransactions.value = transactions;

    // Alertas
    alerts.value = alerts;

    loading.value = false;
  } catch (error) {
    console.error("Erro ao carregar dashboard:", error);
    loading.value = false;
    // Mostrar erro ao usuário (toast/snackbar)
  }
};
```

---

### Passo 3: Backend - Endpoints Necessários

**Arquivo**: `backend/routes/api.php`

```php
// Grupo de rotas autenticadas
Route::middleware('auth:sanctum')->group(function () {

    // Dashboard endpoints
    Route::prefix('dashboard')->group(function () {

        // Resumo do dashboard
        Route::get('/summary', [DashboardController::class, 'summary']);

        // Dados dos gráficos
        Route::get('/charts', [DashboardController::class, 'charts']);

        // Transações recentes
        Route::get('/transactions', [DashboardController::class, 'recentTransactions']);

        // Alertas
        Route::get('/alerts', [DashboardController::class, 'alerts']);
    });
});
```

---

### Passo 4: Backend - Controller

**Arquivo**: `backend/app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receita;
use App\Models\Despesa;
use App\Models\Conta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Obter resumo do dashboard
     */
    public function summary(Request $request)
    {
        $mesAno = $request->query('mes_ano');
        $user = auth()->user();

        if ($mesAno) {
            $data = Carbon::createFromFormat('Y-m', $mesAno);
        } else {
            $data = now();
        }

        $mesInicio = $data->startOfMonth();
        $mesFim = $data->endOfMonth();

        // Receitas do mês
        $receitasMes = Receita::where('user_id', $user->id)
            ->whereBetween('data', [$mesInicio, $mesFim])
            ->sum('valor');

        // Despesas do mês
        $despesasMes = Despesa::where('user_id', $user->id)
            ->whereBetween('data', [$mesInicio, $mesFim])
            ->sum('valor');

        // Saldo total
        $saldoAtual = Conta::where('user_id', $user->id)->sum('saldo');

        // Pendências
        $pendencias = Despesa::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->sum('valor');

        // Contagens
        $receitasRecebidas = Receita::where('user_id', $user->id)
            ->whereBetween('data', [$mesInicio, $mesFim])
            ->count();

        $despesasPagas = Despesa::where('user_id', $user->id)
            ->whereBetween('data', [$mesInicio, $mesFim])
            ->where('status', 'pago')
            ->count();

        $totalPendencias = Despesa::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->count();

        return response()->json([
            'receitasMes' => $receitasMes * 100, // em centavos
            'despesasMes' => $despesasMes * 100,
            'saldoAtual' => $saldoAtual * 100,
            'pendencias' => $pendencias * 100,
            'receitasRecebidas' => $receitasRecebidas,
            'despesasPagas' => $despesasPagas,
            'totalPendencias' => $totalPendencias
        ]);
    }

    /**
     * Obter dados para gráficos
     */
    public function charts(Request $request)
    {
        $mesAno = $request->query('mes_ano');
        $user = auth()->user();

        // Últimos 6 meses
        $meses = [];
        $receitasData = [];
        $despesasData = [];

        for ($i = 5; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $mesInicio = $data->startOfMonth();
            $mesFim = $data->endOfMonth();

            $meses[] = $data->format('M');

            $receitas = Receita::where('user_id', $user->id)
                ->whereBetween('data', [$mesInicio, $mesFim])
                ->sum('valor');

            $despesas = Despesa::where('user_id', $user->id)
                ->whereBetween('data', [$mesInicio, $mesFim])
                ->sum('valor');

            $receitasData[] = $receitas * 100;
            $despesasData[] = $despesas * 100;
        }

        // Distribuição de despesas por categoria
        $categorias = \App\Models\Categoria::where('user_id', $user->id)
            ->with(['despesas' => function($q) use ($mesInicio, $mesFim) {
                $q->whereBetween('data', [$mesInicio, $mesFim]);
            }])
            ->get();

        $categoriasNomes = [];
        $categoriasValores = [];

        foreach ($categorias as $categoria) {
            $total = $categoria->despesas->sum('valor');
            if ($total > 0) {
                $categoriasNomes[] = $categoria->nome;
                $categoriasValores[] = $total * 100;
            }
        }

        return response()->json([
            'barOptions' => [
                'chart' => ['type' => 'bar'],
                'xaxis' => ['categories' => $meses]
            ],
            'barSeries' => [
                ['name' => 'Receitas', 'data' => $receitasData],
                ['name' => 'Despesas', 'data' => $despesasData]
            ],
            'pieOptions' => [
                'labels' => $categoriasNomes
            ],
            'pieSeries' => $categoriasValores
        ]);
    }

    /**
     * Transações recentes
     */
    public function recentTransactions(Request $request)
    {
        $limit = $request->query('limit', 5);
        $user = auth()->user();

        // Combinar receitas e despesas
        $receitas = Receita::where('user_id', $user->id)
            ->select('id', 'descricao', 'valor', 'data')
            ->addSelect(\DB::raw("'receita' as type"))
            ->limit($limit);

        $despesas = Despesa::where('user_id', $user->id)
            ->select('id', 'descricao', 'valor', 'data')
            ->addSelect(\DB::raw("'despesa' as type"))
            ->limit($limit)
            ->unionAll($receitas)
            ->orderByDesc('data')
            ->get();

        return response()->json($despesas);
    }

    /**
     * Alertas
     */
    public function alerts(Request $request)
    {
        $user = auth()->user();
        $alerts = [];

        // Alerta 1: Cartão de crédito próximo do limite
        // ... lógica de alerta

        // Alerta 2: Meta mensal
        // ... lógica de meta

        // Alerta 3: Investimentos
        // ... lógica de investimentos

        return response()->json($alerts);
    }
}
```

---

## 🧪 Testes

### Teste 1: Verificar API

```bash
# No terminal
curl -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost:8000/api/dashboard/summary
```

### Teste 2: Verificar Dados

```javascript
// No console do navegador
dashboardService.getSummary().then((data) => console.log(data));
```

### Teste 3: Verificar Erro

```javascript
// Forçar erro
dashboardService
  .getSummary("2020-01")
  .then((data) => console.log(data))
  .catch((err) => console.error("Erro:", err));
```

---

## 🎯 Variáveis de Ambiente

**Arquivo**: `.env` ou `.env.local`

```env
VITE_API_URL=http://localhost:8000/api
VITE_APP_NAME="MrFinancas"
```

**Uso em TypeScript**:

```typescript
const API_URL = import.meta.env.VITE_API_URL;
```

---

## 📊 Formato de Resposta Esperado

### Summary

```json
{
  "receitasMes": 850000,
  "despesasMes": 520000,
  "saldoAtual": 330000,
  "pendencias": 150000,
  "receitasRecebidas": 12,
  "despesasPagas": 18,
  "totalPendencias": 5
}
```

### Charts

```json
{
  "barOptions": {...},
  "barSeries": [
    {"name": "Receitas", "data": [...]},
    {"name": "Despesas", "data": [...]}
  ],
  "pieOptions": {...},
  "pieSeries": [...]
}
```

### Transactions

```json
[
  {
    "id": 1,
    "descricao": "Salário",
    "valor": 450000,
    "data": "2025-10-15",
    "type": "receita"
  },
  ...
]
```

### Alerts

```json
[
  {
    "tipo": "warning",
    "titulo": "Cartão de Crédito",
    "mensagem": "Você atingiu 78% do limite",
    "icon": "mdi-credit-card",
    "type": "warning"
  },
  ...
]
```

---

## 🔐 Segurança

### Headers Necessários

```typescript
headers: {
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

### Validação Backend

```php
// No controller
$this->authorize('viewDashboard', $user);
```

### Rate Limiting

```php
// No route
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/dashboard/summary', ...);
});
```

---

## ⚠️ Possíveis Erros

| Erro              | Causa            | Solução            |
| ----------------- | ---------------- | ------------------ |
| 401 Unauthorized  | Token inválido   | Renovar token      |
| 403 Forbidden     | Sem permissão    | Verificar role     |
| 404 Not Found     | Rota não existe  | Verificar endpoint |
| 422 Unprocessable | Validação falhou | Verificar params   |
| 500 Server Error  | Erro backend     | Verificar logs     |

---

## 🚀 Melhorias Futuras

1. **Caching**

   - Cache de 5 minutos para summary
   - Invalidar ao adicionar transação

2. **Paginação**

   - Transações recentes com mais itens
   - Carregar mais ao scroll

3. **Filtros**

   - Por período
   - Por categoria
   - Por conta

4. **Real-time**
   - WebSocket para atualizações
   - Push notifications

---

**Versão**: 1.0.0  
**Status**: Em Desenvolvimento  
**Data**: 17/10/2025
