# 🎯 Dashboard Real Data - Guia de Teste

## 📋 Resumo das Mudanças

O Dashboard foi corrigido para exibir **dados reais** do usuário após login, em vez de valores hardcoded/zeros.

### ✅ Problema Resolvido
- ❌ **Antes**: Dashboard mostra R$ 0,00 em todos os campos
- ✅ **Depois**: Dashboard mostra dados reais: R$ 7.500, R$ 18.000, R$ 0

---

## 🧪 Como Testar

### Pré-requisitos
- [ ] Backend (Laravel) rodando em `http://localhost:8000`
- [ ] Frontend (Vue) rodando em `http://localhost:5173`
- [ ] Banco de dados com dados do usuário de teste

### Usuário de Teste
```
Email: rafaelburghausen@gmail.com
Senha: Teste123@
Dados esperados:
  - Saldo: R$ 7.500,00
  - Receitas: R$ 18.000,00
  - Despesas: R$ 0,00
```

### Passos do Teste

#### 1️⃣ Limpar Dados Armazenados
```javascript
// Abrir Console (F12) e executar:
localStorage.clear()
sessionStorage.clear()
location.reload()
```

#### 2️⃣ Navegar para Login
```
http://localhost:5173/login
```

#### 3️⃣ Fazer Login
- Email: `rafaelburghausen@gmail.com`
- Senha: `Teste123@`
- Clicar em "Entrar"

#### 4️⃣ Verificar Redirecionamento
- Deve redirecionar automaticamente para `/dashboard`
- Aguardar carregamento dos dados

#### 5️⃣ Validar Dashboard

✅ **KPI Cards** (primeiro linha)
- [ ] "Total de Receitas" mostra **R$ 18.000,00**
- [ ] "Total de Despesas" mostra **R$ 0,00**
- [ ] "Saldo Total" mostra **R$ 7.500,00**
- [ ] Nenhum campo está em **R$ 0,00** (ou zeros)

✅ **Gráficos**
- [ ] Gráfico de barras mostra "Receitas vs Despesas"
- [ ] Barra de Receitas: **R$ 18.000,00**
- [ ] Barra de Despesas: **R$ 0,00**

✅ **Validação no Console**
```javascript
// Abrir Console (F12) e executar:
import { useUserStore } from '@/store/user'
const userStore = useUserStore()
console.log('Summary:', userStore.summary)
console.log('MesAno:', userStore.mesAno)

// Resultado esperado:
// Summary: { 
//   saldoAtual: 7500, 
//   saldoInicial: 0, 
//   totalReceitas: 18000, 
//   totalDespesas: 0 
// }
// MesAno: "2025-10"
```

---

## 📝 Casos de Teste

### Teste 1: Login e Dashboard
| Etapa | Ação | Resultado Esperado |
|-------|------|------------------|
| 1 | Fazer login | Redireciona para `/dashboard` |
| 2 | Dashboard carrega | KPI cards mostram valores reais |
| 3 | Verificar gráficos | Gráficos mostram dados reais |
| 4 | Verificar store | `userStore.summary` contém dados |

### Teste 2: Persistência de Sessão
| Etapa | Ação | Resultado Esperado |
|-------|------|------------------|
| 1 | Fazer login | Dashboard com dados reais |
| 2 | F5 (Recarregar) | Dashboard mantém dados |
| 3 | Verificar localStorage | `userData` contém `summary` |

### Teste 3: Logout e Re-login
| Etapa | Ação | Resultado Esperado |
|-------|------|------------------|
| 1 | Fazer logout | Redireciona para `/login` |
| 2 | Fazer login novamente | Dashboard mostra dados reais novamente |

### Teste 4: Novo Usuário (Cadastro)
| Etapa | Ação | Resultado Esperado |
|-------|------|------------------|
| 1 | Fazer cadastro | Redireciona para `/dashboard` |
| 2 | Dashboard carrega | Mostra dados do novo usuário |

---

## 🔍 Validação Técnica

### Verificar Requisição de Login
```bash
# No Network tab do DevTools:
POST http://localhost:8000/api/login
Status: 200 OK

# Response body:
{
  "token": "62|...",
  "user": { "id": 1, "name": "...", "email": "..." },
  "summary": {
    "saldoAtual": 7500,
    "saldoInicial": 0,
    "totalReceitas": 18000,
    "totalDespesas": 0
  },
  "mesAno": "2025-10"
}
```

### Verificar localStorage
```javascript
// Console:
JSON.parse(localStorage.getItem('userData'))

// Deve conter:
{
  id: 1,
  name: "Marcos Rafael Burghausen",
  email: "rafaelburghausen@gmail.com",
  type: null,
  summary: {
    saldoAtual: 7500,
    saldoInicial: 0,
    totalReceitas: 18000,
    totalDespesas: 0
  }
}
```

### Verificar Store (Pinia)
```javascript
// Console (após importar store):
import { useUserStore } from '@/store/user'
const store = useUserStore()

store.summary
// { saldoAtual: 7500, saldoInicial: 0, totalReceitas: 18000, totalDespesas: 0 }

store.userData
// { id: 1, name: "...", summary: { ... } }

store.mesAno
// "2025-10"
```

---

## ❌ Problemas Comuns e Soluções

### Problema: Dashboard mostra zeros
**Solução**: 
1. Verificar se `userStore.summary` está vazio
2. Fazer logout e login novamente
3. Limpar localStorage: `localStorage.clear()`

### Problema: Gráficos vazios
**Solução**:
1. Verificar se `chartSeries.value.bar` tem dados
2. Consultar console para erros
3. Recarregar página (F5)

### Problema: Redirecionamento não funciona
**Solução**:
1. Verificar se token foi salvo: `localStorage.getItem('sanctum_token')`
2. Verificar se há erro na resposta do login
3. Consultar Network tab para ver response

### Problema: localStorage não sincroniza
**Solução**:
1. Limpar e tentar novamente
2. Verificar se browser tem localStorage habilitado
3. Usar DevTools → Application → LocalStorage

---

## ✨ Checklist de Validação Final

### Funcionalidade
- [ ] Dashboard carrega após login
- [ ] KPI cards mostram valores reais
- [ ] Gráficos mostram dados reais
- [ ] Sem erros no console
- [ ] Redirecionamento automático funciona

### Performance
- [ ] Dashboard carrega em < 2 segundos
- [ ] Sem requisições extras desnecessárias
- [ ] Dados recuperados do localStorage funcionam

### Data Integrity
- [ ] Dados salvos em localStorage são corretos
- [ ] Summary no store é igual ao localStorage
- [ ] MesAno está correto
- [ ] Formatação de moeda é PT-BR

### UX
- [ ] Usuário vê dados reais imediatamente
- [ ] Sem flickering ou recarregamentos
- [ ] Alertas/erros são em português
- [ ] Layout responsive funciona

---

## 🎬 Demonstração em Video

1. Abrir `http://localhost:5173/login`
2. Inserir credenciais
3. Dashboard carrega com dados reais ✅
4. Mostrar valores: R$ 7.500, R$ 18.000, R$ 0
5. Recarregar página - dados persistem ✅
6. Fazer logout e re-login ✅

---

## 📞 Contato/Suporte

Se encontrar problemas:
1. Verificar logs do backend: `php artisan tinker`
2. Verificar console do frontend: DevTools (F12)
3. Verificar network requests: Network tab
4. Revisar localStorage: DevTools → Application

---

**Data**: 2025-01-17  
**Status**: ✅ Pronto para teste  
**Prioridade**: 🔴 ALTA - Bloqueador principal resolvido
