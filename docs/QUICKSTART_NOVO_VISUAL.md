# 🚀 QUICKSTART - Novo Visual MrFinancas

## ⚡ Teste Rápido (5 minutos)

### 1. **Iniciar**

```bash
cd frontend
npm run dev
```

### 2. **Abrir no Navegador**

```
http://localhost:5173
```

### 3. **Fazer Login**

- Use suas credenciais
- Aguarde redirect para Dashboard

---

## 📝 Testes Recomendados

### ✅ Layout Principal

- [ ] Verifique se o header está fixo no topo
- [ ] Verifique se o sidebar está à esquerda
- [ ] Verifique se o conteúdo ocupa espaço correto

### ✅ Header

- [ ] Clique em ☀️/🌙 para alternar tema
- [ ] Clique em 🔔 para ver notificações
- [ ] Clique em seu avatar para ver menu
- [ ] Clique em "Sair" para logout

### ✅ Sidebar

- [ ] Clique em Dashboard, Receitas, Despesas
- [ ] Verifique se item ativo fica destacado
- [ ] Em mobile (<1024px), clique no ☰ menu

### ✅ Month Selector

- [ ] Clique em < para mês anterior
- [ ] Clique em > para próximo mês
- [ ] Clique em "Hoje" para voltar ao mês atual
- [ ] Verifique formato (Outubro ou Out.2024)

### ✅ Dashboard

- [ ] Veja os 4 cards KPI
- [ ] Veja os gráficos (se implementados)
- [ ] Veja as últimas transações
- [ ] Clique em "Nova Receita" para adicionar

### ✅ Receitas

- [ ] Veja os 4 cards de resumo
- [ ] Veja a tabela de receitas
- [ ] Clique em 🔍 e busque "Salário"
- [ ] Filtre por Status "Recebida"
- [ ] Clique em ✏️ para editar
- [ ] Clique em 🗑️ para deletar
- [ ] Clique em "+ Nova Receita" para adicionar

### ✅ Despesas

- [ ] Veja os 4 cards de resumo
- [ ] Veja a tabela de despesas
- [ ] Clique em 🔍 e busque "Aluguel"
- [ ] Filtre por Status "Paga"
- [ ] Clique em ✏️ para editar
- [ ] Clique em 🗑️ para deletar
- [ ] Clique em "+ Nova Despesa" para adicionar

### ✅ Tema Escuro

- [ ] Clique em 🌙 no header
- [ ] Verifique se tudo fica escuro
- [ ] Verifique se textos ficam legíveis
- [ ] Clique em ☀️ para voltar ao claro

### ✅ Responsividade

- [ ] Abra DevTools (F12)
- [ ] Redimensione para 375px (mobile)
- [ ] Verifique se cards ficam em 1 coluna
- [ ] Clique em ☰ para abrir sidebar
- [ ] Verifique se botões ficam full-width

---

## 🎯 O que Testou?

### ✅ Componentes

- [x] MainLayout funciona
- [x] Header renderiza
- [x] Sidebar funciona
- [x] Month selector funciona
- [x] Profile section funciona
- [x] Notificações abrem
- [x] Tema escuro funciona

### ✅ Views

- [x] Dashboard renderiza
- [x] Receitas renderiza com tabela
- [x] Despesas renderiza com tabela
- [x] Filtros funcionam
- [x] Dialog adiciona/edita/deleta

### ✅ Responsividade

- [x] Desktop (>1024px) - layout completo
- [x] Tablet (600-1024px) - sidebar colapsível
- [x] Mobile (<600px) - drawer sidebar

---

## 🐛 Se Algo Não Funcionar

### Problema: Sidebar não aparece

**Solução:**

1. Verifique o console (F12)
2. Veja se há erro de compilação
3. Recarregue a página (F5)

### Problema: Admin/Trader não aparecem no menu

**Solução:**

1. Abra console (F12)
2. Procure por `[MainLayout]`
3. Verifique `userStore.userData.type`
4. Verifique se é "FULL", "ADMIN" ou "TRADER"

### Problema: Tema escuro não funciona

**Solução:**

1. Verificar `themeStore.theme` no console
2. Verificar se CSS tem variáveis
3. Recarregar página

### Problema: Notificações não abrem

**Solução:**

1. Clique no 🔔 sino
2. Verifique se há menu dropdown
3. Se não abrir, cheque console para erros

---

## 📊 Dados de Teste

### Receitas (Mock)

```
1. Salário - R$ 5.000,00 - Recebida
2. Freelancer - R$ 1.200,00 - Recebida
3. Bonus - R$ 800,00 - Pendente
4. Investimento - R$ 500,00 - Pendente
```

### Despesas (Mock)

```
1. Aluguel - R$ 1.500,00 - Paga
2. Supermercado - R$ 450,00 - Paga
3. Internet - R$ 120,00 - Pendente
4. Uber - R$ 85,00 - Pendente
```

---

## 💡 Dicas

### 🎨 Ver Variações de Cores

1. Mude para tema escuro
2. Veja como cards e chips mudam
3. Mude de volta para claro

### 📱 Testar Mobile

1. Abra DevTools (F12)
2. Clique em 📱 (Toggle device toolbar)
3. Redimensione para 375px
4. Teste todas as funcionalidades

### 🔍 Debugar

1. Abra DevTools (F12)
2. Vá para Console
3. Procure por mensagens `[MainLayout]`
4. Verifique `userStore.userData`

### ⚡ Performance

1. Vá para Network tab
2. Carregue a página
3. Verifique se tudo carrega rápido
4. Verifique se há requisições desnecessárias

---

## 🎓 Estrutura de Arquivos

```
frontend/src/
├── layouts/
│   └── MainLayout.vue ⭐ (NOVO)
├── views/
│   ├── DashboardView.vue (modernizado)
│   ├── receitas/
│   │   ├── ReceitasView.vue ⭐ (NOVO)
│   │   └── ReceitasView_OLD.vue (backup)
│   └── despesas/
│       ├── DespesasView.vue ⭐ (NOVO)
│       └── DespesasView_OLD.vue (backup)
└── router/
    └── index.ts (já configurado com layout)
```

---

## ✅ Checklist Final

**Antes de entregar:**

- [ ] Todas as views carregam sem erro
- [ ] Sidebar funciona em desktop e mobile
- [ ] Admin/Trader aparecem corretamente
- [ ] Filtros funcionam
- [ ] Adicionar/editar/deletar funcionam
- [ ] Tema escuro funciona
- [ ] Responsividade OK
- [ ] Sem console errors

**Se tudo passou:**
✅ **Parabéns! O novo visual está funcionando!**

---

## 📞 Próximas Etapas

1. **Conectar com API Real**

   - Substituir dados mock por dados da API
   - Implementar chamadas HTTP

2. **Adicionar Mais Views**

   - Contas (ContasView)
   - Categorias (CategoriasView)
   - Perfil (PerfilView)
   - Admin (AdminPanel)

3. **Melhorar Visualizações**

   - Adicionar gráficos
   - Adicionar estatísticas
   - Adicionar relatórios

4. **Funcionalidades Avançadas**
   - Exportar para Excel/PDF
   - Agendamento de transações
   - Notificações push
   - Integração com bancos

---

**Versão**: 2.0
**Status**: ✅ Pronto para Testes
**Última Atualização**: Outubro 17, 2025
