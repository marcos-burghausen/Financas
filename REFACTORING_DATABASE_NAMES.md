# 🔄 Refatoração: Renomeação de Tabelas e Campos do Banco de Dados

## 📋 Resumo das Alterações

Este documento mapeia todas as alterações de nomes de tabelas, models e campos no banco de dados, identificando onde o código backend ainda precisa ser atualizado.

---

## 🗃️ Tabelas Renomeadas

| Antigo                 | Novo                   | Status          |
| ---------------------- | ---------------------- | --------------- |
| `contas`               | `accounts`             | ✅ Model criado |
| `lancamentos`          | `launches`             | ✅ Model criado |
| `credit_card_invoices` | `credit_card_invoices` | ✅ Mantido      |

---

## 📦 Models Renomeados

| Antigo              | Novo                | Arquivo                  |
| ------------------- | ------------------- | ------------------------ |
| `Conta`             | `Account`           | `app/Models/Account.php` |
| `Lancamento`        | `Launch`            | `app/Models/Launch.php`  |
| `CreditCardInvoice` | `CreditCardInvoice` | Mantido                  |

---

## 🔤 Campos Renomeados

### Tabela `accounts` (antiga `contas`)

| Campo Antigo              | Campo Novo               | Tipo        | Observação               |
| ------------------------- | ------------------------ | ----------- | ------------------------ |
| `saldo`                   | `balance`                | integer     | ✅ Renomeado             |
| `saldo_inicial`           | ❌ _REMOVIDO_            | -           | ⚠️ Campo não existe mais |
| `incluir_em_soma_inicial` | `include_in_initial_sum` | boolean     | ✅ Renomeado             |
| `descricao`               | `description`            | string      | ✅ Renomeado             |
| `tipo_conta`              | `account_type`           | enum        | ✅ Renomeado             |
| `status_conta`            | `account_status`         | enum        | ✅ Renomeado             |
| `dia_fechamento`          | `closing_day`            | tinyInteger | ✅ Renomeado             |
| `dia_vencimento`          | `due_day`                | tinyInteger | ✅ Renomeado             |
| `conta_pai_id`            | `parent_account_id`      | bigInteger  | ✅ Renomeado             |

### Tabela `launches` (antiga `lancamentos`)

| Campo Antigo             | Campo Novo           | Tipo        | Observação   |
| ------------------------ | -------------------- | ----------- | ------------ |
| `descricao`              | `description`        | string(500) | ✅ Renomeado |
| `valor`                  | `value`              | integer     | ✅ Renomeado |
| `tipo_lancamento`        | `launch_type`        | enum        | ✅ Renomeado |
| `is_estorno`             | `is_refund`          | boolean     | ✅ Renomeado |
| `original_lancamento_id` | `original_launch_id` | bigInteger  | ✅ Renomeado |
| `recorrencia`            | `recurrence`         | enum        | ✅ Renomeado |
| `num_parcelas`           | `qtd_installments`   | integer     | ✅ Renomeado |
| `num_parcela`            | `num_installment`    | integer     | ✅ Renomeado |
| `tipo_parcela`           | `installment_type`   | enum        | ✅ Renomeado |
| `periodicidade`          | `periodicity`        | enum        | ✅ Renomeado |
| `data_vencimento`        | `due_date`           | date        | ✅ Renomeado |
| `status_lancamento`      | `launch_status`      | enum        | ✅ Renomeado |
| `categoria`              | `category`           | string(30)  | ✅ Renomeado |
| `subcategoria`           | `subcategory`        | string(30)  | ✅ Renomeado |
| `observacoes`            | `observations`       | text        | ✅ Renomeado |
| `data_lancamento`        | `launch_date`        | date        | ✅ Renomeado |
| `data_efetivacao`        | `effective_date`     | date        | ✅ Renomeado |
| `conta_id`               | `account_id`         | bigInteger  | ✅ Renomeado |

### Tabela `credit_card_invoices`

| Campo Antigo              | Campo Novo          | Tipo       | Observação   |
| ------------------------- | ------------------- | ---------- | ------------ |
| `conta_id`                | `account_id`        | bigInteger | ✅ Renomeado |
| `competencia`             | `competence`        | string(7)  | ✅ Renomeado |
| `data_fechamento`         | `closing_date`      | date       | ✅ Renomeado |
| `data_vencimento`         | `due_date`          | date       | ✅ Renomeado |
| `status_fatura`           | `status_invoice`    | enum       | ✅ Renomeado |
| `total_fatura`            | `total_invoice`     | integer    | ✅ Renomeado |
| `valor_pago`              | `value_pay`         | integer    | ✅ Renomeado |
| `encargos`                | `charges`           | integer    | ✅ Renomeado |
| `pago_em`                 | `pay_in`            | timestamp  | ✅ Renomeado |
| `lancamento_pagamento_id` | `launch_payment_id` | bigInteger | ✅ Renomeado |

---

## ⚠️ ARQUIVOS QUE PRECISAM SER ATUALIZADOS

### 1. **AuthController.php** ✅ PARCIALMENTE ATUALIZADO

**Localização**: `backend/app/Http/Controllers/AuthController.php`

#### Problemas encontrados:

**Linha ~189-194** (método `callback`):

```php
// ❌ PRECISA ATUALIZAR
$carteira = new Conta;  // → Account
$carteira->descricao = "Carteira de uso pessoal";  // → description
$carteira->tipoConta = "Pessoal";  // → account_type
```

**✅ Corrigir para:**

```php
$carteira = new Account;
$carteira->description = "Carteira de uso pessoal";
$carteira->account_type = "CARTEIRA";  // Nota: usar valor do ENUM correto
$carteira->account_status = "ATIVO";
```

---

### 2. **UserDataTrait.php** ❌ PRECISA ATUALIZAÇÃO COMPLETA

**Localização**: `backend/app/Http/Traits/UserDataTrait.php`

#### Problemas encontrados:

**Linha ~115-131** (método `getUserData` - seção summary):

```php
// ❌ NOMES DE TABELAS ANTIGOS
'saldoAtual' => DB::table('contas')  // → accounts
    ->where('account_type', '!=', 'Cartão de Crédito')  // → 'CARTAO_CREDITO'
    ->sum('balance'),

'totalReceitas' => DB::table('lancamentos')  // → launches
    ->where('launch_type', 'RECEITA')
    ->sum('value'),

'totalDespesas' => DB::table('lancamentos')  // → launches
    ->where('launch_type', 'DESPESA')
    ->sum('valor'),  // → value
```

**✅ Corrigir para:**

```php
'saldoAtual' => DB::table('accounts')
    ->where('user_id', $user->id)
    ->where('account_type', '!=', 'CARTAO_CREDITO')
    ->sum('balance'),

'totalReceitas' => DB::table('launches')
    ->where('user_id', $user->id)
    ->where('launch_type', 'RECEITA')
    ->whereYear('due_date', $year)
    ->whereMonth('due_date', $month)
    ->sum('value'),

'totalDespesas' => DB::table('launches')
    ->where('user_id', $user->id)
    ->where('launch_type', 'DESPESA')
    ->whereYear('due_date', $year)
    ->whereMonth('due_date', $month)
    ->sum('value'),
```

**Linha ~141-156** (queries de totais por conta):

```php
// ❌ NOMES DE TABELAS E CAMPOS ANTIGOS
$totalReceitas = DB::table('lancamentos')
    ->where('account_id', $conta->id)
    ->where('launch_type', 'RECEITA')
    ->sum('value');

$totalDespesas = DB::table('lancamentos')
    ->where('conta_id', $conta->id)  // → account_id
    ->where('launch_type', 'DESPESA')
    ->sum('value');

$conta->saldo_previsto = $conta->saldo_inicial + $totalReceitas - $totalDespesas;
// → Problema: saldo_inicial não existe mais!
```

**✅ Corrigir para:**

```php
$totalReceitas = DB::table('launches')
    ->where('account_id', $conta->id)
    ->where('launch_type', 'RECEITA')
    ->whereYear('due_date', $year)
    ->whereMonth('due_date', $month)
    ->sum('value');

$totalDespesas = DB::table('launches')
    ->where('account_id', $conta->id)
    ->where('launch_type', 'DESPESA')
    ->whereYear('due_date', $year)
    ->whereMonth('due_date', $month)
    ->sum('value');

// ⚠️ PROBLEMA: saldo_inicial foi removido!
// Solução: usar balance atual ou criar lógica alternativa
$conta->saldo_previsto = $conta->balance + $totalReceitas - $totalDespesas;
```

**Linha ~137** (relacionamento):

```php
// ❌ NOME DO RELACIONAMENTO ERRADO
'contas' => $user->contas()  // → accounts()
```

**✅ Corrigir para:**

```php
'contas' => $user->accounts()
    ->where('account_type', '!=', 'CARTAO_CREDITO')
    ->get()
```

**Linha ~162** (cartões):

```php
// ❌ NOME DO RELACIONAMENTO
$user->contas()  // → accounts()
    ->where('account_type', 'Cartão de Crédito')  // → 'CARTAO_CREDITO'
```

**Linha ~183-188** (atributos de fatura):

```php
// ❌ NOMES DE CAMPOS ANTIGOS
$cartao->lancamentos_fatura_vigente = ...  // OK (atributo dinâmico)
$cartao->data_fechamento = $faturaVigente ? $faturaVigente->data_fechamento : null;
// → closing_date
$cartao->data_vencimento = $faturaVigente ? $faturaVigente->data_vencimento : null;
// → due_date
$cartao->valor_em_aberto = $valorEmAberto;  // OK (atributo dinâmico)
```

**Linha ~195** (relacionamento):

```php
// ❌ NOME DO RELACIONAMENTO ERRADO
'contasNames' => $user->account()->pluck("name"),  // → accounts()
```

---

### 3. **ReleasesMonthTrait.php** ❌ PRECISA ATUALIZAÇÃO COMPLETA

**Localização**: `backend/app/Http/Traits/ReleasesMonthTrait.php`

#### Problemas encontrados:

**Linha ~6** (import):

```php
// ❌ MODEL ANTIGO
use App\Models\Conta;  // → Account
```

**Linha ~45** (campo):

```php
// ❌ CAMPO ANTIGO
if (substr($release->data_vencimento, 5, 2) === $mes ...)
// → due_date
```

**Linha ~53-54** (campo):

```php
// ❌ CAMPOS ANTIGOS
->where('status_lancamento', $status)->sum('valor');
// → launch_status, value
```

**Linha ~58** (campo):

```php
// ❌ CAMPO ANTIGO
->sum('valor');  // → value
```

**Linha ~66** (campos):

```php
// ❌ CAMPOS ANTIGOS
if ($release->status_lancamento === 'EFETIVADA' && isset($release->data_vencimento) ...)
    $totalExpensesDay += $release->valor;
// → launch_status, due_date, value
```

**Linha ~75-78** (campos):

```php
// ❌ CAMPOS ANTIGOS
if (isset($totalByCategory[$release->categoria])) {
    $totalByCategory[$release->categoria] += $release->valor;
} else {
    $totalByCategory[$release->categoria] = $release->valor;
}
// → category, value
```

**Linha ~84-86** (relacionamento e campo):

```php
// ❌ RELACIONAMENTO E CAMPO ANTIGOS
$saldoInicialContas = $user->contas()  // → accounts()
    ->where('incluir_em_soma_inicial', true)  // → include_in_initial_sum
    ->sum('saldo_inicial');  // → ⚠️ CAMPO REMOVIDO!
```

**Linha ~88-92** (relacionamento e campos):

```php
// ❌ RELACIONAMENTO E CAMPOS ANTIGOS
$lancamentosAnteriores = $user->lancamentos()  // → launches()
    ->where('status_lancamento', 'EFETIVADA')  // → launch_status
    ->where('data_efetivacao', '<', $dataLimite)  // → effective_date
    ->get();

$totalReceitasAnteriores = $lancamentosAnteriores->where('tipo_lancamento', 'RECEITA')->sum('valor');
// → launch_type, value
$totalDespesasAnteriores = $lancamentosAnteriores->where('tipo_lancamento', 'DESPESA')->sum('valor');
// → launch_type, value
```

**Linha ~103-112** (relacionamento e campos):

```php
// ❌ RELACIONAMENTO E CAMPOS ANTIGOS
$lancamentosDoMes = $user->lancamentos()  // → launches()
    ->where('status_lancamento', 'EFETIVADA')  // → launch_status
    ->whereBetween('data_efetivacao', [$dataInicio, $dataFim])  // → effective_date
    ->get();

$totalReceitasDoMes = $lancamentosDoMes->where('tipo_lancamento', 'RECEITA')->sum('valor');
// → launch_type, value
$totalDespesasDoMes = $lancamentosDoMes->where('tipo_lancamento', 'DESPESA')->sum('valor');
// → launch_type, value
```

---

### 4. **DemoDataSeeder.php** ❌ PRECISA ATUALIZAÇÃO COMPLETA

**Localização**: `backend/database/seeders/DemoDataSeeder.php`

#### Problemas encontrados:

**Linha ~7-8** (imports):

```php
// ❌ MODELS ANTIGOS
use App\Models\Conta;  // → Account
use App\Models\Lancamento;  // → Launch
```

**Todas as instâncias** (aproximadamente linhas 130-400+):

```php
// ❌ PRECISA ATUALIZAR TODAS AS CRIAÇÕES:
Conta::create([...])  // → Account::create([...])
Lancamento::create([...])  // → Launch::create([...])

// E todos os campos dentro dessas criações
```

---

### 5. **UserTest.php** ❌ PRECISA ATUALIZAÇÃO

**Localização**: `backend/tests/Feature/UserTest.php`

#### Problemas encontrados:

**Linha ~82**:

```php
// ❌ MODEL ANTIGO
$conta = Conta::where('user_id', $user->id)->first();  // → Account
```

**Linha ~221, 240, 255**:

```php
// ❌ MODEL ANTIGO
$wallet = Conta::where('name', 'Pessoal')->where('user_id', $this->user['id'])->first();
// → Account
```

---

### 6. **User.php** ✅ PARCIALMENTE ATUALIZADO

**Localização**: `backend/app/Models/User.php`

#### Status Atual:

- ✅ `launches()` - Correto
- ✅ `revenues()` - Correto (usa Launch)
- ✅ `expenses()` - Correto (usa Launch)
- ⚠️ `revenues()` e `expenses()` ainda usam `tipo_lancamento` (português)

**Precisa atualizar:**

```php
// Linha ~62-64
public function revenues()
{
    return $this->hasMany(Launch::class)->where('tipo_lancamento', 'Receita');
    // → launch_type, e valor ENUM correto 'RECEITA'
}

// Linha ~70-72
public function expenses()
{
    return $this->hasMany(Launch::class)->where('tipo_lancamento', 'Despesa');
    // → launch_type, e valor ENUM correto 'DESPESA'
}
```

---

## 🎯 CHECKLIST DE CORREÇÕES

### Alta Prioridade (Bloqueia funcionamento)

- [ ] **UserDataTrait.php**

  - [ ] Atualizar todos os `DB::table('contas')` → `'accounts'`
  - [ ] Atualizar todos os `DB::table('lancamentos')` → `'launches'`
  - [ ] Atualizar todos os campos de `contas/lancamentos`
  - [ ] Resolver problema do `saldo_inicial` removido
  - [ ] Atualizar `$user->contas()` → `$user->accounts()`
  - [ ] Atualizar `$user->lancamentos()` → `$user->launches()`
  - [ ] Atualizar ENUMs: `'Cartão de Crédito'` → `'CARTAO_CREDITO'`

- [ ] **ReleasesMonthTrait.php**

  - [ ] Atualizar import: `Conta` → `Account`
  - [ ] Atualizar `$user->contas()` → `$user->accounts()`
  - [ ] Atualizar `$user->lancamentos()` → `$user->launches()`
  - [ ] Atualizar todos os campos antigos
  - [ ] Resolver `saldo_inicial` removido

- [ ] **AuthController.php**
  - [ ] Atualizar `new Conta` → `new Account`
  - [ ] Atualizar campos: `descricao`, `tipoConta`, etc.

### Média Prioridade (Testes e Seeds)

- [ ] **DemoDataSeeder.php**

  - [ ] Atualizar imports: `Conta` → `Account`, `Lancamento` → `Launch`
  - [ ] Atualizar todas as criações de objetos
  - [ ] Atualizar todos os campos

- [ ] **UserTest.php**
  - [ ] Atualizar `Conta::` → `Account::`
  - [ ] Atualizar campos nos testes

### Baixa Prioridade (Ajustes)

- [ ] **User.php (Model)**
  - [ ] Atualizar `tipo_lancamento` → `launch_type` em revenues()
  - [ ] Atualizar `tipo_lancamento` → `launch_type` em expenses()
  - [ ] Atualizar valores ENUM: `'Receita'` → `'RECEITA'`, `'Despesa'` → `'DESPESA'`

---

## ⚡ PROBLEMA CRÍTICO: Campo `saldo_inicial` Removido

### ⚠️ Situação:

O campo `saldo_inicial` foi removido da tabela `accounts`, mas o código ainda depende dele em vários lugares:

**Usos atuais:**

1. `UserDataTrait::getUserData()` - linha ~155

   ```php
   $conta->saldo_previsto = $conta->saldo_inicial + $totalReceitas - $totalDespesas;
   ```

2. `ReleasesMonthTrait::obterSaldoInicial()` - linha ~84-86
   ```php
   $saldoInicialContas = $user->accounts()
       ->where('include_in_initial_sum', true)
       ->sum('saldo_inicial');  // ❌ Campo não existe!
   ```

### 💡 Soluções Possíveis:

#### Opção 1: Adicionar o campo de volta à migration

```php
// Em 2024_06_18_153844_create_accounts_table.php
$table->integer('initial_balance')->default(0);
```

#### Opção 2: Usar o campo `balance` atual

```php
// Considerar balance como o saldo inicial
$saldoInicialContas = $user->accounts()
    ->where('include_in_initial_sum', true)
    ->sum('balance');
```

#### Opção 3: Criar lógica de cálculo retroativo

```php
// Calcular saldo inicial baseado em lançamentos históricos
// (mais complexo, requer análise de todos os lançamentos)
```

---

## 🔍 ENUMS QUE MUDARAM

### `account_type` (antiga `tipo_conta`):

```php
// Valores antigos (português):
'Carteira', 'Conta Corrente', 'Poupança', 'Investimento', 'Outro', 'Cartão de Crédito'

// ✅ Valores novos (inglês uppercase com underscore):
'CARTEIRA', 'CONTA_CORRENTE', 'POUPANCA', 'INVESTIMENTO', 'OUTRO', 'CARTAO_CREDITO'
```

### `account_status` (antiga `status_conta`):

```php
// Valores antigos: 'Ativo', 'Inativo'
// ✅ Valores novos: 'ATIVO', 'INATIVO'
```

### `launch_type` (antiga `tipo_lancamento`):

```php
// Valores antigos: 'Receita', 'Despesa', 'CartaoCredito'
// ✅ Valores novos: 'RECEITA', 'DESPESA', 'CARTAO_CREDITO'
```

### `launch_status` (antiga `status_lancamento`):

```php
// Valores antigos: 'Efetivada', 'Pendente'
// ✅ Valores novos: 'EFETIVADA', 'PENDENTE'
```

### `recurrence` (antiga `recorrencia`):

```php
// Valores antigos: 'Não recorrente', 'Parcelado', 'Fixa'
// ✅ Valores novos: 'NAO_RECORRENTE', 'PARCELADO', 'FIXA'
```

### `installment_type` (antiga `tipo_parcela`):

```php
// Valores antigos: 'total', 'parcela'
// ✅ Valores novos: 'TOTAL', 'PARCELA'
```

### `periodicity` (antiga `periodicidade`):

```php
// Valores antigos: 'Mensal', 'Diario', 'Semanal', 'Quinzenal', 'Trimestral', 'Anual'
// ✅ Valores novos: 'MENSAL', 'DIARIO', 'SEMANAL', 'QUINZENAL', 'TRIMESTRAL', 'ANUAL'
```

### `status_invoice` (antiga `status_fatura`):

```php
// Valores: 'ABERTA', 'FECHADA', 'PARCIAL', 'PAGA'
```

---

## 📝 SCRIPT DE BUSCA E SUBSTITUIÇÃO

### Buscar por nomes antigos:

```bash
# Models
grep -r "Conta::" backend/
grep -r "Lancamento::" backend/
grep -r "new Conta" backend/
grep -r "new Lancamento" backend/

# Relacionamentos
grep -r "->contas()" backend/
grep -r "->lancamentos()" backend/

# Tabelas
grep -r "DB::table('contas')" backend/
grep -r "DB::table('lancamentos')" backend/

# Campos específicos
grep -r "saldo_inicial" backend/
grep -r "tipo_lancamento" backend/
grep -r "data_vencimento" backend/
grep -r "status_lancamento" backend/
grep -r "conta_id" backend/
```

---

## ✅ ORDEM DE EXECUÇÃO RECOMENDADA

1. **Primeiro**: Decidir sobre o `saldo_inicial` (Opção 1, 2 ou 3)
2. **Segundo**: Atualizar **User.php** (relacionamentos)
3. **Terceiro**: Atualizar **AuthController.php**
4. **Quarto**: Atualizar **ReleasesMonthTrait.php**
5. **Quinto**: Atualizar **UserDataTrait.php**
6. **Sexto**: Atualizar **DemoDataSeeder.php**
7. **Sétimo**: Atualizar **UserTest.php**
8. **Oitavo**: Rodar testes e verificar erros
9. **Nono**: Testar no frontend
10. **Décimo**: Atualizar documentação

---

## 🧪 TESTES APÓS CORREÇÕES

### Verificar:

- [ ] Login e cadastro funcionam
- [ ] Dashboard carrega dados
- [ ] Lançamentos são criados/listados corretamente
- [ ] Contas aparecem corretamente
- [ ] Faturas de cartão funcionam
- [ ] Filtros por mês funcionam
- [ ] Seeds rodam sem erros
- [ ] Testes unitários passam

---

## 📞 NOTAS IMPORTANTES

1. **Backup do banco**: Fazer backup antes de aplicar migrations
2. **Migrations**: Verificar se todas as migrations foram aplicadas corretamente
3. **Cache**: Limpar cache do Laravel após mudanças (`php artisan cache:clear`)
4. **Config**: Verificar config de database (`config/database.php`)
5. **Seeders**: Rodar seeders após correções para popular dados de teste

---

**Data de Criação**: Janeiro 2025  
**Versão**: 1.0.0  
**Status**: 🚨 CORREÇÕES PENDENTES
