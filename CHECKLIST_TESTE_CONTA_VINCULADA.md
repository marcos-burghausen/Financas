# ✅ Checklist de Teste - Conta Vinculada

## 📝 Antes de Testar

- [ ] Frontend rodando em `http://localhost:4081`
- [ ] Backend acessível
- [ ] Aplicação carregada sem erros
- [ ] DevTools aberto (F12)

## 🔍 TESTE 1: Verificar Logs de Carregamento

### Passo 1: Navegar para Cartões

- [ ] Clique em "Meus Cartões de Crédito" (menu lateral)

### Passo 2: Abrir Console

- [ ] Pressionar F12
- [ ] Ir para aba "Console"
- [ ] Limpar logs anteriores (se houver)

### Passo 3: Verificar Logs

Procure por:

```
=== DEBUG loadContas ===
```

**Resultado esperado**:

- [ ] Log mostra "Todas as contas recebidas: Array(X)" - X > 0
- [ ] Log mostra cada conta com tipo_conta
- [ ] Log mostra "=== FIM DEBUG ===" no final

❌ **Se não ver isso**:

- [ ] Verifique se há contas cadastradas
- [ ] Verifique se há erro em vermelho no console
- [ ] Compartilhe a mensagem de erro

## 🎯 TESTE 2: Abrir Formulário

### Passo 1: Clicar em "Novo Cartão"

- [ ] Botão "Novo Cartão" no canto superior direito
- [ ] Dialog deve abrir com ~600px de largura

### Passo 2: Verificar Layout

- [ ] Dialog está centrado na tela
- [ ] Dialog NÃO ocupa tela inteira
- [ ] Botões Cancelar/Salvar no rodapé
- [ ] Título "Novo Cartão" no topo

❌ **Se dialog ocupa tela inteira**:

- [ ] Verifique se refresh da página atualizou
- [ ] Verifique se mudanças foram salvas corretamente

## 🔽 TESTE 3: Testar Dropdown de Conta Vinculada

### Passo 1: Clicar no Campo "Conta Vinculada"

- [ ] Campo está visível
- [ ] Campo está ativo (não desabilitado)
- [ ] Clicar abre dropdown

### Passo 2: Verificar Lista

**Esperado**: Deve exibir contas (sem cartões de crédito)

Exemplo:

```
□ Conta Corrente (com ícone do banco)
□ Conta Poupança (com ícone do banco)
```

- [ ] Dropdown exibe contas
- [ ] Cada conta tem ícone
- [ ] Cada conta tem nome
- [ ] Cartões de crédito NÃO aparecem

❌ **Se dropdown está vazio**:

- [ ] Verifique logs no console
- [ ] Procure por "Contas filtradas (final): Array(0)"
- [ ] Possível causa: Todas as contas são "Cartão de Crédito"
- [ ] Solução: Crie uma conta normal (Corrente/Poupança)

## 🎨 TESTE 4: Selecionar Conta

### Passo 1: Selecionar uma Conta

- [ ] Clique em uma conta da lista
- [ ] Conta é selecionada (com checkmark)
- [ ] Campo "Conta Vinculada" mostra o nome

### Passo 2: Verificar Herança de Cor

- [ ] Ícone da conta aparece no campo "Apelido do Cartão"
- [ ] Ícone está à direita do campo
- [ ] Ícone é do banco correto

### Passo 3: Verificar Campo "Apelido do Cartão"

- [ ] Campo é obrigatório
- [ ] Preencheu "Apelido do Cartão"?
  - [ ] Sim: Continue
  - [ ] Não: Preencha agora

## 📋 TESTE 5: Criar Cartão Completo

### Passo 1: Preencher Formulário

- [ ] Apelido do Cartão: "Meu Cartão 1"
- [ ] Conta Vinculada: Selecionar uma conta
- [ ] Limite: 5000
- [ ] Bandeira: Visa
- [ ] Dia Fechamento: 10
- [ ] Dia Vencimento: 20

### Passo 2: Salvar

- [ ] Botão "Salvar" está ativo
- [ ] Clicar em "Salvar"
- [ ] Aguardar processamento (loading...)

### Passo 3: Verificar Resultado

- [ ] Dialog fecha automaticamente
- [ ] Cartão aparece na lista
- [ ] Conta vinculada está correta

✅ **Se tudo funcionou**:

- [ ] Problema resolvido! 🎉
- [ ] Pode remover os logs de debug

## ⚠️ PROBLEMAS COMUNS

### Problema: "Conta Vinculada" está vazio

```
❌ Dropdown não exibe nenhuma conta
```

**Verificação**:

1. [ ] Abra DevTools (F12)
2. [ ] Procure por "Contas filtradas"
3. [ ] Verifique: Array(0) significa sem contas?
4. [ ] Ou: Há erro em vermelho?

**Soluções**:

- [ ] Criar uma conta normal (Corrente/Poupança)
- [ ] Verificar se contas têm `tipo_conta` definido
- [ ] Verificar tipo_conta NÃO contém "Cartão"

### Problema: Dialog ocupa página inteira

```
❌ Dialog não tem tamanho correto
```

**Verificação**:

1. [ ] Recarregar página (Ctrl+Shift+R hard refresh)
2. [ ] Verificar se as mudanças foram salvas

### Problema: Erro no console (texto vermelho)

```
❌ Mensagem de erro mostrada
```

**Ação**:

1. [ ] Copie a mensagem de erro
2. [ ] Envie para revisar
3. [ ] Inclua também: Output do "DEBUG loadContas"

## 🎯 RESUMO RÁPIDO

Se você vir isso no console → ✅ Tudo OK!

```
=== DEBUG loadContas ===
Todas as contas recebidas: Array(2)
Contagem de contas: 2
Conta: Conta Corrente | Tipo: "Corrente" | Include: true
Conta: Conta Poupança | Tipo: "Poupança" | Include: true
Contas filtradas (final): Array(2)
=== FIM DEBUG ===
```

E o dropdown exibe contas → ✅ Problema Resolvido! 🎉

---

**Checklist Completo**: [ ] Todos os testes passaram ✅
