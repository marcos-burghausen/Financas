# 🎯 Plano de Implementação - MVP 1.0.0 + Quick Wins

## 📋 Status Geral

- **Versão Atual**: 0.0.5
- **Versão Alvo**: 1.0.0
- **Prazo**: Q4 2025
- **Prioridade**: MVP primeiro, depois Quick Wins

---

## ⚡ FASE 1: MVP - Sistema de Notificações por E-mail

### 🎯 Objetivo

Implementar sistema completo de notificações por e-mail para alertar usuários sobre:

- Vencimento de contas (despesas pendentes)
- Limite de cartão de crédito próximo ao limite
- Estornos e reembolsos
- Desvios do orçamento

### 📦 Dependências

- Laravel Mail (já incluído)
- Laravel Queue (configurar)
- Mailhog/Mailtrap (desenvolvimento)
- SMTP configurado (produção)

### 🛠️ Tarefas

#### Backend

- [ ] **1.1 Configurar Sistema de Filas**

  - Arquivos: `config/queue.php`, `.env`
  - Configurar driver (database, redis)
  - Criar tabela `jobs` via migration
  - Testar queue worker

- [ ] **1.2 Criar Notificações (Notification Classes)**

  - `app/Notifications/VencimentoContaNotification.php`
  - `app/Notifications/LimiteCartaoNotification.php`
  - `app/Notifications/EstornoNotification.php`
  - `app/Notifications/DesvioOrcamentoNotification.php`

- [ ] **1.3 Criar Templates de E-mail (Mailables)**

  - `resources/views/emails/vencimento-conta.blade.php`
  - `resources/views/emails/limite-cartao.blade.php`
  - `resources/views/emails/estorno.blade.php`
  - `resources/views/emails/desvio-orcamento.blade.php`
  - Layout base: `resources/views/emails/layout.blade.php`

- [ ] **1.4 Criar Jobs Agendados (Scheduled Jobs)**

  - `app/Console/Commands/EnviarNotificacaoVencimento.php`
  - `app/Console/Commands/VerificarLimiteCartao.php`
  - Configurar em `app/Console/Kernel.php`

- [ ] **1.5 Criar Configurações de Notificação**

  - Migration: `user_notification_settings` table
  - Model: `UserNotificationSettings`
  - Campos: email_vencimento, email_cartao, dias_antecedencia, etc.

- [ ] **1.6 API Endpoints**
  - `GET /api/notification-settings` - Buscar configurações
  - `PUT /api/notification-settings` - Atualizar configurações
  - `POST /api/test-notification` - Testar envio

#### Frontend

- [ ] **1.7 Tela de Configurações de Notificações**

  - Componente: `NotificationSettings.vue`
  - Checkboxes para ativar/desativar notificações
  - Input para dias de antecedência
  - Botão "Enviar E-mail de Teste"

- [ ] **1.8 Store Pinia**
  - `stores/notifications.ts`
  - Actions: fetchSettings, updateSettings, testNotification

#### Testes

- [ ] **1.9 Testes Unitários**
  - Test: NotificacaoVencimentoTest.php
  - Test: JobEnvioEmailTest.php

#### Documentação

- [ ] **1.10 Documentação**
  - Atualizar `docs/api/documentacao_api.md`
  - Criar `docs/guias/configurar_notificacoes.md`

### ⏱️ Estimativa de Tempo

- Backend: 3-4 dias
- Frontend: 1-2 dias
- Testes: 1 dia
- **Total: 5-7 dias**

---

## 🚀 FASE 2: Quick Win 1 - Sistema de Perfis de Usuário

### 🎯 Objetivo

Implementar níveis de acesso baseados em perfis:

- **USER**: Acesso básico (gestão financeira)
- **TRADER**: Acesso ao módulo de investimentos (futuro)
- **USER_TRADER**: Acesso completo
- **ADMIN**: Administração do sistema
- **FULL**: Acesso total (super admin)

### 📦 Dependências

- Laravel Policies
- Laravel Gates

### 🛠️ Tarefas

#### Backend

- [ ] **2.1 Criar Enum de Perfis**

  - Arquivo: `app/Enums/UserRole.php`
  - Valores: USER, TRADER, USER_TRADER, ADMIN, FULL

- [ ] **2.2 Migration - Adicionar campo role**

  - Migration: `add_role_to_users_table`
  - Adicionar coluna `role` (enum) na tabela `users`
  - Valor padrão: USER

- [ ] **2.3 Atualizar Model User**

  - Adicionar cast para UserRole enum
  - Métodos helper: `isAdmin()`, `isTrader()`, etc.

- [ ] **2.4 Criar Policies**

  - `app/Policies/LancamentoPolicy.php`
  - `app/Policies/UserPolicy.php`
  - Registrar em `AuthServiceProvider`

- [ ] **2.5 Criar Middleware de Role**

  - `app/Http/Middleware/CheckRole.php`
  - Registrar em `Kernel.php`
  - Uso: `middleware('role:admin')`

- [ ] **2.6 API Endpoints (Admin)**
  - `GET /api/admin/users` - Listar usuários (ADMIN only)
  - `PUT /api/admin/users/{id}/role` - Alterar perfil
  - `GET /api/me/permissions` - Permissões do usuário atual

#### Frontend

- [ ] **2.7 Componente de Badge de Perfil**

  - Componente: `UserRoleBadge.vue`
  - Mostrar badge com cor por perfil

- [ ] **2.8 Tela Admin - Gerenciar Usuários**

  - View: `AdminUsersView.vue`
  - Lista de usuários com filtros
  - Modal para alterar perfil

- [ ] **2.9 Guards de Rota**
  - Middleware no Vue Router
  - Redirecionar se não tem permissão

#### Testes

- [ ] **2.10 Testes de Permissões**
  - Test: UserRoleTest.php
  - Test: PolicyTest.php

### ⏱️ Estimativa de Tempo

- Backend: 2-3 dias
- Frontend: 2 dias
- Testes: 1 dia
- **Total: 5-6 dias**

---

## 📎 FASE 3: Quick Win 2 - Anexos em Lançamentos

### 🎯 Objetivo

Permitir anexar comprovantes (PDF, imagens) aos lançamentos financeiros.

### 📦 Dependências

- Laravel Storage
- Intervention Image (para thumbnails)

### 🛠️ Tarefas

#### Backend

- [ ] **3.1 Migration - Tabela de Anexos**

  - Migration: `create_lancamento_attachments_table`
  - Campos: lancamento_id, filename, filepath, mime_type, size, uploaded_at

- [ ] **3.2 Model LancamentoAttachment**

  - Relacionamento com Lancamento
  - Accessor para URL pública

- [ ] **3.3 Configurar Storage**

  - Disk 'local' para desenvolvimento
  - Disk 's3' para produção (futuro)
  - Link simbólico: `php artisan storage:link`

- [ ] **3.4 API Endpoints**

  - `POST /api/lancamentos/{id}/attachments` - Upload
  - `GET /api/lancamentos/{id}/attachments` - Listar
  - `DELETE /api/attachments/{id}` - Deletar
  - `GET /api/attachments/{id}/download` - Download

- [ ] **3.5 Validações**
  - Max size: 5MB
  - Tipos permitidos: PDF, JPG, PNG, JPEG
  - Limitar 5 anexos por lançamento

#### Frontend

- [ ] **3.6 Componente de Upload**

  - Componente: `AttachmentUploader.vue`
  - Drag & drop
  - Preview de imagens
  - Lista de anexos com botão deletar

- [ ] **3.7 Integrar no Formulário de Lançamento**

  - Adicionar componente no form
  - Upload ao salvar lançamento

- [ ] **3.8 Visualizador de Anexos**
  - Componente: `AttachmentViewer.vue`
  - Modal para visualizar PDF/imagem
  - Botão de download

#### Testes

- [ ] **3.9 Testes de Upload**
  - Test: AttachmentUploadTest.php
  - Test: AttachmentValidationTest.php

### ⏱️ Estimativa de Tempo

- Backend: 2 dias
- Frontend: 2-3 dias
- Testes: 1 dia
- **Total: 5-6 dias**

---

## 📝 FASE 4: Quick Win 3 - Notas e Observações

### 🎯 Objetivo

Adicionar campo de notas/observações em lançamentos para contexto adicional.

### 🛠️ Tarefas

#### Backend

- [ ] **4.1 Migration - Adicionar campo notes**

  - Migration: `add_notes_to_lancamentos_table`
  - Adicionar coluna `notes` (text, nullable)

- [ ] **4.2 Atualizar Model Lancamento**

  - Adicionar `notes` no fillable
  - Cast para string

- [ ] **4.3 Atualizar Validações**
  - Campo opcional
  - Max length: 1000 caracteres

#### Frontend

- [ ] **4.4 Adicionar Campo no Formulário**

  - Textarea no form de lançamento
  - Contador de caracteres
  - Placeholder sugestivo

- [ ] **4.5 Exibir Notas**
  - Mostrar notas na lista de lançamentos (truncado)
  - Expandir ao clicar
  - Ícone de nota quando tem conteúdo

#### Testes

- [ ] **4.6 Testes Simples**
  - Validar salvamento
  - Validar max length

### ⏱️ Estimativa de Tempo

- Backend: 0.5 dia
- Frontend: 1 dia
- Testes: 0.5 dia
- **Total: 2 dias**

---

## 📊 FASE 5: Quick Win 4 - Relatórios Básicos

### 🎯 Objetivo

Criar relatórios reutilizando queries existentes:

- Relatório mensal (receitas vs despesas)
- Relatório por categoria
- Relatório de evolução (3, 6, 12 meses)
- Exportar para PDF/Excel

### 📦 Dependências

- Laravel Excel (maatwebsite/excel)
- DomPDF ou Snappy

### 🛠️ Tarefas

#### Backend

- [ ] **5.1 Instalar Dependências**

  - `composer require maatwebsite/excel`
  - `composer require barryvdh/laravel-dompdf`

- [ ] **5.2 Criar Controllers**

  - `app/Http/Controllers/RelatorioController.php`
  - Métodos: mensal, categoria, evolucao

- [ ] **5.3 Criar Exports (Excel)**

  - `app/Exports/RelatorioMensalExport.php`
  - `app/Exports/RelatorioCategoriaExport.php`

- [ ] **5.4 Criar Views PDF**

  - `resources/views/relatorios/mensal.blade.php`
  - `resources/views/relatorios/categoria.blade.php`
  - CSS para impressão

- [ ] **5.5 API Endpoints**
  - `GET /api/relatorios/mensal?mes=2025-10` - JSON
  - `GET /api/relatorios/mensal/pdf?mes=2025-10` - PDF
  - `GET /api/relatorios/mensal/excel?mes=2025-10` - Excel
  - `GET /api/relatorios/categoria?mes=2025-10`
  - `GET /api/relatorios/evolucao?meses=6`

#### Frontend

- [ ] **5.6 Tela de Relatórios**

  - View: `RelatoriosView.vue`
  - Seletor de período
  - Seletor de tipo de relatório
  - Botões: Visualizar, PDF, Excel

- [ ] **5.7 Componentes de Visualização**

  - `RelatorioMensal.vue` - Tabela + gráfico
  - `RelatorioCategoria.vue` - Pizza + tabela
  - `RelatorioEvolucao.vue` - Linha temporal

- [ ] **5.8 Store Pinia**
  - `stores/relatorios.ts`
  - Actions: fetchRelatorio, downloadPDF, downloadExcel

#### Testes

- [ ] **5.9 Testes de Relatórios**
  - Test: RelatorioControllerTest.php
  - Test: ExportTest.php

### ⏱️ Estimativa de Tempo

- Backend: 3 dias
- Frontend: 3-4 dias
- Testes: 1 dia
- **Total: 7-8 dias**

---

## 📅 CRONOGRAMA SUGERIDO

| Fase | Funcionalidade          | Duração  | Início   | Fim      |
| ---- | ----------------------- | -------- | -------- | -------- |
| 1    | Sistema de Notificações | 5-7 dias | Semana 1 | Semana 2 |
| 2    | Perfis de Usuário       | 5-6 dias | Semana 2 | Semana 3 |
| 3    | Anexos em Lançamentos   | 5-6 dias | Semana 3 | Semana 4 |
| 4    | Notas e Observações     | 2 dias   | Semana 4 | Semana 4 |
| 5    | Relatórios Básicos      | 7-8 dias | Semana 5 | Semana 6 |

**Total Estimado: 24-29 dias (aprox. 5-6 semanas)**

---

## ✅ CHECKLIST DE CONCLUSÃO

### MVP 1.0.0

- [ ] Sistema de Notificações por E-mail funcionando
- [ ] Testes unitários passando
- [ ] Documentação atualizada
- [ ] Deploy em ambiente de teste

### Quick Wins

- [ ] Perfis de usuário implementados
- [ ] Anexos funcionando
- [ ] Notas salvando
- [ ] Relatórios gerando PDF/Excel

### Qualidade

- [ ] Cobertura de testes > 70%
- [ ] Sem erros no console
- [ ] Performance otimizada
- [ ] Documentação completa

---

## 🔄 PRÓXIMOS PASSOS

1. **Revisar e aprovar este plano**
2. **Configurar ambiente (filas, storage, etc)**
3. **Criar branches para cada funcionalidade**
4. **Implementar fase por fase**
5. **Code review + testes**
6. **Deploy e monitoramento**

---

**Atualizado em**: 15 de Outubro de 2025  
**Responsável**: Marcos Burghausen  
**Status**: ✅ Plano Aprovado - Aguardando Implementação
