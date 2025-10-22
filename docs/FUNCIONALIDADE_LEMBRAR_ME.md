# Funcionalidade "Lembrar-me" - LoginView

## 📋 Resumo

Implementação completa da funcionalidade "Lembrar-me nesta máquina" no formulário de login, permitindo que o sistema salve o email do usuário para facilitar logins futuros.

## 🎯 Objetivo

Melhorar a experiência do usuário ao permitir que o email seja preenchido automaticamente em logins subsequentes quando a opção "Lembrar-me" estiver marcada.

## ✨ Funcionalidades Implementadas

### 1. **Carregar Email Salvo (onMounted)**

```typescript
onMounted(() => {
  const rememberMe = localStorage.getItem("rememberMe");
  const rememberedEmail = localStorage.getItem("rememberedEmail");

  if (rememberMe === "true" && rememberedEmail) {
    formData.value.email = rememberedEmail;
    formData.value.remember = true;
  }
});
```

**Comportamento:**

- Ao abrir a página de login, verifica localStorage
- Se `rememberMe === 'true'` e existe `rememberedEmail`
- Preenche o campo email automaticamente
- Marca o checkbox "Lembrar-me" como checked

### 2. **Observar Mudanças no Checkbox (watch)**

```typescript
watch(
  () => formData.value.remember,
  (newValue) => {
    // Se desmarcar, limpar dados salvos
    if (!newValue) {
      localStorage.removeItem("rememberMe");
      localStorage.removeItem("rememberedEmail");
    }
  }
);
```

**Comportamento:**

- Monitora mudanças no checkbox em tempo real
- Quando usuário **desmarca** a opção:
  - Remove `rememberMe` do localStorage
  - Remove `rememberedEmail` do localStorage
- Permite que usuário "esqueça" o email antes do login

### 3. **Salvar/Limpar após Login Bem-sucedido**

```typescript
// Gerenciar preferência de "lembrar-me"
if (formData.value.remember) {
  // Salvar email para próximo login
  localStorage.setItem("rememberMe", "true");
  localStorage.setItem("rememberedEmail", formData.value.email);
} else {
  // Limpar dados salvos se não marcou lembrar-me
  localStorage.removeItem("rememberMe");
  localStorage.removeItem("rememberedEmail");
}
```

**Comportamento:**

- Após login bem-sucedido
- Se checkbox **marcado**:
  - Salva `rememberMe = 'true'`
  - Salva `rememberedEmail = email do usuário`
- Se checkbox **desmarcado**:
  - Remove ambos os itens do localStorage
  - Garante que email não será lembrado

## 🔄 Fluxo Completo

```mermaid
graph TD
    A[Usuário acessa /login] --> B[onMounted executa]
    B --> C{localStorage tem rememberMe?}
    C -->|Sim| D[Preenche email automaticamente]
    C -->|Não| E[Campos vazios]
    D --> F[Marca checkbox]
    E --> G[Usuário preenche email]
    F --> G
    G --> H{Marca "Lembrar-me"?}
    H -->|Sim| I[Mantém marcado]
    H -->|Não| J[watch detecta desmarcação]
    J --> K[Remove dados do localStorage]
    I --> L[Usuário faz login]
    L --> M{Login bem-sucedido?}
    M -->|Sim| N{Checkbox marcado?}
    M -->|Não| O[Mostra erro]
    N -->|Sim| P[Salva email no localStorage]
    N -->|Não| Q[Remove email do localStorage]
    P --> R[Redireciona para dashboard]
    Q --> R
```

## 📦 Dados Armazenados no localStorage

### Estrutura

```javascript
// Quando "Lembrar-me" está marcado
localStorage = {
  rememberMe: "true",
  rememberedEmail: "usuario@exemplo.com",
};

// Quando "Lembrar-me" está desmarcado
localStorage = {
  // rememberMe: removido
  // rememberedEmail: removido
};
```

### Chaves Utilizadas

| Chave             | Tipo   | Valor               | Propósito                      |
| ----------------- | ------ | ------------------- | ------------------------------ |
| `rememberMe`      | string | `"true"` ou ausente | Flag indicando se deve lembrar |
| `rememberedEmail` | string | email do usuário    | Email a ser preenchido         |

## 🎨 Interface do Usuário

### Checkbox "Lembrar-me"

```vue
<v-checkbox
  v-model="formData.remember"
  label="Lembrar-me nesta máquina"
  density="compact"
/>
```

**Estados:**

- ☐ **Desmarcado**: Email não será salvo
- ☑ **Marcado**: Email será salvo após login bem-sucedido
- ☑ **Marcado (auto)**: Email foi carregado do localStorage

## 🧪 Cenários de Teste

### 1. Primeiro Login com "Lembrar-me"

```
1. Acessa /login pela primeira vez
   ✓ Campos vazios
   ✓ Checkbox desmarcado

2. Preenche email: usuario@teste.com
3. Preenche senha
4. Marca checkbox "Lembrar-me"
5. Clica "Entrar"

Resultado:
✓ Login bem-sucedido
✓ localStorage.rememberMe = "true"
✓ localStorage.rememberedEmail = "usuario@teste.com"
```

### 2. Segundo Login (Email Lembrado)

```
1. Acessa /login novamente
   ✓ Campo email preenchido: "usuario@teste.com"
   ✓ Checkbox marcado automaticamente

2. Preenche apenas a senha
3. Clica "Entrar"

Resultado:
✓ Login bem-sucedido
✓ Dados mantidos no localStorage
```

### 3. Desmarcar "Lembrar-me" Antes do Login

```
1. Acessa /login (email lembrado)
   ✓ Email preenchido
   ✓ Checkbox marcado

2. Desmarca checkbox
   ✓ watch() detecta mudança
   ✓ localStorage limpo IMEDIATAMENTE

3. Preenche senha e faz login

Resultado:
✓ Login bem-sucedido
✓ Email NÃO salvo
✓ Próximo login: campos vazios
```

### 4. Login sem Marcar "Lembrar-me"

```
1. Acessa /login
2. Preenche email e senha
3. NÃO marca "Lembrar-me"
4. Clica "Entrar"

Resultado:
✓ Login bem-sucedido
✓ localStorage.rememberMe: removido
✓ localStorage.rememberedEmail: removido
✓ Próximo login: campos vazios
```

### 5. Desmarcar Após Login Anterior

```
Login 1:
1. Marca "Lembrar-me"
2. Faz login → Email salvo

Login 2:
1. Acessa /login → Email preenchido
2. Desmarca "Lembrar-me"
3. Faz login

Resultado:
✓ Login bem-sucedido
✓ localStorage limpo após login
✓ Próximo login: campos vazios
```

## 🔒 Segurança

### O que É Salvo

- ✅ Email do usuário
- ✅ Flag booleana (rememberMe)

### O que NÃO É Salvo

- ❌ Senha (NUNCA salvar senhas!)
- ❌ Token de autenticação
- ❌ Dados sensíveis

### Boas Práticas Implementadas

- ✅ Apenas email é armazenado
- ✅ localStorage (não sessionStorage) para persistência
- ✅ Limpeza explícita ao desmarcar
- ✅ Validação antes de usar dados salvos
- ✅ Não expõe dados sensíveis

## 💡 Decisões de Design

### Por que localStorage?

```typescript
// localStorage: Persiste entre sessões do navegador
localStorage.setItem("rememberMe", "true");

// vs sessionStorage: Limpo ao fechar aba
// sessionStorage.setItem('rememberMe', 'true') // ❌ Não usado
```

**Razão:** Usuário espera que "Lembrar-me" funcione mesmo após fechar o navegador.

### Por que Dois Itens?

```typescript
localStorage.setItem("rememberMe", "true"); // Flag
localStorage.setItem("rememberedEmail", email); // Dado
```

**Razão:**

- `rememberMe`: Flag explícita de preferência do usuário
- `rememberedEmail`: Dado específico a ser lembrado
- Separação de conceitos (flag vs. dado)

### Por que watch()?

```typescript
watch(
  () => formData.value.remember,
  (newValue) => {
    if (!newValue) {
      localStorage.removeItem("rememberMe");
      localStorage.removeItem("rememberedEmail");
    }
  }
);
```

**Razão:**

- Feedback imediato ao desmarcar
- Usuário não precisa fazer login para esquecer
- Comportamento intuitivo

## 📊 Comparação Antes/Depois

| Aspecto                   | Antes ❌          | Depois ✅                  |
| ------------------------- | ----------------- | -------------------------- |
| **Email salvo**           | Só no handleLogin | onMounted + handleLogin    |
| **Checkbox marcado**      | Manual sempre     | Automático se lembrado     |
| **Desmarcar antes login** | Não fazia nada    | Remove dados imediatamente |
| **Desmarcar após login**  | Não limpava       | Limpa após login           |
| **Experiência**           | Incompleta        | Completa e intuitiva       |

## 🎯 Benefícios

### Para o Usuário

- ✅ Email preenchido automaticamente
- ✅ Menos digitação em logins frequentes
- ✅ Controle total (pode desmarcar a qualquer momento)
- ✅ Feedback imediato ao desmarcar
- ✅ Comportamento previsível

### Para o Sistema

- ✅ Reduz fricção no login
- ✅ Aumenta taxa de retorno de usuários
- ✅ Implementação simples e eficiente
- ✅ Sem dependências externas
- ✅ Performance nativa (localStorage é síncrono)

## 🔍 Detalhes de Implementação

### Imports Necessários

```typescript
import { onMounted, ref, watch } from "vue";
```

- `onMounted`: Hook de ciclo de vida para carregar dados
- `watch`: Observar mudanças reativas no checkbox

### Ordem de Execução

```
1. Component mounted
   ↓
2. onMounted() executa
   ↓ (se houver dados salvos)
3. Email e checkbox preenchidos
   ↓
4. watch() ativo (monitora checkbox)
   ↓ (usuário interage)
5. watch() detecta mudanças
   ↓ (se desmarcado)
6. localStorage limpo IMEDIATAMENTE
   ↓
7. handleLogin() executa
   ↓
8. Salva ou remove dados conforme checkbox
```

## 🚀 Melhorias Futuras Possíveis

### Opcionais (não implementadas)

- [ ] Salvar múltiplos emails (histórico)
- [ ] Autocomplete com emails anteriores
- [ ] Expiração automática (ex: 30 dias)
- [ ] Criptografar email no localStorage
- [ ] Mostrar tooltip explicativo no checkbox
- [ ] Animação ao preencher email automaticamente
- [ ] Botão "Não é você?" para trocar email

### Não Recomendadas

- ❌ Salvar senha (NUNCA!)
- ❌ Auto-login (requer senha sempre)
- ❌ Lembrar token (questão de segurança)

## 📝 Manutenção

### Como Desabilitar a Funcionalidade

```typescript
// No onMounted, comentar:
// const rememberMe = localStorage.getItem('rememberMe')
// const rememberedEmail = localStorage.getItem('rememberedEmail')
// ...

// No watch, comentar:
// watch(() => formData.value.remember, (newValue) => { ... })

// No handleLogin, remover bloco:
// if (formData.value.remember) { ... }
```

### Como Limpar Dados Manualmente (Console)

```javascript
localStorage.removeItem("rememberMe");
localStorage.removeItem("rememberedEmail");
```

### Como Verificar Dados Salvos (Console)

```javascript
console.log("RememberMe:", localStorage.getItem("rememberMe"));
console.log("Email:", localStorage.getItem("rememberedEmail"));
```

## ✅ Checklist de Implementação

- [x] Import de `onMounted` e `watch`
- [x] Lógica de carregar email no `onMounted`
- [x] Verificação de `rememberMe === 'true'`
- [x] Preenchimento automático de email
- [x] Marcação automática do checkbox
- [x] `watch()` no checkbox
- [x] Limpeza ao desmarcar (watch)
- [x] Salvar dados após login (if remember)
- [x] Limpar dados após login (else)
- [x] Testes de cenários principais

## 🔗 Arquivos Relacionados

```
frontend/src/views/acesso/LoginView.vue
├── onMounted() → Carrega email salvo
├── watch() → Monitora checkbox
└── handleLogin() → Salva/remove dados
```

## 📞 Debugging

### Problema: Email não preenche automaticamente

```typescript
// Verificar no console:
console.log("RememberMe:", localStorage.getItem("rememberMe"));
console.log("Email:", localStorage.getItem("rememberedEmail"));

// Se null/undefined → dados não foram salvos
// Se valores corretos → verificar onMounted
```

### Problema: Checkbox não marca automaticamente

```typescript
// Verificar no onMounted:
if (rememberMe === "true" && rememberedEmail) {
  formData.value.remember = true; // ← Esta linha
}
```

### Problema: Dados não são removidos ao desmarcar

```typescript
// Verificar watch:
watch(
  () => formData.value.remember,
  (newValue) => {
    if (!newValue) {
      // ← Verificar esta condição
      localStorage.removeItem("rememberMe");
      localStorage.removeItem("rememberedEmail");
    }
  }
);
```

---

**Versão**: 1.0.0  
**Data**: Janeiro 2025  
**Status**: ✅ Implementado e Funcional  
**Comportamento**: Completo e Intuitivo
