# 👤 PerfilView - Documentação Completa

**Versão**: 2.0 Redesign  
**Data**: Outubro 17, 2025  
**Status**: ✅ IMPLEMENTADO  
**Tipo**: View Autenticada (com MainLayout)

---

## 📌 Visão Geral

A **PerfilView** permite que usuários autenticados gerenciem suas informações pessoais, segurança, preferências e visualizem suas sessões ativas.

---

## 🎨 Interface Visual

### Tabs

```
┌─────────────────────────────────────────┐
│ [👤 Dados Pessoais] [🔒 Segurança] [⚙️ Preferências] │
├─────────────────────────────────────────┤
│  [Avatar]  │  Formulário Dados          │
│ João Silva │  [Nome]                    │
│ Usuário    │  [Email]                   │
│ [📤]       │  [Telefone]                │
│            │  [CPF]                     │
│ Membro:    │  [Data Nascimento]         │
│ 15/01/2025 │  [Profissão]               │
│            │  [Biografia]               │
│ Último:    │  [SALVAR] [CANCELAR]      │
│ 5m atrás   │                            │
└─────────────────────────────────────────┘
```

---

## 🚀 Funcionalidades Implementadas

### 1. **Dados Pessoais (Tab 1)**

- ✅ Avatar com opção de trocar foto
- ✅ Edição de Nome
- ✅ Edição de Email
- ✅ Telefone
- ✅ CPF
- ✅ Data de Nascimento
- ✅ Profissão
- ✅ Biografia (textarea)
- ✅ Botões Salvar/Cancelar
- ✅ Estatísticas (Membro desde, Último acesso)

### 2. **Segurança (Tab 2)**

- ✅ Alteração de Senha:
  - Senha Atual
  - Nova Senha
  - Confirmar Nova Senha
- ✅ Toggle de visibilidade para cada campo
- ✅ Validação de correspondência
- ✅ Sessões Ativas:
  - Lista de dispositivos conectados
  - Localização
  - Último acesso
  - Status (Ativa/Inativa)
  - Chip colorido por status

### 3. **Preferências (Tab 3)**

- ✅ Seleção de Idioma
- ✅ Seleção de Moeda Padrão
- ✅ Checkboxes de Notificações:
  - Email notificações
  - Relatórios mensais
  - Alertas de transações
- ✅ Seleção de Tema (Claro/Escuro/Automático)
- ✅ Botões Salvar/Cancelar

### 4. **Zona de Risco**

- ✅ Card em red border
- ✅ Botão "Baixar Meus Dados"
- ✅ Botão "Deletar Conta"
- ✅ Aviso de ações irreversíveis

### 5. **Responsividade**

- ✅ Desktop: Layout completo
- ✅ Tablet: Otimizado
- ✅ Mobile: Stack vertical

---

## 💻 Estrutura de Código

### State

```typescript
const userData = ref({
  nome: "João Silva",
  email: "joao@example.com",
  type: "FULL",
  dataCriacao: "2025-01-15",
  ultimoAcesso: "2025-10-17T14:30:00",
});

const formData = ref({
  nome: "",
  email: "",
  telefone: "",
  cpf: "",
  dataNascimento: "",
  profissao: "",
  biografia: "",
});

const formSeguranca = ref({
  senhaAtual: "",
  senhaNova: "",
  confirmarSenha: "",
});

const formPreferencias = ref({
  idioma: "pt-BR",
  moeda: "BRL",
  emailNotificacoes: true,
  relatóriosMensais: true,
  alertasTransacoes: true,
  tema: "light",
});
```

### Métodos

```typescript
function saveDados();
function resetFormDados();
function saveSeguranca();
function resetFormSeguranca();
function savePreferencias();
function resetFormPreferencias();
function formatDate(date: string): string;
function formatUltimoAcesso(): string;
function getTypeLabel(type: string): string;
```

---

## 📊 Dados Mock

### Usuário

```typescript
{
  nome: 'João Silva',
  email: 'joao@example.com',
  type: 'FULL',
  dataCriacao: '2025-01-15',
  ultimoAcesso: '2025-10-17T14:30:00'
}
```

### Sessões

```typescript
[
  {
    id: 1,
    dispositivo: "Chrome no Windows",
    localizacao: "São Paulo, BR",
    ultimoAcesso: new Date(),
    ativa: true,
  },
  {
    id: 2,
    dispositivo: "Safari no iPhone",
    localizacao: "São Paulo, BR",
    ultimoAcesso: new Date(Date.now() - 86400000),
    ativa: false,
  },
];
```

---

## 🎨 Cores e Temas

### Tabs

- **Dados Pessoais**: Primary color
- **Segurança**: Lock icon
- **Preferências**: Cog icon

### Zona de Risco

- **Border**: Red (error)
- **Icons**: Alert, Delete
- **Buttons**: Error color

---

## 📱 Responsividade

### Desktop (>1024px)

- Avatar sidebar esquerda (3 colunas)
- Formulário direita (9 colunas)
- Tabs completos

### Tablet (600-1024px)

- Redimensionado proporcionalmente
- Mantém estrutura

### Mobile (<600px)

- Avatar em cima
- Formulário em baixo
- Stack vertical completo

---

## 🧪 Teste Manualmente

### Dados Pessoais

1. Ir para `/perfil`
2. Editar Nome → Salvar
3. Editar Email → Salvar
4. Cancelar em meio a edição

### Segurança

1. Clicar tab "Segurança"
2. Tentar deixar campo vazio → erro
3. Tentar senha diferente → erro
4. Preencher tudo corretamente → Salvar
5. Verificar toggle de visibilidade

### Preferências

1. Clicar tab "Preferências"
2. Mudar idioma
3. Mudar moeda
4. Desmarcar checkboxes
5. Mudar tema → Salvar

### Zona de Risco

1. Scroll até final
2. Ver card em red
3. Botões visíveis

---

## 💡 Tipos de Usuário

- **USER**: Usuário comum
- **TRADER**: Trader
- **ADMIN**: Administrador
- **USER_TRADER**: Usuário + Trader
- **FULL**: Full Access

---

## 🔐 Campos Sensíveis

- Email
- Senha (nunca retorna em responses)
- CPF (formato de máscara)
- Sessões ativas

---

**Versão**: 2.0  
**Status**: ✅ COMPLETO
