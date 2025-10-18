# 🎓 GuiaUso - ReceitasView com Campos Avançados

**Objetivo**: Demonstrar como usar cada funcionalidade do novo ReceitasView  
**Versão**: 2.0 com FormLancamentos

---

## 🎯 Cenários de Uso

### Cenário 1: Registrar Salário Fixo

**Passos**:

1. Clique "Nova Receita"
2. Digite "Salário" em Descrição
3. Digite "5000" em Valor (auto-formata para `5.000,00`)
4. Selecione "Salário" em Categoria
5. Subcategoria popula automaticamente com "Salário"
6. Selecione "Conta Principal"
7. Clique no campo "Recorrência"
8. Selecione "Fixa" (para mensal automático)
9. Deixe Data de Vencimento como "Hoje"
10. Clique "Adicionar"

**Resultado**: ✅ Receita Fixa criada com recorrência mensal

---

### Cenário 2: Lançar Freelance Parcelado

**Passos**:

1. Clique "Nova Receita"
2. Digite "Projeto Web - Empresa XYZ"
3. Digite "3000" em Valor
4. Selecione "Freelancer" em Categoria
5. Subcategoria → "Projeto"
6. Selecione "Conta Principal"
7. **Clique em Recorrência**
8. **Selecione "Parcelado"**
9. **Clique no ícone ✏️ de Parcelas**
10. **Configure**:
    - Parcela Inicial: 1
    - Quantidade: 3
    - Periodicidade: Mensal
11. **Clique "Valor parcela"** (para entrar R$ 1.000/mês)
12. **Clique "Concluído"**
13. Clique "Adicionar"

**Resultado**: ✅ 3 Receitas de R$ 1.000 criadas automaticamente (Mês 1, 2, 3)

---

### Cenário 3: Registrar Bônus com Detalhes

**Passos**:

1. Clique "Nova Receita"
2. Digite "Bônus Desempenho Q4"
3. Digite "2500"
4. Selecione "Bonus" → Subcategoria "Bônus anual"
5. Conta: "Investimento"
6. Deixe Status como "PENDENTE"
7. **Clique "Mais Informações"**
8. Configure Data de Lançamento: "ontem"
9. Configure Data de Efetivação: "hoje"
10. **Escreva em Observações**: "Bônus de desempenho referente ao Q4. Depositado conforme combinado com RH."
11. Clique "Adicionar"

**Resultado**: ✅ Receita com histórico de datas e observações

---

## 📝 Exemplo de Form Completo

### Dados Preenchidos

```javascript
{
  descricao: "Consultoria - Empresa ABC",
  valor: "8.500,00",  // Auto-formatado
  categoria: "Freelancer",
  subcategoria: "Consultoria",
  conta_id: 2,  // Conta Investimento
  data_vencimento: "2025-10-31",
  status: "pendente",
  status_lancamento: "PENDENTE",  // Toggle visual
  recorrencia: "Não recorrente",
  data_lancamento: "2025-10-17",
  data_efetivacao: null,
  observacoes: "Consultoria de 2 meses para implementação de sistema ERP"
}
```

### Exibição Visual

```
┌────────────────────────────────────────────────────┐
│ Descrição                                          │
│ [Consultoria - Empresa ABC                      ] │
│                                                    │
│ Valor                                              │
│ [8.500,00                                        ] │
│                                                    │
│ 🔄 Não recorrente                     [detalhe]  │
│ ───────────────────────────────────────────────── │
│                                                    │
│ Categoria          │ Subcategoria                 │
│ [Freelancer    ▼] │ [Consultoria             ▼] │
│                                                    │
│ Conta Principal    │ Status                       │
│ [Investimento  ▼] │ [🕐─────] PENDENTE           │
│                                                    │
│ 📅 Data de Vencimento: Qui., 31/10/2025          │
│                                                    │
│ [▼ Mais Informações]                             │
│                                                    │
│ [Cancelar] [Adicionar]                           │
└────────────────────────────────────────────────────┘
```

---

## 🔄 Edição de Receita Existente

### Cenário: Ajustar Valor

**Passos**:

1. Localize a receita na tabela
2. Clique ✏️ (lápis)
3. Altere o Valor de `5.000,00` para `5.500,00`
4. Clique "Atualizar"
5. ✅ Receita atualizada

**Antes**: R$ 5.000,00  
**Depois**: R$ 5.500,00

---

## 🗑️ Exclusão com Confirmação

### Passos\*\*:

1. Clique 🗑️ (lixeira) na receita
2. Confirme "Tem certeza que deseja deletar esta receita?"
3. ✅ Receita removida

---

## 📊 Filtros e Busca

### Filtrar por Status

```
Status: [pendente ▼]
↓
Mostra apenas receitas com status = "pendente"
```

### Filtrar por Categoria

```
Categoria: [Freelancer ▼]
↓
Mostra apenas receitas de "Freelancer"
```

### Buscar por Descrição

```
Buscar: [Consultoria        ]
↓
Mostra receitas que contenham "Consultoria"
```

### Limpar Filtros

```
[Limpar Filtros]
↓
Mostra todas as receitas
```

---

## 💡 Dicas Avançadas

### 1. **Formatação de Valor Automática**

```
Digite: 1234567
Resultado: 1.234,56 (auto-formata)

Digite: 99
Resultado: 0,99

Digite: 0
Resultado: 0,00 (rejeita na validação)
```

### 2. **Data Inteligente**

```
Hoje: 18/10/2025 → "Hoje"
Amanhã: 19/10/2025 → "Amanhã"
Ontem: 17/10/2025 → "Ontem"
Próxima semana: 25/10/2025 → "Sáb., 25/10/2025"
```

### 3. **Recorrência Fixa**

```
Recorrência: Fixa
↓
Receita se repete todo mês automaticamente
Ideal para: Salários, aluguéis, assinaturas
```

### 4. **Parcelamento**

```
Recorrência: Parcelado
Quantidade: 12
Valor: Valor total R$ 1.200,00
↓
Cria 12 receitas de R$ 100,00 cada
```

### 5. **Status Toggle**

```
Antes: [🕐─────] PENDENTE
Clique: ✓
Depois: [─────✓] EFETIVADA
```

---

## ❌ Validações e Erros

### Campo: Descrição

```
Deixar em branco → ❌ "Campo obrigatório"
Digitar "ab"    → ❌ "Mínimo 3 caracteres"
Digitar "abc"   → ✅ Válido
```

### Campo: Valor

```
Deixar em branco → ❌ "Valor obrigatório"
Digitar "0"     → ❌ "Valor deve ser maior que zero"
Digitar "0,01"  → ✅ Válido
```

### Campo: Categoria

```
Deixar em branco → ❌ "Campo obrigatório"
Selecionar      → ✅ Válido
```

### Campo: Observações

```
Máximo: 1000 caracteres
Contador: "42/1000"
Limite atingido → ⚠️ Não permite mais digitação
```

---

## 🎨 Visual e Responsividade

### Desktop (> 1024px)

```
┌──────────────────────────────────────────────────────┐
│ Descrição  │ Valor  │ Categoria │ Status │ Ações   │
├──────────────────────────────────────────────────────┤
│ Salário    │ 5K     │ Salário   │ ✅     │ ✏️ 🗑️  │
│ Freelancer │ 1.2K   │ Freelancer│ ⏳     │ ✏️ 🗑️  │
└──────────────────────────────────────────────────────┘
```

### Tablet (600-1024px)

```
Linhas mais compactas
Coluna "Ações" permanece
Dialog se adapta a 90% da tela
```

### Mobile (< 600px)

```
Stack vertical
Dialog fullscreen
Buttons empilhados
Tabela com scroll horizontal
```

---

## 🔄 Workflow Completo: Do Registro ao Recebimento

```
1️⃣ REGISTRO (Data de Lançamento)
   └─ "Nova Receita" → Preenche formulário → Salva

2️⃣ PROCESSAMENTO (Data de Vencimento)
   └─ Receita fica "PENDENTE"
   └─ Dashboard mostra em "Pendentes"

3️⃣ RECEBIMENTO (Data de Efetivação)
   └─ Edita receita
   └─ Alterna Status para "EFETIVADA"
   └─ Dashboard move para "Recebidas"

4️⃣ FINALIZADO
   └─ Receita arquivada
   └─ Entra em relatórios
```

---

## 📞 Suporte e Referência

| Funcionalidade | Atalho           | Referência     |
| -------------- | ---------------- | -------------- |
| Novo           | [Nova Receita]   | Topo da página |
| Editar         | ✏️               | Coluna "Ações" |
| Deletar        | 🗑️               | Coluna "Ações" |
| Buscar         | 🔍               | Seção Filtros  |
| Filtrar        | ▼                | Seção Filtros  |
| Limpar         | [Limpar Filtros] | Seção Filtros  |

---

## 🎓 Conclusão

O novo **ReceitasView** oferece:

- ✅ Experiência intuitiva e profissional
- ✅ Campos completos e validados
- ✅ Funcionalidades avançadas acessíveis
- ✅ Responsividade total
- ✅ Compatibilidade com FormLancamentos

**Comece a usar agora!** 🚀
