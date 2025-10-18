# 🏗️ Arquitetura Backend - Diagrama Visual

**Data**: Outubro 18, 2025

---

## 📐 Fluxo de Requisições

```
┌─────────────────────────────────────────────────────────────────────┐
│                    FRONTEND (Vue 3 + Vuetify)                       │
│                 LoginView, ReceitasView, etc                        │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                    HTTP/HTTPS Request
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        LARAVEL API GATEWAY                           │
│                          (nginx/apache)                              │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                    Middleware Stack
                    (CORS, Auth, etc)
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    AUTHENTICATION LAYER                              │
│  ┌─────────────┐  ┌────────────┐  ┌──────────────────────────────┐ │
│  │    JWT      │  │  Sanctum   │  │   OAuth (Social Login)       │ │
│  │ (Legacy)    │  │ (Modern)   │  │ Facebook, Google, LinkedIn   │ │
│  └─────────────┘  └────────────┘  └──────────────────────────────┘ │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                       ROUTING LAYER                                  │
│                        (routes/api.php)                              │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ POST   /api/login              → SanctumAuthController       │  │
│  │ POST   /api/lancamentos        → LancamentoController        │  │
│  │ POST   /api/wallet             → WalletsController           │  │
│  │ PUT    /api/user/profile       → UserController              │  │
│  │ POST   /api/save-category      → CategoryController          │  │
│  │ GET    /api/admin/users        → AdminController             │  │
│  │ ... (40+ rotas total)                                        │  │
│  └──────────────────────────────────────────────────────────────┘  │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      CONTROLLER LAYER                                │
│                    (app/Http/Controllers/)                           │
│                                                                       │
│  ┌──────────────────┐  ┌──────────────────┐  ┌────────────────────┐│
│  │ AuthController   │  │LancamentoControl │  │ CategoryController ││
│  │                  │  │ler               │  │                    ││
│  │ • auth()         │  │ • saveLancamento │  │ • saveCategory()   ││
│  │ • respondToken() │  │ • efetivar()     │  │ • deleteCategory() ││
│  │ • socialAuth()   │  │ • edit()         │  │                    ││
│  └──────────────────┘  │ • delete()       │  └────────────────────┘│
│                        └──────────────────┘                          │
│                                                                       │
│  ┌──────────────────┐  ┌──────────────────┐  ┌────────────────────┐│
│  │ WalletsController│  │UserController    │  │ AdminController    ││
│  │                  │  │                  │  │                    ││
│  │ • saveWallet()   │  │ • show()         │  │ • listUsers()      ││
│  │ • editWallets()  │  │ • updateProfile()│  │ • blockUser()      ││
│  │ • deleteWallets()│  │ • updatePassword()│  │ • getLogs()        ││
│  └──────────────────┘  └──────────────────┘  └────────────────────┘│
│                                                                       │
│  (+ 10 mais controllers: Revenue, Expense, Dashboard, Notification) │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    REQUEST VALIDATION LAYER                          │
│                    (app/Http/Requests/)                              │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │ StoreLancamentoRequest                                         │ │
│  │                                                                 │ │
│  │ prepareForValidation():                                        │ │
│  │   • transformValor()       (1.234,56 → 123456)               │ │
│  │   • transformTipoLancamento (string → Enum)                  │ │
│  │   • transformRecorrencia    (string → Enum)                  │ │
│  │                                                                 │ │
│  │ rules():                                                       │ │
│  │   • 13 validações principais                                  │ │
│  │   • Conditional rules (required_if)                           │ │
│  │   • Custom validation rules                                   │ │
│  └────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      SERVICE LAYER                                   │
│                    (app/Services/)                                   │
│                                                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │ LancamentoService (302 linhas)                                │ │
│  │                                                                 │ │
│  │ createLancamento()                                            │ │
│  │   ├─ createStandardLancamento()                              │ │
│  │   │  ├─ createLancamentoUnico()                             │ │
│  │   │  ├─ createLancamentoParceladoStandard()                │ │
│  │   │  └─ createLancamentoFixoStandard()                     │ │
│  │   └─ createCreditCardLancamento()                            │ │
│  │      ├─ Parcelas múltiplas                                  │ │
│  │      └─ Múltiplas faturas                                   │ │
│  │                                                                 │ │
│  │ efetivarLancamento()                                          │ │
│  │   └─ atualizarSaldos()                                        │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │ CreditCardInvoiceService                                      │ │
│  │                                                                 │ │
│  │ • createInvoice()                                             │ │
│  │ • updateInvoice()                                             │ │
│  │ • closeInvoice()                                              │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │ WalletService                                                 │ │
│  │                                                                 │ │
│  │ • createWallet()                                              │ │
│  │ • updateWallet()                                              │ │
│  │ • deleteWallet()                                              │ │
│  └────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        TRAIT LAYER                                   │
│                    (app/Http/Traits/)                                │
│                                                                       │
│  ┌────────────────────┐  ┌────────────────────┐  ┌───────────────┐ │
│  │ UserDataTrait      │  │ReleasesMonthTrait  │  │GroupRelease   │ │
│  │                    │  │                    │  │Trait          │ │
│  │ • getUserData()    │  │ • classifies()     │  │ • groupBy()   │ │
│  │ • Cache support    │  │ • group by month   │  │ • byCategory()│ │
│  └────────────────────┘  └────────────────────┘  └───────────────┘ │
│                                                                       │
│  ┌───────────────────────────────────────────────────────────────┐ │
│  │ TotalByCategoryTrait                                          │ │
│  │ • calculateByCategory()                                       │ │
│  │ • percentages()                                               │ │
│  └───────────────────────────────────────────────────────────────┘ │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                       MODEL LAYER                                    │
│                      (app/Models/)                                   │
│                                                                       │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ User (545 linhas)                                            │  │
│  │                                                               │  │
│  │ Relationships:                                               │  │
│  │  • 1:N lancamentos()                                        │  │
│  │  • 1:N revenues()      [filtered by tipo='Receita']        │  │
│  │  • 1:N expenses()      [filtered by tipo='Despesa']        │  │
│  │  • 1:N contas()                                             │  │
│  │  • N:N roles()                                              │  │
│  │  • 1:1 notificationSettings()                               │  │
│  │                                                               │  │
│  │ Attributes:                                                 │  │
│  │  • id, name, email, password (hashed)                       │  │
│  │  • type: USER, TRADER, ADMIN, USER_TRADER, FULL             │  │
│  │  • email_verified_at                                        │  │
│  │  • facebookId, googleId, linkedinId                         │  │
│  │  • timestamps                                               │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                       │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Lancamento                                                   │  │
│  │                                                               │  │
│  │ Relationships:                                               │  │
│  │  • N:1 user()                                                │  │
│  │  • N:1 contaModel()                                          │  │
│  │  • N:1 invoice()       [Credit Card Invoice]                │  │
│  │  • N:1 original()      [For reversals]                      │  │
│  │                                                               │  │
│  │ Attributes:                                                 │  │
│  │  • id, user_id, installment_group_id (UUID)                 │  │
│  │  • descricao, valor (em centavos)                           │  │
│  │  • tipo_lancamento: RECEITA, DESPESA, CARTAO_CREDITO        │  │
│  │  • is_estorno, original_lancamento_id                       │  │
│  │  • recorrencia, qtd_parcelas, num_parcela                   │  │
│  │  • tipo_parcela, periodicidade                              │  │
│  │  • data_vencimento, data_lancamento, data_efetivacao        │  │
│  │  • status_lancamento: EFETIVADA, PENDENTE                   │  │
│  │  • categoria, subcategoria, observacoes                     │  │
│  │  • conta_id, invoice_id, timestamps                         │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                       │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ Conta                                                        │  │
│  │                                                               │  │
│  │ Relationships:                                               │  │
│  │  • N:1 user()                                                │  │
│  │  • 1:N lancamentos()                                         │  │
│  │  • 1:N childAccounts()  [sub-contas/cartões]               │  │
│  │  • N:1 parentAccount()  [conta pai]                         │  │
│  │  • 1:N creditCardInvoices()                                 │  │
│  │                                                               │  │
│  │ Attributes:                                                 │  │
│  │  • id, user_id, name, icon, color, descricao               │  │
│  │  • saldo, saldo_inicial, incluir_em_soma_inicial            │  │
│  │  • tipo_conta, status_conta                                 │  │
│  │  • limite, dia_fechamento, dia_vencimento                  │  │
│  │  • conta_pai_id (for hierarchy)                             │  │
│  │  • timestamps                                               │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                       │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ CreditCardInvoice                                            │  │
│  │                                                               │  │
│  │ Attributes:                                                 │  │
│  │  • id, conta_id, competencia (MES/YYYY)                     │  │
│  │  • data_abertura, data_fechamento, data_vencimento          │  │
│  │  • saldo_devedor, saldo_pago                                │  │
│  │  • status: Aberta, Fechada, Paga, Atrasada                  │  │
│  │  • timestamps                                               │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                       │
│  (+ Categoria, Subcategoria, Role, Log models)                     │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      CACHING LAYER                                   │
│                     (Redis/File)                                     │
│                                                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │ FinancasCache                                                 │ │
│  │                                                                 │ │
│  │ Cache Keys:                                                   │ │
│  │  • login_data_user_{id}_month_{YYYY-MM} (10 min)            │ │
│  │  • user_data_{id}_{type}_{YYYY-MM} (30 min)                │ │
│  │  • lancamentos_by_category_{id} (60 min)                    │ │
│  │  • FLOW_TITLE::{email} (30 min)                             │ │
│  │                                                                 │ │
│  │ Invalidation:                                                 │ │
│  │  • Manual: POST /api/user-data/invalidate-cache             │ │
│  │  • Automatic: On save/update/delete                         │ │
│  └────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      DATABASE LAYER                                  │
│                      (MySQL/PostgreSQL)                              │
│                                                                       │
│  Tables (14):                                                        │
│  ├─ users                    (545 rows + indexes)                   │
│  ├─ contas                   (hierarchical accounts)                │
│  ├─ lancamentos              (transactions)                         │
│  ├─ credit_card_invoices     (CC statements)                        │
│  ├─ categories               (category system)                      │
│  ├─ subcategories            (sub-categories)                       │
│  ├─ roles                    (RBAC)                                 │
│  ├─ role_user               (many-to-many)                          │
│  ├─ user_notification_settings                                      │
│  ├─ logs                     (audit trail)                          │
│  ├─ personal_access_tokens   (Sanctum)                             │
│  ├─ password_reset_tokens                                          │
│  ├─ failed_jobs              (queue failures)                       │
│  └─ jobs                     (job queue)                            │
│                                                                       │
│  Relationships:                                                      │
│  ├─ users 1:N lancamentos         (ON DELETE CASCADE)               │
│  ├─ users 1:N contas              (ON DELETE CASCADE)               │
│  ├─ contas 1:N lancamentos        (ON DELETE RESTRICT)             │
│  ├─ contas 1:N credit_card_invoices                                 │
│  ├─ lancamentos N:1 credit_card_invoices (ON DELETE SET NULL)      │
│  └─ lancamentos N:1 lancamentos   (estornos - reversals)           │
└──────────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    EXTERNAL SERVICES                                 │
│                                                                       │
│  ┌──────────────────────┐  ┌────────────────────────────────────┐  │
│  │ Email Service        │  │ OAuth Providers                    │  │
│  │ (Mail/Mailable)      │  │                                    │  │
│  │                      │  │ • Facebook                         │  │
│  │ • Notifications      │  │ • Google                          │  │
│  │ • Confirmations      │  │ • LinkedIn                        │  │
│  │ • Reports            │  │ • Twitter                         │  │
│  └──────────────────────┘  └────────────────────────────────────┘  │
│                                                                       │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │ File Storage (Storage/public ou S3)                         │  │
│  │                                                               │  │
│  │ • User avatars                                               │  │
│  │ • Reports/Exports                                            │  │
│  │ • Documents                                                  │  │
│  └──────────────────────────────────────────────────────────────┘  │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
                        JSON Response
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    RESPONSE LAYER                                    │
│                                                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │ Success Response (201/200):                                   │ │
│  │ {                                                              │ │
│  │   "success": "Lançamento cadastrado com sucesso",             │ │
│  │   "token": "...",                                             │ │
│  │   "user": { ... },                                            │ │
│  │   "data": { ... },                                            │ │
│  │   "receipts": [ ... ],                                        │ │
│  │   "expenses": [ ... ]                                         │ │
│  │ }                                                              │ │
│  │                                                                 │ │
│  │ Error Response (400/500):                                     │ │
│  │ {                                                              │ │
│  │   "error": "Mensagem de erro",                                │ │
│  │   "validation_errors": { ... }                                │ │
│  │ }                                                              │ │
│  └────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                        HTTP/HTTPS Response
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    FRONTEND (Vue 3 + Pinia)                          │
│                                                                       │
│  • Parse response                                                    │
│  • Update state (Pinia stores)                                       │
│  • Show toast/notifications                                          │
│  • Redirect to new view                                              │
│  • Update UI with new data                                           │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📊 Fluxo de Dados - Exemplo: Criar Lançamento

```
1. USER ACTION
   Frontend: Click "Salvar Receita"
   └─ Event: handleSaveReceita()

2. DATA PREPARATION
   └─ FormLancamentos data:
      {
        descricao: "Salário",
        valor: "5.000,00",
        tipo_lancamento: "Receita",
        categoria: "Salário",
        subcategoria: "Mensal",
        conta_id: 1,
        data_vencimento: "2025-10-31",
        recorrencia: "Fixa",
        periodicidade: "MENSAL"
      }

3. HTTP REQUEST
   POST /api/lancamentos
   Headers:
     Authorization: Bearer {token}
     Content-Type: application/json
   Body: { ... dados acima ... }

4. ROUTING
   routes/api.php
   └─ Route::middleware('auth:sanctum')->group(function () {
        Route::post('/lancamentos', [LancamentoController::class, 'saveLancamento']);
      })

5. AUTHENTICATION
   SanctumGuard verifies token
   └─ auth()->user() → User model

6. CONTROLLER
   LancamentoController::saveLancamento()
   └─ Input: StoreLancamentoRequest
      Output: LancamentoService

7. VALIDATION
   StoreLancamentoRequest::prepareForValidation()
   ├─ transformValor():  "5.000,00" → 500000 (centavos)
   ├─ transformTipoLancamento(): "Receita" → "RECEITA"
   └─ transformRecorrencia(): "Fixa" → "FIXA"

   StoreLancamentoRequest::rules()
   ├─ 'valor' => 'required|integer|min:0.01'
   ├─ 'tipo_lancamento' => 'required|in:RECEITA,DESPESA,CARTAO_CREDITO'
   ├─ 'recorrencia' => 'required|in:NAO_RECORRENTE,PARCELADO,FIXA'
   ├─ 'conta_id' => 'required|exists:contas,id'
   └─ ... (13 validações total)

8. SERVICE LOGIC
   LancamentoService::createLancamento()
   ├─ tipo_lancamento === 'RECEITA'
   └─ recorrencia === 'FIXA'
      └─ LancamentoService::createLancamentoFixoStandard()
         ├─ Loop para cada mês (próximos 12 meses)
         └─ Lancamento::create([
              'user_id' => auth()->id(),
              'descricao' => 'Salário',
              'valor' => 500000,
              'tipo_lancamento' => 'RECEITA',
              'recorrencia' => 'FIXA',
              'periodicidade' => 'MENSAL',
              'status_lancamento' => 'PENDENTE',
              'conta_id' => 1,
              ...
            ])

9. DATABASE TRANSACTION
   DB::beginTransaction()
   ├─ Lancamento::create()
   │  └─ INSERT INTO lancamentos VALUES (...)
   ├─ Cache::invalidate()
   │  └─ Limpar cache de user_data_{id}
   └─ DB::commit()

10. RESPONSE PREPARATION
    UserDataTrait::getUserData($user, $mesAno, ['revenues', 'wallets'])
    ├─ Busca receitas do mês
    ├─ Busca saldos das contas
    ├─ Calcula totalizações
    └─ Retorna dados estruturados

11. HTTP RESPONSE
    return response()->json([
      'success' => 'Lançamento cadastrado com sucesso',
      'revenues' => [ ... array de receitas ... ],
      'wallets' => [ ... array de contas ... ]
    ], 201)

12. FRONTEND HANDLING
    ├─ Parse JSON response
    ├─ Validate status code (201 = Created)
    ├─ Update Pinia store
    │  └─ lancamentos.push(novo_lancamento)
    ├─ Show toast notification
    │  └─ "✅ Lançamento cadastrado com sucesso"
    ├─ Refresh ReceitasView
    │  └─ Atualizar cards KPI, datatable, gráficos
    └─ Redirect ou voltar
```

---

## 🔄 Fluxo de Dados - Exemplo: Efetivação de Lançamento

```
1. FRONTEND: Click "Efetivação" button em lançamento PENDENTE

2. REQUEST
   PATCH /api/lancamentos/{id}
   Body: { mesAno: "2025-10" }

3. CONTROLLER
   LancamentoController::receivedLancamento($request, Lancamento $lancamento)

4. SERVICE
   LancamentoService::efetivarLancamento(Lancamento $lancamento)
   ├─ Verificar se já está EFETIVADA (idempotent)
   ├─ Atualizar status para EFETIVADA
   ├─ Definir data_efetivacao = NOW()
   ├─ LancamentoService::atualizarSaldos($lancamento)
   │  ├─ IF tipo_lancamento === 'RECEITA':
   │  │  └─ $conta->saldo += valor
   │  └─ IF tipo_lancamento === 'DESPESA':
   │     └─ $conta->saldo -= valor
   └─ $lancamento->save()

5. RESPONSE
   {
     "success": "Lançamento efetivado com sucesso!",
     "revenues": [ ... updated revenues ... ],
     "wallets": [ ... updated wallets with new saldo ... ]
   }

6. FRONTEND
   ├─ Update Pinia state
   ├─ Refresh KPI cards with new saldo
   ├─ Update datatable (remove from PENDENTE, add to EFETIVADA)
   └─ Show toast: "✅ Lançamento efetivado com sucesso"
```

---

## 📈 Padrão de Resposta

### ✅ Sucesso (201/200)

```json
{
  "success": "Mensagem de sucesso",
  "token": "string",
  "user": {
    "id": 1,
    "name": "João",
    "email": "joao@example.com",
    "type": "USER"
  },
  "summary": {
    "total_receitas": 5000000,
    "total_despesas": 2000000,
    "saldo_contas": 3000000
  },
  "revenues": [
    {
      "id": 1,
      "descricao": "Salário",
      "valor": 5000000,
      "data_vencimento": "2025-10-31",
      "status_lancamento": "EFETIVADA"
    }
  ],
  "wallets": [
    {
      "id": 1,
      "name": "Conta Corrente",
      "saldo": 3000000,
      "tipo_conta": "Corrente"
    }
  ]
}
```

### ❌ Erro (400/500)

```json
{
  "error": "Mensagem de erro",
  "status": 400,
  "validation_errors": {
    "valor": ["O campo valor é obrigatório"],
    "conta_id": ["O campo conta_id não existe"]
  }
}
```

---

## 🎯 Resumo da Arquitetura

| Layer                  | Components             | Responsibilities                      |
| ---------------------- | ---------------------- | ------------------------------------- |
| **Presentation**       | Frontend Vue 3         | UI, Forms, State Management (Pinia)   |
| **API Gateway**        | nginx/apache           | Routing, CORS, Rate limiting          |
| **Authentication**     | JWT/Sanctum/OAuth      | Token validation, user identification |
| **Routing**            | routes/api.php         | URL to Controller mapping             |
| **Request Validation** | StoreLancamentoRequest | Data validation, transformation       |
| **Business Logic**     | Services               | Complex operations, transactions      |
| **Data Access**        | Models (Eloquent)      | Database abstraction                  |
| **Caching**            | Redis/File             | Performance optimization              |
| **Database**           | MySQL/PostgreSQL       | Persistent data storage               |

---

**Status**: ✅ Arquitetura bem estruturada e escalável
