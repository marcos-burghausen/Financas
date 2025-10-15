# Seeders - Sistema Financeiro

## 📋 Visão Geral

Este diretório contém os seeders responsáveis por popular o banco de dados com dados iniciais e de demonstração.

## 🌱 Seeders Disponíveis

### 1. RolesSeeder

**Arquivo:** `RolesSeeder.php`

Cria as 5 roles do sistema com suas respectivas permissões:

| Role            | Permissões    | Descrição                           |
| --------------- | ------------- | ----------------------------------- |
| **USER**        | 10 permissões | Usuário comum - finanças pessoais   |
| **TRADER**      | 9 permissões  | Investidor - foco em trading        |
| **USER_TRADER** | 19 permissões | Completo - finanças + investimentos |
| **ADMIN**       | 9 permissões  | Administrador do sistema            |
| **FULL**        | Todas (\*)    | Super admin - acesso total          |

### 2. DemoDataSeeder

**Arquivo:** `DemoDataSeeder.php`

Cria 5 usuários completos, um de cada role, com dados realistas:

#### 👥 Usuários Criados

| Nome         | Email           | Role        | Senha    | Descrição      |
| ------------ | --------------- | ----------- | -------- | -------------- |
| João Silva   | joao@teste.com  | USER        | senha123 | Usuário comum  |
| Maria Santos | maria@teste.com | TRADER      | senha123 | Investidora    |
| Pedro Costa  | pedro@teste.com | USER_TRADER | senha123 | Completo       |
| Ana Oliveira | ana@teste.com   | ADMIN       | senha123 | Administradora |
| Carlos Admin | admin@teste.com | FULL        | senha123 | Super admin    |

#### 💰 Dados Criados por Usuário

**Contas Bancárias:**

-   Conta Corrente (Nubank) - R$ 2.500,00
-   Poupança (BB) - R$ 5.000,00
-   Cartão Nubank Visa (limite R$ 5.000)
-   Cartão Inter Mastercard (limite R$ 3.000)
-   Investimentos (apenas TRADER/USER_TRADER) - R$ 50.000,00

**Lançamentos:**

-   **USER**: ~30 lançamentos

    -   Receitas: Salário, Freelance
    -   Despesas fixas: Aluguel, condomínio, contas
    -   Despesas variáveis: Supermercado, restaurante, streaming
    -   Parcelamentos: Celular 12x, Supermercado 3x
    -   Estorno: Compra cancelada

-   **TRADER**: Lançamentos USER + investimentos

    -   Aportes mensais
    -   Dividendos recebidos
    -   Compra/venda de ações

-   **USER_TRADER**: Mix completo de finanças + investimentos

-   **ADMIN/FULL**: Lançamentos básicos para testes

#### 📊 Características dos Dados

✅ **Parcelamentos**: Celular em 12x, supermercado em 3x
✅ **Recorrências**: Salário, aluguel, Netflix, Spotify, academia
✅ **Status variados**: PAGO, PENDENTE
✅ **Faturas de cartão**: Lançamentos vinculados às faturas corretas
✅ **Observações**: ~30% dos lançamentos com notas explicativas
✅ **Categorias diversas**: Alimentação, transporte, moradia, saúde, lazer
✅ **Datas variadas**: Mês atual com vencimentos futuros
✅ **Estornos**: Exemplo de estorno de compra cancelada

## 🚀 Como Usar

### Executar todos os seeders

```bash
# Limpar banco e executar migrations + seeders
php artisan migrate:fresh --seed

# Ou apenas executar os seeders (mantém dados existentes)
php artisan db:seed
```

### Executar seeder específico

```bash
# Apenas roles
php artisan db:seed --class=RolesSeeder

# Apenas dados de demonstração
php artisan db:seed --class=DemoDataSeeder
```

## 🧪 Testes Recomendados

Após executar os seeders, teste:

### 1. Autenticação e Roles

-   [ ] Login com cada usuário
-   [ ] Verificar permissões específicas de cada role
-   [ ] Testar acesso ao painel admin (apenas ADMIN/FULL)

### 2. Lançamentos

-   [ ] Visualizar lançamentos de cada usuário
-   [ ] Verificar parcelamentos (iPhone 12x)
-   [ ] Conferir recorrências (salário, aluguel)
-   [ ] Validar estornos aparecem corretamente
-   [ ] Verificar observações nos lançamentos

### 3. Contas e Cartões

-   [ ] Verificar saldos das contas
-   [ ] Conferir limites dos cartões
-   [ ] Validar faturas dos cartões
-   [ ] Testar datas de fechamento/vencimento

### 4. Investimentos (TRADER/USER_TRADER)

-   [ ] Verificar conta de investimentos
-   [ ] Conferir lançamentos de aportes
-   [ ] Validar dividendos recebidos
-   [ ] Testar compra de ações

### 5. Admin Panel

-   [ ] Listar todos os usuários
-   [ ] Visualizar estatísticas do sistema
-   [ ] Atribuir/remover roles
-   [ ] Editar dados de usuários
-   [ ] Desativar/ativar usuários

### 6. Notificações

-   [ ] Configurar notificações de vencimento
-   [ ] Testar notificação de limite cartão
-   [ ] Verificar notificação de estorno
-   [ ] Testar desvio de orçamento

## 📝 Notas Importantes

⚠️ **ATENÇÃO**: O comando `migrate:fresh --seed` irá **APAGAR TODOS OS DADOS** do banco!

💡 **Dica**: Use em ambiente de desenvolvimento/testes apenas

🔒 **Segurança**: Em produção, nunca use seeders com senhas fracas

## 🎯 Próximos Passos

Após validar o sistema com os seeders:

1. ✅ Testar todas as funcionalidades
2. ✅ Corrigir bugs encontrados
3. ✅ Validar permissões e autorização
4. ✅ Testar performance com dados reais
5. ✅ Preparar para v1.0

## 📧 Credenciais de Acesso

Todos os usuários usam a mesma senha: **senha123**

```
USER:        joao@teste.com
TRADER:      maria@teste.com
USER_TRADER: pedro@teste.com
ADMIN:       ana@teste.com
FULL:        admin@teste.com
```
