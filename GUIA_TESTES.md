# 🧪 Guia Rápido de Testes - Sistema Financeiro v1.0

## 🚀 Início Rápido

### 1. Preparar Ambiente

```bash
# Iniciar containers
docker compose up -d

# Resetar e popular banco de dados
docker compose exec php php artisan migrate:fresh --seed --force
```

### 2. Acessar Sistema

- **Frontend**: http://localhost:4081
- **Backend API**: http://localhost:4080/api
- **phpMyAdmin**: http://localhost:4033

## 👥 Credenciais de Teste

| Usuário      | Email           | Senha    | Role        | Tem Investimentos? |
| ------------ | --------------- | -------- | ----------- | ------------------ |
| João Silva   | joao@teste.com  | senha123 | USER        | ❌                 |
| Maria Santos | maria@teste.com | senha123 | TRADER      | ✅                 |
| Pedro Costa  | pedro@teste.com | senha123 | USER_TRADER | ✅                 |
| Ana Oliveira | ana@teste.com   | senha123 | ADMIN       | ❌                 |
| Carlos Admin | admin@teste.com | senha123 | FULL        | ❌                 |

## ✅ Checklist de Testes

### 1. Autenticação (5 min)

- [ ] Login com João (USER)
- [ ] Login com Maria (TRADER)
- [ ] Login com Pedro (USER_TRADER)
- [ ] Login com Ana (ADMIN)
- [ ] Login com Carlos (FULL)
- [ ] Verificar logout funciona
- [ ] Testar "lembrar-me"

### 2. Lançamentos - Visualização (10 min)

#### Como João (USER - 13 lançamentos)

- [ ] Ver lista de despesas
- [ ] Ver lista de receitas
- [ ] Verificar filtros funcionam
- [ ] Ordenar por data/valor
- [ ] Ver **observações** nos lançamentos (ex: "Salário depositado via PIX")
- [ ] Verificar cores e badges de status
- [ ] Conferir valores em R$

**Lançamentos Esperados:**

- ✅ Salário: R$ 5.000,00 (recorrente mensal)
- ✅ Freelance: R$ 1.500,00 (com observação)
- ✅ Aluguel: R$ 1.200,00 (recorrente)
- ✅ Supermercado: R$ 450,00 (cartão, com observação)
- ✅ Restaurante: R$ 150,00 (cartão, com observação "Jantar de aniversário")
- ✅ Academia: R$ 89,90 (recorrente)
- ✅ Netflix/Spotify (cartão, recorrente)

### 3. Lançamentos - CRUD (15 min)

#### Criar Novo

- [ ] Criar receita simples
- [ ] Criar despesa com **observação** (testar limite 1000 chars)
- [ ] Criar lançamento de cartão
- [ ] Criar lançamento recorrente mensal
- [ ] Verificar validações funcionam

#### Editar

- [ ] Editar lançamento existente
- [ ] Adicionar observação a lançamento sem observação
- [ ] Editar observação existente
- [ ] Mudar status PENDENTE → EFETIVADA
- [ ] Salvar e verificar atualização

#### Deletar

- [ ] Deletar lançamento
- [ ] Confirmar modal de confirmação
- [ ] Verificar remoção da lista

### 4. Contas (10 min)

#### Como João (4 contas)

- [ ] Ver lista de contas
- [ ] Verificar saldos:
  - Nubank: R$ 2.500,00
  - Poupança BB: R$ 5.000,00
  - Cartões: limite disponível
- [ ] Criar nova conta
- [ ] Editar conta existente
- [ ] Verificar cores personalizadas

#### Como Maria (5 contas - tem Investimentos)

- [ ] Ver conta "Corretora XP" (R$ 50.000,00)
- [ ] Verificar tipo "Investimento"

### 5. Cartões de Crédito (10 min)

#### Como qualquer usuário

- [ ] Ver cartões listados
- [ ] Verificar limites:
  - Nubank Visa: R$ 5.000,00
  - Inter Mastercard: R$ 3.000,00
- [ ] Ver dia de fechamento
- [ ] Ver dia de vencimento
- [ ] Criar lançamento no cartão
- [ ] Verificar limite disponível atualiza

### 6. Observações nos Lançamentos (5 min)

**Feature Nova! Testar extensivamente:**

- [ ] Visualizar observações na lista (ícone + texto truncado)
- [ ] Expandir observações longas
- [ ] Criar lançamento COM observação
- [ ] Criar lançamento SEM observação (opcional)
- [ ] Editar e adicionar observação
- [ ] Testar limite de 1000 caracteres
- [ ] Verificar observações no detalhe do lançamento

**Exemplos para testar:**

```
Observação curta: "Pagamento via PIX"
Observação média: "Compra de supermercado mensal incluindo arroz, feijão, carnes, legumes e produtos de limpeza"
Observação longa: Digitar texto de 950+ caracteres e verificar validação
```

### 7. Painel Administrativo (15 min)

#### Como Ana ou Carlos (ADMIN/FULL)

- [ ] Acessar menu "Admin" (deve aparecer)
- [ ] Ver estatísticas:
  - Total de usuários: 5
  - Usuários ativos: 5
  - Total lançamentos: 23
- [ ] **Tab Usuários:**
  - [ ] Ver lista de 5 usuários
  - [ ] Buscar usuário
  - [ ] Editar dados de usuário
  - [ ] Ativar/desativar usuário
  - [ ] Atribuir roles
  - [ ] Remover roles
  - [ ] Verificar não pode deletar a si mesmo
- [ ] **Tab Roles:**
  - [ ] Ver 5 roles (cards coloridos)
  - [ ] Ver permissões de cada role
  - [ ] Badges com contagem de usuários
- [ ] **Tab Estatísticas:**
  - [ ] Gráficos carregam
  - [ ] Dados corretos exibidos
- [ ] **Tab Sistema:**
  - [ ] Ver informações do sistema
  - [ ] Versão exibida

#### Como João (USER - não tem acesso)

- [ ] Verificar menu "Admin" NÃO aparece
- [ ] Tentar acessar /admin via URL → redirecionado

### 8. Notificações (10 min)

#### Como qualquer usuário

- [ ] Menu lateral → **"Notificações"** (ícone 🔔)
- [ ] Ver estatísticas de notificações
- [ ] **Notificação de Vencimento:**
  - [ ] Ativar/desativar
  - [ ] Ajustar dias de antecedência (1-30)
  - [ ] Testar envio
  - [ ] Ver feedback sucesso
- [ ] **Limite de Cartão:**
  - [ ] Ativar/desativar
  - [ ] Ajustar percentual (50-100%)
  - [ ] Testar envio
- [ ] **Estorno:**
  - [ ] Ativar/desativar
  - [ ] Testar envio
- [ ] **Desvio de Orçamento:**
  - [ ] Ativar/desativar
  - [ ] Testar envio
- [ ] Verificar auto-save funciona (aguardar 1s)
- [ ] Recarregar página e ver configurações salvas

### 8.1 Perfil do Usuário (8 min) 👤

#### Como qualquer usuário

- [ ] Menu lateral → **"Perfil"** (ícone de pessoa)
- [ ] Ver dados pessoais (nome, email, data de criação)
- [ ] Ver badges de roles/permissões
- [ ] Ver estatísticas (contas, receitas, despesas)
- [ ] **Editar Perfil:**
  - [ ] Clicar em "Editar"
  - [ ] Alterar nome
  - [ ] Alterar email
  - [ ] Clicar em "Salvar"
  - [ ] Ver snackbar de sucesso
  - [ ] Recarregar e confirmar mudanças
- [ ] **Cancelar Edição:**
  - [ ] Clicar em "Editar"
  - [ ] Alterar campos
  - [ ] Clicar em "Cancelar"
  - [ ] Verificar valores voltam ao original
- [ ] **Alterar Senha:**
  - [ ] Preencher senha atual: `senha123`
  - [ ] Nova senha: `novaSenha123`
  - [ ] Confirmar nova senha
  - [ ] Clicar em "Alterar Senha"
  - [ ] Ver snackbar de sucesso
  - [ ] Fazer logout
  - [ ] Login com nova senha
  - [ ] Voltar senha para `senha123` (facilitar próximos testes)
- [ ] **Testar Validações:**
  - [ ] Email inválido → Ver erro
  - [ ] Senha < 8 caracteres → Ver erro
  - [ ] Senhas não coincidem → Ver erro
  - [ ] Senha atual incorreta → Ver erro backend

### 9. Permissões e Autorização (10 min)

#### Testar Restrições

````

- [ ] **USER** não vê funcionalidades de TRADER
- [ ] **USER** não acessa painel admin
- [ ] **TRADER** vê recursos de investimentos
- [ ] **TRADER** não acessa painel admin
- [ ] **USER_TRADER** vê TUDO exceto admin
- [ ] **ADMIN** acessa painel admin
- [ ] **FULL** tem acesso irrestrito

#### Testar no Admin Panel

- [ ] Admin consegue mudar role de USER para TRADER
- [ ] Usuário ao relogar tem novas permissões
- [ ] Admin remove role → usuário perde acesso

### 10. Investimentos (TRADER/USER_TRADER) (10 min)

#### Como Maria (TRADER)

- [ ] Ver conta "Corretora XP"
- [ ] Ver lançamentos de investimentos:
  - Aporte Mensal - Tesouro Direto
  - Dividendos ITSA4
- [ ] Criar novo lançamento de investimento
- [ ] Categorias de investimento disponíveis

#### Como Pedro (USER_TRADER)

- [ ] Mesmas funcionalidades de Maria
- [ ] PLUS todas as de USER
- [ ] Ver lançamento "Aporte CDB"

### 11. Responsividade (5 min)

- [ ] Testar em mobile (DevTools)
- [ ] Menu lateral funciona
- [ ] Tabelas responsivas
- [ ] Formulários usáveis
- [ ] Cards adaptam

### 12. Performance (5 min)

- [ ] Listas carregam rápido
- [ ] Filtros instantâneos
- [ ] Sem lag ao digitar
- [ ] Transições suaves
- [ ] Auto-save não trava UI

## 🐛 Bugs Conhecidos

> Nenhum bug conhecido até o momento! 🎉

## 📝 Notas de Teste

### Campo Observações

- ✅ Limite: 1000 caracteres
- ✅ Opcional (pode ser nulo)
- ✅ Exibido na lista com ícone
- ✅ Truncado com "..." após 2 linhas
- ✅ Tooltip/expand para ver completo

### Valores Monetários

- ✅ Armazenados em centavos no banco
- ✅ Exibidos em R$ no frontend
- ✅ Formatação correta (R$ 1.234,56)

### Datas

- ✅ Timezone: America/Sao_Paulo
- ✅ Formato brasileiro: DD/MM/YYYY
- ✅ data_vencimento para pagamentos futuros
- ✅ data_efetivacao quando pago

### Status

- ✅ EFETIVADA (verde) - já pago/recebido
- ✅ PENDENTE (amarelo) - a pagar/receber

---

## 🔔 Sistema de Notificações (15 min)

### 1. Acessar Configurações de Notificações

- [ ] Login com qualquer usuário
- [ ] Menu lateral → **"Notificações"** (ícone de sino 🔔)
- [ ] OU Acessar diretamente: `http://localhost:4081/configuracoes/notificacoes`

### 2. Visualizar Estatísticas (10 min)

#### Cards de Resumo:

- [ ] **Total Enviadas**: Mostra quantidade total
- [ ] **Hoje**: Notificações enviadas hoje
- [ ] **Este Mês**: Notificações do mês atual
- [ ] **Última Notificação**: Data da última enviada

### 3. Configurar Alertas (5 min)

#### Testar cada configuração:

- [ ] **Vencimento de Contas**

  - [ ] Toggle Ativar/Desativar
  - [ ] Ajustar dias de antecedência (0-30)
  - [ ] Salvar alterações

- [ ] **Limite de Cartão**

  - [ ] Toggle Ativar/Desativar
  - [ ] Ajustar percentual de alerta (50-100%)
  - [ ] Salvar alterações

- [ ] **Estornos**

  - [ ] Toggle Ativar/Desativar
  - [ ] Salvar alterações

- [ ] **Desvio de Orçamento**
  - [ ] Toggle Ativar/Desativar
  - [ ] Salvar alterações

### 4. Testar Envio de E-mails (10 min) ⭐

#### Pré-requisitos:

```bash
# Verificar se o email está configurado no .env do backend
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # ou seu SMTP
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
````

#### Teste de Vencimento de Conta:

- [ ] Certificar que possui uma **despesa pendente**
- [ ] Clicar em "📧 Testar E-mail de Vencimento"
- [ ] Aguardar mensagem de sucesso
- [ ] Verificar inbox do e-mail cadastrado
- [ ] Confirmar que e-mail chegou com:
  - ✉️ Assunto: "Vencimento de Conta"
  - 📝 Descrição do lançamento
  - 💰 Valor da despesa
  - 📅 Data de vencimento
  - ⏰ Dias restantes

#### Teste de Limite de Cartão:

- [ ] Certificar que possui um **cartão cadastrado**
- [ ] Clicar em "💳 Testar E-mail de Limite"
- [ ] Aguardar mensagem de sucesso
- [ ] Verificar inbox do e-mail cadastrado
- [ ] Confirmar que e-mail chegou com:
  - ✉️ Assunto: "Alerta de Limite de Cartão"
  - 💳 Nome do cartão
  - 💰 Valor utilizado
  - 📊 Percentual do limite
  - ⚠️ Recomendações

#### Teste de Estorno:

- [ ] Certificar que possui um **estorno registrado**
- [ ] Clicar em "↩️ Testar E-mail de Estorno"
- [ ] Aguardar mensagem de sucesso
- [ ] Verificar inbox do e-mail cadastrado
- [ ] Confirmar que e-mail chegou com:
  - ✉️ Assunto: "Estorno Registrado"
  - 📝 Descrição do estorno
  - 💰 Valor estornado
  - 🔗 Lançamento original

### 5. Verificar Mensagens de Feedback (5 min)

#### Cenários de Sucesso:

- [ ] Configuração salva: "✅ Configurações atualizadas com sucesso!"
- [ ] E-mail enviado: "✅ E-mail de teste enviado com sucesso!"
- [ ] Detalhes do envio exibidos

#### Cenários de Erro:

- [ ] Sem despesas pendentes: Mensagem clara + dica
- [ ] Sem cartões: Mensagem clara + dica
- [ ] Sem estornos: Mensagem clara + dica
- [ ] Erro de envio: Mensagem de erro com detalhes

### 6. Testar Responsividade (3 min)

- [ ] Desktop: Layout 4 colunas (estatísticas)
- [ ] Tablet: Layout 2 colunas
- [ ] Mobile: Layout 1 coluna empilhada
- [ ] Toggles funcionam em todos os tamanhos
- [ ] Botões acessíveis em mobile

### 7. Integração com Sistema (5 min)

#### Verificar se notificações são enviadas automaticamente:

- [ ] **Criar despesa próxima ao vencimento**

  - Data: 3 dias no futuro
  - Aguardar envio automático (cronjob)

- [ ] **Usar cartão próximo ao limite**

  - Criar lançamentos até atingir 80% do limite
  - Verificar se alerta é disparado

- [ ] **Registrar estorno**
  - Criar estorno de uma despesa
  - Verificar se e-mail é enviado

### 8. Checklist de Validações

- [ ] Dias de antecedência: Não aceita < 0 ou > 30
- [ ] Percentual cartão: Não aceita < 50% ou > 100%
- [ ] Toggles persistem após reload
- [ ] Horário preferido (se implementado) aceita formato HH:mm
- [ ] Configurações carregam do backend
- [ ] Salvar realmente persiste no banco

---

## 🎯 Critérios de Aceitação v1.0

Para considerar pronto para produção:

- [ ] Todos os testes acima passam
- [ ] Sem erros no console do navegador
- [ ] Sem erros nos logs do Laravel
- [ ] Performance aceitável (< 1s para carregar listas)
- [ ] UI responsiva e polida
- [ ] Validações funcionam corretamente
- [ ] Autorização funciona (sem bypass possível)
- [ ] Dados persistem corretamente
- [ ] Observações exibidas corretamente em todos os lugares

## 🚀 Após Testes

1. Documentar bugs encontrados
2. Criar issues no GitHub
3. Priorizar correções críticas
4. Validar novamente após fixes
5. Preparar para deploy v1.0

## 📞 Suporte

Em caso de problemas:

1. Verificar logs do Docker: `docker compose logs -f php`
2. Verificar logs do Laravel: `backend/storage/logs/laravel.log`
3. Limpar cache: `docker compose exec php php artisan cache:clear`
4. Resetar banco: `docker compose exec php php artisan migrate:fresh --seed`

---

**Boa sorte nos testes! 🎉**
