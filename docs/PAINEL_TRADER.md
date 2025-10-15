# 📊 Painel do Trader - Documentação

## 🎯 Visão Geral

O **Painel do Trader** é uma interface dedicada para usuários com perfis de investidor (TRADER, USER_TRADER, FULL), oferecendo ferramentas especializadas para acompanhamento e análise de investimentos.

---

## 🚀 Funcionalidades Implementadas

### 1. **Dashboard com Métricas**

Cards de resumo mostrando:

- 💰 **Portfólio Total**: Valor total investido com percentual de variação mensal
- 📊 **Investimentos Ativos**: Quantidade de investimentos e categorias
- 📈 **Rendimento Mensal**: Média de rendimento dos últimos 6 meses
- 🎯 **Diversificação**: Score de distribuição do portfólio

### 2. **Aba: Meus Investimentos**

- Grid de cards com todos os investimentos
- Informações por investimento:
  - Nome e tipo (Renda Fixa, Renda Variável, FII, etc)
  - Valor investido
  - Valor atual
  - Rentabilidade (% e valor absoluto)
  - Ícone colorido por categoria
- Botão "Novo Investimento"
- Ações: Ver detalhes, menu de opções

### 3. **Aba: Análises**

- **Gráfico de Distribuição**: Visualização por categoria (em desenvolvimento)
- **Performance Histórica**: Evolução temporal (em desenvolvimento)
- **Tabela Comparativa**: Análise detalhada de ativos
  - Colunas: Ativo, Categoria, Performance, Risco, Liquidez
  - Chips coloridos por performance (positiva/negativa)
  - Indicadores de risco: Baixo, Médio, Alto, Muito Alto

### 4. **Aba: Rentabilidade**

- Gráfico de evolução temporal (em desenvolvimento)
- Cards de resumo:
  - Retorno último mês
  - Retorno último ano
  - Retorno total (desde início)
- Valores em % e reais

### 5. **Aba: Alertas**

- Lista de notificações importantes:
  - Valorizações detectadas
  - Alertas de mercado
  - Recebimento de dividendos
  - Cards coloridos por tipo (sucesso, aviso, info)
- **Configurações de Alertas**:
  - Toggle para alerta de valorização (>5%)
  - Toggle para alerta de desvalorização (>3%)
  - Toggle para relatório mensal

---

## 🎨 Design e UX

### Paleta de Cores

- **Gradiente Principal**: Verde (#11998e → #38ef7d) - transmite crescimento e prosperidade
- **Sucesso**: Verde - rentabilidades positivas
- **Erro**: Vermelho - perdas/desvalorizações
- **Info**: Azul - informações neutras
- **Warning**: Amarelo/Laranja - alertas

### Ícones por Categoria

- 🏦 `mdi-bank` - Renda Fixa
- 📈 `mdi-chart-line` - Ações / Renda Variável
- 🏢 `mdi-office-building` - Fundos Imobiliários
- 💵 `mdi-cash-multiple` - CDB e títulos
- ₿ `mdi-bitcoin` - Criptomoedas

### Interações

- Hover nos cards com elevação e transformação
- Cards de investimento responsivos
- Tabs com ícones descritivos
- Chips coloridos para status visual rápido

---

## 🔐 Controle de Acesso

### Rota

```typescript
{
  path: "/trader",
  name: "trader",
  component: () => import("../views/trader/TraderPanelView.vue"),
  meta: {
    auth: true,
    requiresTrader: true
  }
}
```

### Guard de Permissão

Verifica se usuário possui uma das roles:

- ✅ `TRADER` - Apenas investimentos
- ✅ `USER_TRADER` - Usuário comum + investimentos
- ✅ `FULL` - Acesso total ao sistema

```typescript
const hasTraderRole = rolesStore.hasAnyRole(["TRADER", "USER_TRADER", "FULL"]);
if (!hasTraderRole) {
  return next({ name: "dashboard" });
}
```

### Visibilidade no Menu

- Item "Trader" aparece apenas para usuários com roles apropriadas
- Ícone: `mdi-chart-line` (gráfico de linha)
- Posição: Entre Admin e Dashboard (para usuários que tem ambas)

---

## 📊 Dados Mock (Exemplo)

### Investimentos

```javascript
{
  id: 1,
  name: 'Tesouro Selic 2027',
  type: 'Renda Fixa',
  invested: 10000,
  current: 11250,
  profit: 12.5,  // 12.5%
}
```

### Alertas

```javascript
{
  id: 1,
  type: 'success',
  icon: 'mdi-trending-up',
  title: 'Valorização Detectada',
  message: 'PETR4 subiu 5.2% nas últimas 24h',
  time: 'Há 2 horas',
}
```

---

## 🛠️ Arquivos Criados/Modificados

### Novos Arquivos

1. **`frontend/src/views/trader/TraderPanelView.vue`**
   - View principal do painel trader
   - 4 abas: Investimentos, Análises, Rentabilidade, Alertas
   - 500+ linhas de código
   - Totalmente responsivo

### Arquivos Modificados

1. **`frontend/src/router/index.ts`**

   - Adicionada rota `/trader`
   - Meta: `requiresTrader: true`

2. **`frontend/src/router/routes.ts`**

   - Adicionada verificação `requiresTrader`
   - Guard checa `hasAnyRole(['TRADER', 'USER_TRADER', 'FULL'])`

3. **`frontend/src/store/roles.ts`**

   - Adicionado método `hasAnyRole(roleNames: string[])`
   - Exportado no return da store

4. **`frontend/src/views/mobile/DashboardMobileView copy.vue`**
   - Atualizada rota do menu "Trader" de `dashAdmim` → `trader`
   - Atualizado ícone para `mdi-chart-line`
   - Corrigidos índices no watch do router

---

## 🧪 Como Testar

### 1. Acessar como Usuário TRADER

```bash
Email: maria@teste.com
Senha: senha123
Role: TRADER
```

**Resultado esperado**:

- ✅ Menu "Trader" aparece
- ✅ Clique leva para `/trader`
- ✅ Painel carrega com 4 abas
- ✅ 6 investimentos mock exibidos
- ✅ Cards responsivos e interativos

### 2. Acessar como USER_TRADER

```bash
Email: pedro@teste.com
Senha: senha123
Role: USER_TRADER
```

**Resultado esperado**:

- ✅ Menu "Trader" aparece
- ✅ Mesmo acesso que TRADER

### 3. Acessar como USER (sem permissão)

```bash
Email: joao@teste.com
Senha: senha123
Role: USER
```

**Resultado esperado**:

- ❌ Menu "Trader" NÃO aparece
- ❌ Acesso direto `/trader` redireciona para `/dashboard`

---

## 🔄 Próximas Iterações

### Backend (Futuro)

- [ ] API endpoint `/api/investments` - CRUD de investimentos
- [ ] Model `Investment` com relacionamento a User
- [ ] Cálculo automático de rentabilidade
- [ ] Integração com APIs de cotações reais
- [ ] Sistema de alertas automático
- [ ] Histórico de transações

### Frontend (Melhorias)

- [ ] Gráficos reais com Chart.js ou ApexCharts
- [ ] Formulário "Novo Investimento"
- [ ] Modal de detalhes do investimento
- [ ] Filtros e ordenação
- [ ] Exportação de relatórios (PDF/Excel)
- [ ] Dashboard customizável
- [ ] Modo escuro

### Features Avançadas

- [ ] Calculadora de rentabilidade
- [ ] Simulador de investimentos
- [ ] Comparador de ativos
- [ ] Rebalanceamento automático de carteira
- [ ] Integração com Open Banking
- [ ] Notificações push em tempo real

---

## 📈 Métricas de Qualidade

### Performance

- ✅ Lazy loading da view (code splitting)
- ✅ Componentes leves e otimizados
- ✅ CSS scoped (sem poluição global)

### Acessibilidade

- ✅ Ícones semânticos
- ✅ Cores com bom contraste
- ✅ Estrutura hierárquica clara

### Segurança

- ✅ Route guard protege acesso
- ✅ Verificação de permissões no backend (pronto para implementar)
- ✅ Tokens JWT para autenticação

### Responsividade

- ✅ Grid adaptativo (cols="12" md="6" lg="4")
- ✅ Tabelas responsivas
- ✅ Cards empilhados em mobile

---

## 🎓 Conceitos Utilizados

- **Vue 3 Composition API** - Script setup, refs, computed
- **Vuetify 3** - Componentes Material Design
- **Vue Router** - Navegação e guards
- **Pinia** - Gerenciamento de estado
- **TypeScript** - Tipagem forte e IntelliSense
- **CSS Scoped** - Estilos isolados
- **Lazy Loading** - Otimização de bundle
- **Role-Based Access Control (RBAC)** - Controle granular

---

## 📝 Notas Importantes

1. **Dados Mock**: Todos os dados são estáticos (mock) para desenvolvimento. Backend necessário para produção.

2. **Gráficos**: Placeholders preparados para integração com bibliotecas de charts.

3. **Permissões**: Sistema robusto permite expansão fácil para novas roles.

4. **Modularidade**: View pode ser facilmente estendida com novos componentes.

5. **Compatibilidade**: Funciona com o sistema existente sem conflitos.

---

## 🐛 Troubleshooting

### Menu "Trader" não aparece

- ✅ Verificar se usuário tem role TRADER/USER_TRADER/FULL
- ✅ Limpar localStorage e fazer login novamente
- ✅ Verificar console do navegador por erros

### Rota redireciona para dashboard

- ✅ Confirmar guard `requiresTrader` está implementado
- ✅ Verificar método `hasAnyRole` está na store
- ✅ Conferir se `fetchMyPermissions()` foi chamado

### Cards não carregam

- ✅ Verificar se mockInvestments está populado
- ✅ Conferir v-for está correto
- ✅ Inspecionar erros no console

---

**Versão**: 1.0.0  
**Data**: 15 de outubro de 2025  
**Status**: ✅ **COMPLETO E TESTÁVEL**
