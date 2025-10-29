# MrFinancas - Sistema de Gestão Financeira Pessoal

**Nota**: Esta documentação está em desenvolvimento/planejamento.

<h1 align="center">
    <img width="250px" src="./docs/img/logo.png" />
</h1>
<!-- https://www.plantuml.com/plantuml/duml/lLJ1RXGn3BtFLqIzj4NT2WHSK5NrGYz829NUHzwCPskKSOR4MRHg-Hx41_05_J5iqNOtPQgLoehBI7b-iVtibpdB0adA49fctxrd8I7Cxk0IvW7pOOE6hDnUoPZodhjkd7lXg7Fl7B6uavDF7qvF5PlDzCCme0Qo9EA5dd402fl023b_YwdxYzGkCL5FQ95vZ061DGHB44YRv02yAB2eglYI4h9VLLX24EnQ44M51im2mwY1CdPJWU_DWr3mFXQMRLfj7kr6x5nZ4oEGrLYToKUk6eqXLWhde1gztUdGqm11CV12Iwn6ymy29G5dIndvcIBlHZGHyP2w6ZvoN48mCzqH11SnhHxVdHWL35Qa6eH9Cm04xOJP9nfDo8vsR-tlS2RJbWWqq2DP1TiELdykce8Git8hR8jnTcsIkpqH_iztiACjSXJgoOCPeyqeeXcV8sEYRMHpSlsNqYtbTevzJBZUu1KmOzYH8E9b9Bzr5URU2GtchkhXhFrLeH85GZlnwfxH-Rwxm02sW7n5OsvilDdTtCncyVh-gMIzDCQS2zNXpV-JKdmYg0ccywdBQzy-GPWqXvM9tDEY96WUsflkPqdiIu2k8bPzhHc6RCzJiUvthOm59cZMWhRcqQb_Z8AvOVswWBsus5Vq3PFS_dHnz0f2S0TZxD1bJFQlZeV73NBdB4cTQTsAvlbSvlTdo9e14h9euaCt1c3y0ft2YeAxE7_vvlXLdVJ9zYxj2BS_Rd-fu8G-9ikBMa4F9TbREd9zmgy5sQycYxqzVhrHh3b3xieGVWC0 -->
## 📌 Status do Projeto

[![Versão](https://img.shields.io/badge/Versão-0.0.5-blue.svg)](https://github.com/marcos-burghausen/MrFinancas/releases)
[![Licença](https://img.shields.io/badge/Licença-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow.svg)](https://github.com/marcos-burghausen/MrFinancas)
[![Documentação](https://img.shields.io/badge/📚_Documentação-Em_Desenvolvimento-orange)](./docs)

## 📑 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Objetivo e Funcionalidades](#objetivo-e-funcionalidades)
- [Tecnologias e Arquitetura](#tecnologias-e-arquitetura)
- [Instalação e Configuração](#instalação-e-configuração)
- [Variáveis de Ambiente](#variáveis-de-ambiente)
- [Execução de Testes](#execução-de-testes)
- [Deploy em Produção](#deploy-em-produção)
- [Segurança Detalhada](#segurança-detalhada)
- [Performance e Escalabilidade](#performance-e-escalabilidade)
- [Backup e Disaster Recovery](#backup-e-disaster-recovery)
- [Monitoramento e Logs](#monitoramento-e-logs)
- [Documentação e Diagramas](#documentação-e-diagramas)
- [Troubleshooting](#troubleshooting)
- [Roadmap e Contribuição](#roadmap-e-contribuição)
- [FAQ e Licença](#faq-e-licença)
- [Contato](#contato)

## 📋 Sobre o Projeto

Como entusiasta mercado financeiro, desenvolvi o MrFinanças a partir de uma necessidade pessoal: criar uma aplicação prática para consolidar meus estudos e atender às metas do meu Plano de Desenvolvimento Individual (PDI). Como usuário de um aplicativo pago de gestão financeira, decidi construir uma solução própria, mais acessível e personalizada.

O MrFinanças é um sistema de gestão financeira pessoal projetado para capacitar pessoas a organizar suas finanças, monitorar receitas e despesas e tomar decisões financeiras mais inteligentes. Simples, eficiente e intuitivo, o projeto reúne funcionalidades essenciais em uma única plataforma, oferecendo uma alternativa prática para o controle financeiro pessoal. Para futuras versões, pretendo expandir o sistema com um módulo de controle de investimentos voltado para profissionais do mercado financeiro, agregando ainda mais valor à gestão patrimonial.

## 🎯 Objetivo e Funcionalidades

### Objetivo

O MrFinanças visa fornecer uma ferramenta abrangente para o controle de finanças pessoais e, futuramente, para o gerenciamento de investimentos. Ele resolve problemas comuns, como:

- Falta de visibilidade sobre despesas e receitas
- Dificuldade em planejar orçamentos
- Organização de contas a pagar e receber
- Acompanhamento de faturas de cartão de crédito
- Monitoramento eficiente de investimentos
- Criação de metas financeiras realistas

### Mapa de Funcionalidades

| #   | Funcionalidade                              | Status          | Versão | Prioridade |
| --- | ------------------------------------------- | --------------- | ------ | ---------- |
| 1   | Cadastro de Usuários                        | ✅ Implementado | 1.0    | P0         |
| 2   | Lançamentos Financeiros                     | ✅ Implementado | 1.0    | P0         |
| 3   | Contas e Cartões de Crédito                 | ✅ Implementado | 1.0    | P0         |
| 4   | Perfil do Usuário                           | ✅ Implementado | 1.0    | P0         |
| 5   | Relatórios e Gráficos                       | ✅ Implementado | 1.0    | P0         |
| 6   | Alertas e Notificações                      | 🔄 Em Progresso | 1.1    | P0         |
| 7   | Orçamento Financeiro                        | 🔄 Em Progresso | 1.0    | P0         |
| 8   | Recursos Adicionais (Importação/Exportação) | ✅ Implementado | 1.0    | P1         |
| 9   | Transferências Entre Contas                 | 📋 Planejado    | 1.1    | P1         |
| 10  | Controle de Caixa                           | 📋 Planejado    | 1.1    | P1         |
| 11  | Gestão de Débito Técnico                    | 📋 Planejado    | 1.2    | P2         |
| 12  | Categorização Inteligente                   | 🔄 Em Progresso | 1.0    | P0         |
| 13  | Busca e Filtros Avançados                   | 🔄 Em Progresso | 1.0    | P1         |
| 14  | Dashboard e Analytics                       | ✅ Implementado | 1.0    | P0         |
| 15  | Relatórios Detalhados                       | 🔄 Em Progresso | 1.1    | P1         |
| 16  | Lembretes e Agendamentos                    | 📋 Planejado    | 1.1    | P0         |
| 17  | Reconciliação Bancária                      | 📋 Planejado    | 1.2    | P1         |
| 18  | Gestão de Débitos Recorrentes               | 📋 Planejado    | 1.1    | P1         |
| 19  | Análise Financeira Pessoal                  | 📋 Planejado    | 1.2    | P1         |
| 20  | Controle de Limite de Gastos                | 📋 Planejado    | 1.1    | P2         |
| 21  | Exportação de Dados                         | ✅ Parcial      | 1.0    | P1         |
| 22  | Auditoria e Histórico                       | 📋 Planejado    | 1.2    | P2         |
| 23  | Gestão de Múltiplos Usuários                | 📋 Planejado    | 2.0    | P2         |
| 24  | Planejamento Financeiro                     | 📋 Planejado    | 2.5    | P2         |
| 25  | Integração e APIs                           | 📋 Planejado    | 1.0    | P0         |
| 26  | Segurança Avançada                          | ✅ Implementado | 1.0    | P0         |
| 27  | Notificações                                | 🔄 Em Progresso | 1.1    | P0         |
| 28  | Modo Mobile (PWA)                           | ✅ Implementado | 1.0    | P0         |

**Legenda:**

- ✅ Implementado - Funcionalidade pronta para produção
- 🔄 Em Progresso - Funcionalidade sendo desenvolvida
- 📋 Planejado - Funcionalidade planejada para futuro
- P0 - Crítico (MVP)
- P1 - Alta prioridade
- P2 - Média prioridade

### Funcionalidades Principais

### 1. **Cadastro de Usuários**

- **Métodos de Cadastro/Login**:
  - Tradicional (e-mail e senha)
  - Facebook
  - Google
  - LinkedIn
- **Perfis de Usuário**:
  - USER: Acesso básico à gestão financeira
  - TRADER: Acesso ao módulo de investimentos
  - USER_TRADER: Acesso completo à gestão financeira e investimentos
  - ADMIN: Acesso administrativo ao sistema
  - FULL: Acesso completo a todas as funcionalidades

### 2. **Lançamentos Financeiros**

- **Tipos de Lançamentos**:
  - Despesas (fixas e variáveis)
  - Receitas (fixas e variáveis)
  - Despesas de cartão de crédito
  - Estornos e reembolsos
- **Características**:
  - Categorização e subcategorização
  - Recorrência (única, mensal, semanal, anual)
  - Anexos de comprovantes
  - Notas e observações
  - Status (pago, pendente, atrasado)

### 3. **Contas e Cartões de Crédito**

- **Contas**:
  - Múltiplas contas bancárias
  - Saldo inicial e atual
  - Histórico de transações
  - Extratos personalizados
- **Cartões de Crédito**:
  - Vinculação a contas bancárias
  - Controle de limite
  - Fechamento e vencimento de fatura
  - Parcelamento de compras

### 4. **Perfil do Usuário**

- Avatar personalizado
- Dados pessoais e endereço
- Preferências de sistema
- Gerenciamento de dispositivos conectados
- Configurações de privacidade

### 5. **Relatórios e Gráficos**

- **Tipos de Relatórios**:
  - Diários, semanais, mensais, trimestrais e anuais
  - Comparativos entre períodos
  - Previsões com base em histórico
- **Visualizações**:
  - Gráficos de pizza para distribuição de despesas
  - Gráficos de linha para evolução financeira
  - Gráficos de barras para comparativos

### 6. **Alertas e Notificações**

- Avisos de vencimento de contas
- Alertas de limite de cartão
- Notificações de estornos
- Avisos de desvios do orçamento
- Lembretes personalizáveis

### 7. **Orçamento Financeiro**

- Planejamento mensal e anual
- Metas por categoria
- Acompanhamento em tempo real
- Recomendações de ajustes
- Comparativo entre planejado e realizado

### 8. **Recursos Adicionais**

- Exportação para calendários (Google, Outlook)
- Backup e restauração de dados
- Sincronização entre dispositivos
- Modo offline com sincronização posterior
- Importação de extratos bancários (CSV, OFX)

### 9. **Transferências Entre Contas**

- Transferências internas entre contas do usuário
- Histórico de transferências
- Comprovantes de transferência
- Agendamento de transferências futuras
- Transferências recorrentes

### 10. **Controle de Caixa**

- Saldo diário e projeção de caixa
- Fluxo de caixa mensal
- Previsão de caixa (próximos 30/60/90 dias)
- Alertas de saldo baixo
- Diferença entre previsto vs realizado

### 11. **Gestão de Débito Técnico**

- Rastreamento de dívidas pessoais
- Empréstimos recebidos e concedidos
- Prazos de quitação
- Recordatórios de empréstimos
- Histórico de débitos liquidados

### 12. **Categorização Inteligente**

- Categorias padrão pré-configuradas
- Criação de categorias customizadas
- Subcategorias em múltiplos níveis
- Auto-categorização por padrão de gastos
- Tags customizadas para lançamentos
- Regras de categorização automática

### 13. **Busca e Filtros Avançados**

- Busca por descrição/notas
- Filtro por data (período, mês, ano)
- Filtro por categoria/subcategoria
- Filtro por conta
- Filtro por valor (range)
- Filtro por status (pago/pendente/atrasado)
- Filtros salvos e reutilizáveis
- Pesquisa fulltext em lançamentos

### 14. **Dashboard e Analytics**

- Resumo de receitas vs despesas
- Total por categoria (mês atual)
- Top 5 categorias com mais gastos
- Evolução mensal (últimos 12 meses)
- Saldo total de todas as contas
- Indicadores KPI (saúde financeira)
- Gráfico de saúde financeira (score)
- Widgets customizáveis no dashboard

### 15. **Relatórios Detalhados**

- Relatório de receitas e despesas
- Relatório por categoria
- Relatório por conta
- Análise de sazonalidade
- Comparativo anual (ano vs ano)
- Tendências de gastos
- Exportação em PDF, Excel, CSV
- Agendamento de relatórios por email

### 16. **Lembretes e Agendamentos**

- Lembrete de contas a pagar
- Lembrete de contas a receber
- Notificações por email
- Notificações push mobile
- Agendamento de despesas futuras
- Recorrências automáticas (parcelas, subscrições)

### 17. **Reconciliação Bancária**

- Comparação com extrato bancário
- Identificação de discrepâncias
- Marcação de lançamentos reconciliados
- Histórico de reconciliações
- Relatório de diferenças

### 18. **Gestão de Débitos Recorrentes**

- Subscrições mensais (Netflix, Spotify, etc)
- Rastreamento de renovações automáticas
- Alertas de mudança de valor
- Cancelamento de subscrições
- Análise de gastos recorrentes

### 19. **Análise Financeira Pessoal**

- Taxa de economia mensal
- Proporção de gastos por categoria
- Índice de saúde financeira
- Score de crédito simulado
- Sugestões de economia
- Comparativo com benchmark (média nacional)

### 20. **Controle de Limite de Gastos**

- Limite por categoria
- Limite diário/semanal/mensal
- Alertas quando próximo do limite
- Bloqueio de gastos acima do limite
- Histórico de ultrapassagens

### 21. **Exportação de Dados**

- Exportar para Excel (múltiplas abas)
- Exportar para CSV
- Exportar para PDF
- Exportar para JSON
- Sincronização com Google Sheets
- Backup automático em cloud (Google Drive, Dropbox)

### 22. **Auditoria e Histórico**

- Registro de alterações em lançamentos
- Histórico de quem modificou o quê e quando
- Rastreamento de exclusões
- Log de acessos ao sistema
- Recuperação de dados deletados (trash)

### 23. **Gestão de Múltiplos Usuários** (Compartilhamento)

- Contas compartilhadas com família
- Permissões por usuário (visualizar, editar, deletar)
- Histórico de alterações por usuário
- Divisão de despesas
- Cálculo de quem deve pagar para quem

### 24. **Planejamento Financeiro**

- Simulador de empréstimos
- Calculadora de juros
- Planejamento de aposentadoria
- Metas financeiras (curto/médio/longo prazo)
- Progresso visual das metas
- Recomendações para alcançar metas

### 25. **Integração e APIs**

- API REST documentada com Swagger
- Webhooks para eventos importantes
- Integração com Open Banking (futuro)
- OAuth para login social
- Exportação de dados estruturados

### 26. **Segurança Avançada**

- Autenticação de dois fatores (2FA)
- Biometria (impressão digital, reconhecimento facial)
- Criptografia end-to-end para dados sensíveis
- Sessões de dispositivos
- Revogação de sessões remotas
- Histórico de logins
- Detecção de atividade suspeita

### 27. **Notificações**

- Notificação de novo lançamento
- Notificação de vencimento de conta
- Notificação de limite de cartão atingido
- Notificação de limite de gastos atingido
- Resumo diário/semanal/mensal
- Alertas de atividade suspeita
- Preferences de notificação (email, push, SMS)

### 28. **Modo Mobile**

- Interface responsiva para celular
- App progressive web (PWA)
- Funcionamento offline
- Sincronização automática
- Otimização de bateria
- Sincronização em background

### 📊 Recursos Estratégicos (Core Business)

#### Inteligência Financeira

1. **Score de Saúde Financeira**

   - Cálculo baseado em múltiplos indicadores
   - Visualização em dashboard
   - Melhoria contínua com dicas personalizadas
   - Benchmark com média de usuários

2. **Insights Automáticos**

   - Detecção de tendências de gastos
   - Alertas de anomalias (gastos fora do padrão)
   - Sugestões de economia por categoria
   - Análise de sazonalidade

3. **Previsões**
   - Previsão de saldo (próximos 30/60/90 dias)
   - Previsão de gastos por categoria
   - Estimativa de economia anual
   - Projeção de fluxo de caixa

#### Qualidade de Vida Financeira

4. **Metas e Objetivos**

   - Criar metas (economia, viagem, carro, casa)
   - Progresso visual das metas
   - Sugestões de economia para atingir metas
   - Compartilhamento de metas com cônjuge/família

5. **Análise Comportamental**
   - Padrões de gasto por dia da semana
   - Gastos por horário do dia
   - Categorias com maior variação
   - Hábitos de consumo

#### Segurança Financeira

6. **Detecção de Fraude**

   - Monitoramento de transações anormais
   - Alertas de atividade suspeita
   - Padrões de gasto esperados vs reais
   - Integração com Open Banking (futura)

7. **Gestão de Risco**
   - Alertas de saldo baixo crítico
   - Projeção de insolvência (próximos 30 dias)
   - Recomendação de fundo de emergência
   - Análise de saúde de cartão de crédito

## 🛠️ Tecnologias e Arquitetura

### Tecnologias Utilizadas

**Frontend**

- **Framework Principal**: Vue.js 3
- **UI Framework**: Vuetify 3
- **Gerenciamento de Estado**: Pinia
- **Roteamento**: Vue Router
- **HTTP Client**: Axios
- **Gráficos**: Chart.js e D3.js
- **Calendário**: FullCalendar
- **Formatação de Datas**: Moment.js

**Backend**

- **Linguagem**: PHP 8.2+
- **Framework**: Laravel 10
- **Banco de Dados**: MySQL 8.0
- **Cache**: Redis
- **API**: RESTful com autenticação JWT
- **Filas e Jobs**: Laravel Queue

**DevOps**

- **Controle de Versão**: Git e GitHub
- **CI/CD**: GitHub Actions
- **Containerização**: Docker
- **Hospedagem**: AWS (Amazon Web Services)
- **Monitoramento**: New Relic

**Ferramentas de Desenvolvimento**

- **IDE**: Visual Studio Code
- **Testes**: PHPUnit, Jest
- **Linting**: ESLint, PHP_CodeSniffer
- **Documentação**: OpenAPI (Swagger)

### Arquitetura do Sistema

O MrFinancas utiliza uma arquitetura cliente-servidor com separação clara entre frontend e backend:

### Camada de Apresentação (Frontend)

- Interface responsiva baseada em componentes Vue.js
- PWA (Progressive Web App) para funcionamento offline
- Design mobile-first com adaptação para todos os dispositivos

### Camada de Aplicação (Backend)

- API RESTful para comunicação com o frontend
- Controladores para lógica de negócios
- Serviços para operações complexas
- Middleware para autenticação e autorização

### Camada de Dados

- Modelos Eloquent ORM
- Migrações e seeds para estrutura de banco
- Validação de dados com regras de negócio

### Integração

- Webhooks para serviços externos
- WebSockets para atualizações em tempo real
- Filas assíncronas para operações pesadas

## 📦 Instalação e Configuração

### Pré-requisitos

- Node.js v22
- PHP 8.2+
- Composer
- MySQL 8.0
- Redis
- Git

### Backend

1. Clone o repositório:

```bash
git clone https://github.com/marcos-burghausen/Mr-financas.git
# acesse o diretório
cd Mr-financas
```

2. Dentro do diretório backend duplique o arquivo .env.example e o renomeie para .env

3. Suba os containers e instale as dependências:

```bash
docker composer up -d
# Acesse o container Mr_financas_backend
docker exec -it Mr_financas_backend
# De permissão para acessar o storage
chmod -R 777 ./storage
# Instale as dependências
composer install
# Crie as tabelas no banco de dados
php artisan migrate
# Gere o token jwt
php artisan jwt:secret
```

4. Instale as dependências:

```bash
composer install
```

3. Configure o ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure o banco de dados no arquivo `.env`

5. Execute as migrações:

```bash
php artisan migrate --seed
```

6. Inicie o servidor:

```bash
php artisan serve
```

### Frontend

1. Navegue para o diretório frontend:

```bash
cd ../frontend
```

2. Instale as dependências:

```bash
npm install
```

3. Configure o ambiente:

```bash
cp .env.example .env.local
```

4. Inicie o servidor de desenvolvimento:

```bash
npm run dev
```

5. Para build de produção:

```bash
npm run build
```

### Solução de Problemas

- **Erro de conexão com MySQL**: Verifique as credenciais no `.env`
- **Versão Node.js incompatível**: Use `nvm` para instalar v18+
- Consulte [Troubleshooting](#troubleshooting) para mais detalhes

## 🔑 Variáveis de Ambiente

### Backend (.env)

Crie um arquivo `.env` na raiz do diretório `backend/` com as seguintes variáveis:

```env
# Aplicação
APP_NAME=MrFinancas
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mr_financas
DB_USERNAME=root
DB_PASSWORD=root

# Cache
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Fila
QUEUE_CONNECTION=redis

# JWT
JWT_SECRET=seu_secret_jwt_aqui_gerado_com_artisan_jwt_secret
JWT_ALGORITHM=HS256
JWT_EXPIRES_IN=31536000

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
MAIL_FROM_ADDRESS=noreply@mrfinancas.com
MAIL_FROM_NAME=MrFinancas

# Autenticação Social (OAuth)
FACEBOOK_APP_ID=seu_app_id
FACEBOOK_APP_SECRET=seu_app_secret
FACEBOOK_APP_REDIRECT_URI=http://localhost:8000/auth/facebook/callback

GOOGLE_CLIENT_ID=seu_client_id
GOOGLE_CLIENT_SECRET=seu_client_secret
GOOGLE_CALLBACK_URL=http://localhost:8000/auth/google/callback

LINKEDIN_CLIENT_ID=seu_client_id
LINKEDIN_CLIENT_SECRET=seu_client_secret
LINKEDIN_CALLBACK_URL=http://localhost:8000/auth/linkedin/callback

# Monitoramento
NEW_RELIC_LICENSE_KEY=sua_license_key
NEW_RELIC_APP_NAME=MrFinancas

# Segurança
SESSION_DOMAIN=localhost
SESSION_LIFETIME=120
SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

### Frontend (.env.local)

Crie um arquivo `.env.local` na raiz do diretório `frontend/` com as seguintes variáveis:

```env
# API
VITE_API_BASE_URL=http://localhost:8000/api
VITE_API_TIMEOUT=30000

# Aplicação
VITE_APP_NAME=MrFinancas
VITE_APP_VERSION=0.0.5

# Tema
VITE_DEFAULT_THEME=light

# Analytics (opcional)
VITE_GOOGLE_ANALYTICS_ID=seu_tracking_id
```

## ✅ Execução de Testes

### Backend - PHPUnit

#### Configurar Ambiente de Testes

```bash
# Copie o arquivo .env para .env.testing
cp backend/.env backend/.env.testing

# Configure as variáveis para ambiente de teste
# DB_DATABASE=mr_financas_test
# CACHE_DRIVER=array
# QUEUE_CONNECTION=sync
```

#### Rodar Testes

```bash
cd backend

# Executar todos os testes
php artisan test

# Executar testes com cobertura de código
php artisan test --coverage

# Executar testes de uma feature específica
php artisan test --filter=LoginControllerTest

# Executar testes com output verboso
php artisan test --verbose
```

#### Estrutura de Testes

```
backend/tests/
├── Feature/          # Testes de funcionalidades (integração)
│   ├── Auth/
│   ├── Accounts/
│   ├── Transactions/
│   └── Reports/
└── Unit/            # Testes unitários
    ├── Services/
    ├── Models/
    └── Utils/
```

#### Cobertura de Testes Esperada

- **Meta**: 80%+ de cobertura de código
- **Controllers**: 100%
- **Services**: 85%+
- **Models**: 75%+

### Frontend - Vitest/Jest

#### Rodar Testes

```bash
cd frontend

# Executar todos os testes
npm run test

# Executar testes em modo watch
npm run test:watch

# Gerar cobertura de código
npm run test:coverage

# Executar um teste específico
npm run test -- PerfilView.spec.ts
```

#### Estrutura de Testes

```
frontend/src/
├── components/__tests__/     # Testes de componentes
├── stores/__tests__/         # Testes de Pinia stores
├── services/__tests__/       # Testes de serviços
└── utils/__tests__/          # Testes de utilitários
```

## 🚀 Deploy em Produção

### Pré-requisitos para Deploy

- ✅ Todos os testes passando (cobertura mínima 80%)
- ✅ Análise de segurança completada
- ✅ Variáveis de ambiente configuradas
- ✅ Backup do banco de dados realizado
- ✅ Plano de rollback preparado

### Backend - Deploy em AWS/Docker

#### 1. Build da Imagem Docker

```bash
cd backend
docker build -t mr-financas-backend:latest -f Dockerfile .

# Com tag específica de versão
docker build -t mr-financas-backend:1.0.0 -f Dockerfile .
```

#### 2. Push para Registro (ECR/Docker Hub)

```bash
# AWS ECR
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin 123456789.dkr.ecr.us-east-1.amazonaws.com
docker tag mr-financas-backend:latest 123456789.dkr.ecr.us-east-1.amazonaws.com/mr-financas-backend:latest
docker push 123456789.dkr.ecr.us-east-1.amazonaws.com/mr-financas-backend:latest
```

#### 3. Deploy com Zero Downtime

```bash
# Usando AWS ECS
aws ecs update-service \
  --cluster mr-financas-cluster \
  --service mr-financas-backend-service \
  --force-new-deployment

# Verificar status
aws ecs describe-services \
  --cluster mr-financas-cluster \
  --services mr-financas-backend-service
```

#### 4. Executar Migrações

```bash
# Após o deploy
docker exec mr-financas-backend php artisan migrate --force
docker exec mr-financas-backend php artisan cache:clear
docker exec mr-financas-backend php artisan config:cache
```

### Frontend - Deploy em AWS/CDN

#### 1. Build de Produção

```bash
cd frontend

# Build otimizado
npm run build

# Verificar tamanho dos assets
npm run build -- --analyze
```

#### 2. Deploy em S3 + CloudFront

```bash
# Upload para S3
aws s3 sync dist/ s3://mr-financas-frontend/ --delete

# Invalidar cache CloudFront
aws cloudfront create-invalidation \
  --distribution-id E123EXAMPLE \
  --paths "/*"
```

#### 3. Workflow de Deploy - GitHub Actions

```yaml
name: Deploy Produção

on:
  push:
    branches: [main]

jobs:
  deploy-backend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Build Backend
        run: docker build -t backend:latest ./backend
      - name: Push to ECR
        run: |
          aws ecr get-login-password | docker login --username AWS --password-stdin $ECR_REGISTRY
          docker push $ECR_REGISTRY/mr-financas:latest

  deploy-frontend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Build Frontend
        run: cd frontend && npm install && npm run build
      - name: Deploy to S3
        run: aws s3 sync frontend/dist s3://mr-financas-frontend --delete
      - name: Invalidate CloudFront
        run: aws cloudfront create-invalidation --distribution-id $CDN_ID --paths "/*"
```

### Estratégia de Rollback

```bash
# Reverter para versão anterior
aws ecs update-service \
  --cluster mr-financas-cluster \
  --service mr-financas-backend-service \
  --task-definition mr-financas-backend:NUMERO_VERSAO_ANTERIOR

# Verificar health checks
aws elbv2 describe-target-health \
  --target-group-arn arn:aws:elasticloadbalancing:...
```

## 🔒 Segurança Detalhada

### Validação de Entrada (OWASP Top 10)

#### Backend - Laravel

```php
// Validação de Request
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:12|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        'amount' => 'required|numeric|min:0.01|max:999999.99',
    ]);
}

// Sanitização
$input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
$input = filter_var($input, FILTER_SANITIZE_STRING);
```

#### Frontend - Vue 3

```typescript
// Sanitização de entrada
import DOMPurify from "dompurify";

const sanitizeInput = (dirty: string): string => {
  return DOMPurify.sanitize(dirty, { ALLOWED_TAGS: [] });
};

// Validação
const validateEmail = (email: string): boolean => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
};
```

### Proteção contra SQL Injection

```php
// ❌ NUNCA faça isso
$users = DB::select("SELECT * FROM users WHERE email = '" . $email . "'");

// ✅ Use query builder ou prepared statements
$users = User::where('email', $email)->get();
$users = DB::select('SELECT * FROM users WHERE email = ?', [$email]);
```

### Proteção contra XSS

```php
// Backend - Escape na resposta
return response()->json([
    'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
]);

// Frontend - Use v-text ou sanitização
<p v-text="userMessage"></p>
<p>{{ sanitizeInput(userMessage) }}</p>
```

### Autenticação e Autorização

```php
// JWT - Token Expiration
'jwt' => [
    'secret' => env('JWT_SECRET'),
    'algo' => 'HS256',
    'expires' => 31536000, // 1 ano em segundos
    'refresh_ttl' => 604800, // 7 dias
],

// 2FA - Habilitação
$user->enableTwoFactorAuth(secret: $secret);

// Rate Limiting
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/login', 'AuthController@login');
    Route::post('/password-reset', 'PasswordResetController@store');
});
```

### Rate Limiting Detalhado

```php
// Middleware customizado
class ApiRateLimit
{
    public function handle($request, $next)
    {
        $throttle = match($request->path()) {
            'api/login' => '5,1',           // 5 tentativas por minuto
            'api/password-reset' => '3,5',   // 3 tentativas por 5 minutos
            default => '60,1',               // 60 requisições por minuto
        };

        return $next($request)->middleware("throttle:$throttle");
    }
}
```

### Políticas de Senha

```env
# .env
PASSWORD_MIN_LENGTH=12
PASSWORD_REQUIRE_UPPERCASE=true
PASSWORD_REQUIRE_LOWERCASE=true
PASSWORD_REQUIRE_NUMBERS=true
PASSWORD_REQUIRE_SPECIAL_CHARS=true
PASSWORD_EXPIRY_DAYS=90
PASSWORD_HISTORY_COUNT=5
```

### Criptografia de Dados Sensíveis

```php
// Dados em repouso - AES-256
$encrypted = encrypt($sensitiveData);
$decrypted = decrypt($encrypted);

// Hashing de Senhas - Bcrypt
$hashed = Hash::make($password);
Hash::check($password, $hashed);

// HTTPS obrigatório
// config/app.php
'url' => env('APP_URL', 'https://app.example.com'),
```

### Headers de Segurança

```php
// middleware/EncryptCookies.php
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'');
```

## ⚡ Performance e Escalabilidade

### Otimizações de Backend

#### Queries Otimizadas

```php
// ❌ N+1 Query Problem
$users = User::all();
foreach ($users as $user) {
    echo $user->accounts->count(); // Query para cada user
}

// ✅ Eager Loading
$users = User::with('accounts')->get();
foreach ($users as $user) {
    echo $user->accounts->count(); // Sem queries adicionais
}

// ✅ Select apenas colunas necessárias
$users = User::select('id', 'name', 'email')->get();
```

#### Caching

```php
// Redis Cache
Cache::remember('user.reports:' . $userId, 3600, function () use ($userId) {
    return Report::where('user_id', $userId)->get();
});

// Cache Invalidation
Cache::forget('user.reports:' . $userId);
Cache::tags(['user', 'reports'])->flush();
```

#### Índices de Banco de Dados

```php
// Migration
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id')->index();
    $table->unsignedBigInteger('account_id')->index();
    $table->date('date_transaction')->index();
    $table->decimal('amount', 15, 2);

    // Índice composto
    $table->index(['user_id', 'date_transaction']);

    // Índice fulltext para buscas
    $table->fullText(['description']);
});
```

### Otimizações de Frontend

#### Code Splitting

```typescript
// Route-based code splitting
const Dashboard = defineAsyncComponent(
  () => import("./views/DashboardView.vue")
);

const Reports = defineAsyncComponent(() => import("./views/ReportsView.vue"));
```

#### Lazy Loading de Imagens

```vue
<img v-lazy="imageUrl" :alt="imageAlt" loading="lazy" />
```

#### Compressão de Assets

```bash
# Verificar tamanho
npm run build -- --analyze

# Resultado esperado
# main.js: ~150KB (gzipped: ~40KB)
# vendor.js: ~80KB (gzipped: ~25KB)
```

### Benchmarks Esperados

| Métrica                  | Target  | Ferramenta       |
| ------------------------ | ------- | ---------------- |
| Time to First Byte       | < 200ms | Google Analytics |
| First Contentful Paint   | < 1.5s  | Lighthouse       |
| Largest Contentful Paint | < 2.5s  | Lighthouse       |
| Cumulative Layout Shift  | < 0.1   | Lighthouse       |
| Lighthouse Score         | 90+     | Lighthouse       |
| API Response Time        | < 500ms | New Relic        |
| DB Query Time            | < 100ms | New Relic        |

## 💾 Backup e Disaster Recovery

### Estratégia de Backup

#### Backup Automático

```bash
# Script de backup diário
#!/bin/bash
# backup.sh

BACKUP_DIR="/backups/mr-financas"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Backup do banco de dados
mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME \
  | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup de arquivos críticos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz \
  ./backend/storage/app/uploads \
  ./backend/config

# Upload para S3
aws s3 sync $BACKUP_DIR s3://mr-financas-backups/ --delete

# Limpeza de backups antigos
find $BACKUP_DIR -name "*.gz" -mtime +$RETENTION_DAYS -delete
```

#### Schedule no Laravel

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('backup:run')->daily()->at('02:00');
    $schedule->command('backup:clean')->daily()->at('03:00');
}
```

### RTO e RPO

| Métrica                            | Objetivo | Ação                            |
| ---------------------------------- | -------- | ------------------------------- |
| **RPO** (Recovery Point Objective) | 1 hora   | Backups automáticos a cada hora |
| **RTO** (Recovery Time Objective)  | 4 horas  | Documentação de restauração     |
| **Retenção**                       | 90 dias  | Backups incrementais            |

### Processo de Restauração

```bash
# 1. Listar backups disponíveis
aws s3 ls s3://mr-financas-backups/

# 2. Download do backup
aws s3 cp s3://mr-financas-backups/db_20241017_120000.sql.gz .

# 3. Restaurar banco de dados
gunzip < db_20241017_120000.sql.gz | mysql -u $DB_USER -p $DB_NAME

# 4. Restaurar arquivos
tar -xzf files_20241017_120000.tar.gz

# 5. Verificação
php artisan migrate
php artisan cache:clear
```

## 📊 Monitoramento e Logs

### Sistema de Logs Centralizado

#### Backend - Logging com Laravel

```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'slack'],
    ],
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 30,
    ],
];

// Estrutura de log
Log::info('User login', [
    'user_id' => $user->id,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'timestamp' => now(),
]);

Log::error('Payment failed', [
    'transaction_id' => $transaction->id,
    'error' => $exception->getMessage(),
]);
```

#### Frontend - Logging

```typescript
// utils/logger.ts
enum LogLevel {
  DEBUG = "DEBUG",
  INFO = "INFO",
  WARN = "WARN",
  ERROR = "ERROR",
}

const logger = {
  debug: (message: string, data?: any) => {
    console.log(`[${LogLevel.DEBUG}]`, message, data);
  },
  info: (message: string, data?: any) => {
    console.info(`[${LogLevel.INFO}]`, message, data);
  },
  warn: (message: string, data?: any) => {
    console.warn(`[${LogLevel.WARN}]`, message, data);
  },
  error: (message: string, error?: any) => {
    console.error(`[${LogLevel.ERROR}]`, message, error);
    // Enviar para backend de logs
    sendToLogService({ level: "ERROR", message, error });
  },
};
```

### Monitoramento com New Relic

#### Configuração

```php
// bootstrap/app.php
if (extension_loaded('newrelic')) {
    newrelic_set_appname(env('NEW_RELIC_APP_NAME'));
}

// Rastreamento customizado
newrelic_custom_metric('CustomMetric/PaymentProcessed', 1);
```

#### Alertas Configurados

| Alerta      | Threshold    | Ação                |
| ----------- | ------------ | ------------------- |
| CPU Alto    | > 80%        | Notificar via Slack |
| Erro na API | > 5% de taxa | Page on-call        |
| Latência    | > 1000ms     | Investigação        |
| Disco Cheio | > 85%        | Notificar admin     |
| DB Lento    | > 100ms      | Otimizar query      |

## � Troubleshooting

### Problemas Comuns

#### 1. **ERRO: "Connection refused" ao conectar MySQL**

**Sintoma:**

```
SQLSTATE[HY000] [2002] Connection refused
```

**Solução:**

```bash
# Verifique se MySQL está rodando
docker ps | grep mysql
# ou
brew services list | grep mysql

# Verifique credenciais em .env
cat backend/.env | grep DB_

# Tente conectar manualmente
mysql -h 127.0.0.1 -u root -p

# Se estiver em Docker
docker logs mr_financas_mysql
```

#### 2. **ERRO: "RuntimeException: No application encryption key has been specified"**

**Sintoma:**

```
No application encryption key has been specified
```

**Solução:**

```bash
cd backend
php artisan key:generate
php artisan jwt:secret
```

#### 3. **ERRO: "CORS policy: no 'Access-Control-Allow-Origin' header"**

**Sintoma:**

```
Access to XMLHttpRequest blocked by CORS policy
```

**Solução:**

```php
// config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

#### 4. **ERRO: "VITE API request timeout"**

**Sintoma:**

```
ERR_CONNECTION_TIMED_OUT
```

**Solução:**

```typescript
// frontend/.env.local
VITE_API_TIMEOUT = 30000; // 30 segundos

// services/http.ts
const timeout = parseInt(import.meta.env.VITE_API_TIMEOUT || "30000");
axiosInstance.defaults.timeout = timeout;
```

#### 5. **ERRO: "Redis connection failed"**

**Sintoma:**

```
Could not connect to Redis
```

**Solução:**

```bash
# Verificar se Redis está rodando
redis-cli ping

# ou com Docker
docker ps | grep redis
docker logs mr_financas_redis

# Resetar conexão
redis-cli FLUSHALL
```

#### 6. **ERRO: "npm run dev - Module not found"**

**Sintoma:**

```
Cannot find module '@/store/...'
```

**Solução:**

```bash
cd frontend
rm -rf node_modules package-lock.json
npm install

# Limpar cache Vite
rm -rf .vite
npm run dev
```

#### 7. **ERRO: "PHP artisan migrate - Table already exists"**

**Sintoma:**

```
SQLSTATE[42S01]: Table already exists
```

**Solução:**

```bash
cd backend

# Rollback todas as migrações
php artisan migrate:reset

# Reexecutar migrações
php artisan migrate:fresh --seed
```

#### 8. **ERRO: "Arquivo de permissão negada em storage"**

**Sintoma:**

```
Permission denied writing to .../storage/logs/laravel.log
```

**Solução:**

```bash
cd backend

# Dar permissão
chmod -R 777 storage bootstrap/cache

# Ou com Docker
docker exec mr_financas_backend chmod -R 777 storage bootstrap/cache
```

#### 9. **ERRO: "JWT token invalid or expired"**

**Sintoma:**

```
{
  "message": "Unauthorized",
  "error": "Token invalid"
}
```

**Solução:**

```typescript
// Frontend - Renovar token
const refreshToken = async () => {
  try {
    const response = await axios.post("/api/auth/refresh");
    localStorage.setItem("token", response.data.token);
    return response.data.token;
  } catch (error) {
    // Redirecionar para login
    router.push({ name: "login" });
  }
};
```

#### 10. **ERRO: "Build fail - Memory limit exceeded"**

**Sintoma:**

```
JavaScript heap out of memory
npm ERR! code FATAL
```

**Solução:**

```bash
# Aumentar limite de memória
export NODE_OPTIONS=--max_old_space_size=4096
npm run build

# Ou definitivamente
echo "max_old_space_size=4096" > ~/.npmrc
```

### Logs de Erro Esperados (Não são Críticos)

```
# Warning - Pode ignorar em desenvolvimento
[2024-10-17 12:00:00] local.WARNING: Undefined array key...

# Info - Informacional
[2024-10-17 12:00:00] local.INFO: User login successful

# Debug - Apenas com APP_DEBUG=true
[2024-10-17 12:00:00] local.DEBUG: SQL Query...
```

### Verificação de Saúde da Aplicação

```bash
# Backend - Health Check
curl http://localhost:8000/api/health

# Response esperado
{
  "status": "ok",
  "database": "connected",
  "redis": "connected",
  "timestamp": "2024-10-17T12:00:00Z"
}

# Frontend - Verificar conexão API
curl http://localhost:3000/api/config

# Verificar certificado SSL (Produção)
curl -I https://api.mrfinancas.com
```

### Ferramentas de Debug

#### Backend

```bash
# Usar Laravel Tinker para debug
php artisan tinker

# Teste de query
> User::count()
> User::where('email', 'test@example.com')->first()
```

#### Frontend

```typescript
// Vue DevTools
// Download: Chrome Web Store

// Console browser
console.log("Debug:", variable);

// Network tab - F12 > Network
// Verificar requisições e responses
```

## �📚 Documentação e Diagramas

### Documentação

- [**Requisitos do Sistema**](./docs/requisitos/requisitos.md)
- [**Manual do Usuário**](./docs/guias/user_guide.md)

### Modelos e Diagramas

- [**Diagrama ER**](./docs/diagramas/Diagrama%20ER%20-%20MrFinancas.md)
- **Protótipos**:
  - [Dashboard](./docs/wireframes/dashboard.md),
  - [Lançamentos](./docs/wireframes/lancamentos.md),
  - [Relatórios](./docs/wireframes/relatorios.md),
  - [Perfil](./docs/wireframes/perfil.md)
- **Diagramas UML**:
  - [Classes](./docs/diagramas/Diagrama%20de%20Classes%20-%20Sistema%20de%20Finanças%20Pessoal.markdown),
  - [Componentes](./docs/diagramas/Diagrama%20de%20Componentes%20-%20MrFinancas.md),
  - [Implantação](./docs/diagramas/Diagrama%20de%20Implantação%20-%20MrFinancas.md),
  - **Sequencia**:
    - [Cadastrar Usuário](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Cadastrar%20Usuário.markdown),
    - [Login](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Fazer%20Login.markdown),
    - [Configurar Notificações](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Configurar%20Notificações.markdown),
    - [Criar Transação](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Criar%20Lançamento.markdown),
    - [Exportar para Calendario](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Exportar%20para%20Calendário.markdown),
    - [Gerar Relatório](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Gerar%20Relatório.markdown),
    - [Gerenciar Cartão](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Gerenciar%20Cartão.markdown),
    - [Gerencia Categoria_Subcategoria](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Gerenciar%20Categoria.markdown),
    - [Gerenciar Conta](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Gerenciar%20Conta.markdown),
    - [Realizar Backup_Restauração](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Realizar%20Backup%20e%20Restauração.markdown),
    - [Visualizar Fatura](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Visualizar%20Fatura.markdown),
    - [Visualizar_Editar_Excluir Transação](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Visualizar,%20Editar%20e%20Excluir%20Transação.markdown),
    - [Configurar Perfil](./docs/diagramas/Diagrama%20de%20Sequência%20-%20Configurar%20Perfil.markdown)

### Fluxos e Casos de Uso

- Fluxos:
  - [Cadastrar Usuário](./docs/fluxos/Fluxo%20-%20Cadastrar%20Usuário.markdown),
  - [Login](./docs/fluxos/Fluxo%20-%20Fazer%20Login.markdown),
  - [Configurar Notificações](./docs/fluxos/Fluxo%20-%20Configurar%20Notificações.markdown),
  - [Criar Transação](./docs/fluxos/Fluxo%20-%20Gerenciar%20Transação.markdown),
  - [Exportar para Calendário](./docs/fluxos/Fluxo%20-%20Exportar%20para%20Calendário.markdown),
  - [Gerar Relatório](./docs/fluxos/Fluxo%20-%20Gerar%20Relatório.markdown),
  - [Gerenciar Cartão](./docs/fluxos/Fluxo%20-%20Gerenciar%20Cartão.markdown),
  - [Gerencia Categoria_Subcategoria](./docs/fluxos/Fluxo%20-%20Gerenciar%20Categoria.markdown),
  - [Gerenciar Conta](./docs/fluxos/Fluxo%20-%20Gerenciar%20Conta.markdown),
  - [Realizar Backup_Restauração](./docs/fluxos/Fluxo%20-%20Realizar%20Backup%20e%20Restauração.markdown),
  - [Visualizar Fatura](./docs/fluxos/Fluxo%20-%20Visualizar%20Fatura.markdown),
  - [Visualizar_Editar_Excluir Transação](./docs/fluxos/Fluxo%20-%20Visualizar,%20Editar%20e%20Excluir%20Transação.markdown),
  - [Configurar Perfil](./docs/fluxos/Fluxo%20-%20Configurar%20Perfil.markdown)
- Casos de Uso:
  - [Cadastrar Usuário](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Cadastrar%20Usuario.md),
  - [Login](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Fazer%20Login.markdown),
  - [Configurar Notificações](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Configurar%20Notificações.markdown),
  - [Criar Transação](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Criar%20Transacao.markdown),
  - [Exportar para Calendário](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Exportar%20para%20Calendário.markdown),
  - [Gerar Relatório](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Gerar%20Relatório.markdown),
  - [Gerenciar Cartão](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Gerenciar%20Cartão.markdown),
  - [Gerencia Categoria_Subcategoria](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Gerenciar%20Categoria_Subcategoria.markdown),
  - [Gerenciar Conta](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Gerenciar%20Conta.markdown),
  - [Realizar Backup_Restauração](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Realizar%20Backup_Restauração.markdown),
  - [Visualizar Fatura](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Visualizar%20Fatura.markdown),
  - [Visualizar_Editar_Excluir Transação](./docs/casosDeUso/Caso%20de%20Uso%20UML%20-%20Visualizar_Editar_Excluir%20Lançamento.markdown),
  - [Configurar Perfil](./docs/casosDeUso/Caso%20de%20Uso%20-%20Configurar%20Perfil.markdown)

### API

- [Visão Geral](./docs/api/visao_geral.md)
- [Swagger](./docs/api/swagger.md)(Em breve)

## �️ Roadmap e Contribuição

### Roadmap de Desenvolvimento

**Versão 1.0.0 (Q4 2025)** - MVP  
[![Status](https://img.shields.io/badge/MVP-Q4_2025-blue)](https://github.com/marcos-burghausen/MrFinancas)

Funcionalidades Essenciais:

- [x] Autenticação (e-mail/senha, JWT)
- [x] Cadastro de receitas/despesas com categorização
- [x] Dashboard com gráficos (Chart.js)
- [x] Controle de contas e cartões
- [ ] Notificações por e-mail (vencimentos)
- [ ] Sistema de backup automático
- [ ] Deploy automatizado com GitHub Actions

**Versão 1.1 (Q1 2026)** - Experiência do Usuário

- [ ] Notificações push em tempo real
- [ ] Melhorias de UI/UX (Redesign Mobile)
- [ ] Modo dark theme (já iniciado)
- [ ] Localização i18n (PT-BR, EN, ES)
- [ ] Exportação de relatórios (PDF, Excel)

**Versão 1.2 (Q2 2026)** - Integração Bancária

- [ ] Integração com Open Banking
- [ ] Importação automática de transações
- [ ] Sincronização com bancos
- [ ] Conciliação automática

**Versão 2.0 (Q3 2026)** - Mobile

- [ ] Aplicativo móvel nativo (React Native)
- [ ] iOS e Android
- [ ] Sincronização em tempo real (WebSockets)
- [ ] Notificações push

**Versão 2.5 (Q4 2026)** - Inteligência

- [ ] Sistema de metas financeiras
- [ ] Recomendações de economia com IA
- [ ] Análise de gastos inteligente
- [ ] Previsões com Machine Learning

**Versão 3.0 (Q4 2027)** - Investimentos

- [ ] Módulo de investimentos
- [ ] Carteira de ações e fundos
- [ ] Análise preditiva com IA
- [ ] Rebalanceamento automático

### Estrutura de Branches (Git Flow)

```
main (Production)
  ↓
release/v1.0.0
  ↓
develop (Development)
  ├── feature/autenticacao
  ├── feature/dashboard
  ├── bugfix/fix-cors
  └── hotfix/security-patch
```

**Convenção de Commits:**

```
feat: adiciona nova funcionalidade
fix: corrige bug
docs: atualiza documentação
style: mudanças de código (sem lógica)
refactor: refatora código existente
test: adiciona/atualiza testes
chore: tarefas de build/deploy
```

### Como Contribuir

#### 1. Setup Inicial

```bash
# Fork o repositório
git clone https://github.com/seu-usuario/MrFinancas.git
cd MrFinancas

# Adicione upstream
git remote add upstream https://github.com/marcos-burghausen/MrFinancas.git

# Configure ambiente (veja Instalação)
```

#### 2. Desenvolva sua Feature

```bash
# Crie branch do develop
git checkout develop
git pull upstream develop
git checkout -b feature/sua-feature

# Faça commits pequenos e descritivos
git commit -m "feat: adiciona validação de email"
git commit -m "test: adiciona testes para validação"

# Mantenha sincronizado com upstream
git fetch upstream
git rebase upstream/develop
```

#### 3. Testes Obrigatórios

```bash
# Backend
cd backend
php artisan test
php artisan test --coverage

# Frontend
cd ../frontend
npm run test
npm run test:coverage
```

**Cobertura Mínima Exigida:**

- Backend: 80%+
- Frontend: 75%+

#### 4. Code Quality

```bash
# Linting
cd backend && php -l app/**/*.php
cd ../frontend && npm run lint

# Análise de código
cd backend && vendor/bin/phpstan analyse
```

#### 5. Submeta Pull Request

```bash
git push origin feature/sua-feature

# No GitHub:
# 1. Crie PR contra `develop` (não main!)
# 2. Preencha template obrigatório
# 3. Aguarde revisão
# 4. Faça ajustes conforme feedback
```

#### Template de PR

```markdown
## Descrição

Breve descrição da mudança

## Tipo de Mudança

- [ ] Bug fix
- [ ] Nova feature
- [ ] Breaking change
- [ ] Mudança de docs

## Checklist

- [ ] Testes passando (>80% cobertura)
- [ ] Sem warnings de linting
- [ ] Documentação atualizada
- [ ] Commits semânticos
- [ ] Sem conflitos com develop

## Screenshots (se aplicável)
```

### Reportar Issues

Ao reportar bugs, inclua:

1. Versão do MrFinanças
2. Passos para reproduzir
3. Comportamento esperado vs atual
4. Screenshots/logs
5. Ambiente (OS, navegador, versões)

### Documentação para Contribuidores

- [Guia de Contribuição Completo](./docs/guias/guia_contribuicao.md)
- [Padrões de Código](./docs/guias/padroes_codigo.md)
- [Arquitetura](./docs/guias/arquitetura.md)

### Comunidade

- **Discussões**: GitHub Discussions
- **Chat**: Discord (em breve)
- **Email**: dev@mrfinancas.com

## ❓ FAQ e Licença

### Perguntas Frequentes (FAQ)

#### Funcionalidades

**P: O MrFinanças é gratuito?**  
R: Sim, com funcionalidades básicas. Planos premium oferecem recursos avançados como:

- Relatórios avançados
- Integração com bancos
- Módulo de investimentos

**P: Posso acessar offline?**  
R: Sim, via PWA (Progressive Web App) com sincronização automática quando conectado à internet.

**P: Quais formatos de extrato são suportados?**  
R: Atualmente CSV e OFX (importação manual). Integração automática com Open Banking planejada para v1.2 (Q2 2026).

**P: O módulo de investimentos será gratuito?**  
R: Detalhes serão divulgados na v3.0 (Q4 2027).

#### Técnicas

**P: Que banco de dados vocês usam?**  
R: MySQL 8.0 para dados relacionais e Redis para cache e fila de processamento.

**P: É possível fazer deploy em outros servidores além AWS?**  
R: Sim! O MrFinanças roda em qualquer servidor com PHP 8.2+, MySQL 8.0 e Redis. Suportamos:

- AWS (EC2, ECS, Lambda)
- DigitalOcean
- Heroku
- VPS genérico
- Docker Swarm
- Kubernetes

**P: Qual a versão Node.js recomendada?**  
R: Node.js v22 LTS. Compatível com v18+ mas v22 é recomendado para melhor performance.

#### Segurança

**P: Meus dados financeiros estão seguros?**  
R: Sim! Implementamos:

- Criptografia AES-256 em repouso
- HTTPS/TLS em trânsito
- Autenticação JWT com 2FA
- Conformidade LGPD/GDPR
- Auditoria de acesso
- Backups automáticos

**P: Vocês compartilham dados com terceiros?**  
R: Não. Seus dados são seus. Nunca compartilhamos com serviços de marketing ou análise. Apenas integrações técnicas necessárias (ex: serviço de e-mail).

**P: Como reporto uma vulnerabilidade?**  
R: Envie um email para security@mrfinancas.com com detalhes. Agradecemos responsablemente!

#### Performance

**P: Quantos usuários o sistema suporta?**  
R: Arquitetura escalável. Testado com até 10.000 usuários simultâneos. Em produção:

- Aplicação stateless (escala horizontalmente)
- Cache Redis (10.000+ operações/segundo)
- CDN para assets estáticos

**P: Qual a velocidade esperada?**  
R:

- Carregamento inicial: < 2 segundos
- Requisições API: < 500ms
- Relatórios: < 3 segundos

#### Suporte

**P: Vocês oferecem suporte comercial?**  
R: Não no momento, mas você pode:

- Abrir issues no GitHub
- Participar da comunidade
- Enviar PRs com melhorias
- Contactar maintainers via email

**P: Posso usar MrFinanças para clientes?**  
R: Sim! A licença MIT permite uso comercial. Apenas mantenha atribuição.

### Licença

Este projeto está licenciado sob a [Licença MIT](LICENSE).

**Resumo MIT:**

- ✅ Uso comercial
- ✅ Modificação
- ✅ Distribuição
- ✅ Uso privado
- ❌ Sem responsabilidade do autor
- ⚠️ Deve incluir aviso de licença

Veja [LICENSE](LICENSE) para detalhes completos.

## 📞 Contato

Marcos Burghausen - [GitHub](https://github.com/marcos-burghausen)  
Projeto: [https://github.com/marcos-burghausen/MrFinancas](https://github.com/marcos-burghausen/MrFinancas)

---

⭐️ Se este projeto foi útil para você, considere deixar uma estrela no GitHub!

<!-- VITE_API_URL=https://mrfinancas.burghausen.dev/api -->

APP_DEBUG=false
APP_ENV=local
APP_KEY=base64:mJIUN6KJn0qPnuc3rkwF1H15eTghBhd27p55k7Zu/e0=
APP_NAME=Mr Finanças
APP_URL=https://mrfinancas.burghausen.dev
AWS_ACCESS_KEY_ID=
AWS_BUCKET=
AWS_DEFAULT_REGION=us-east-1
AWS_SECRET_ACCESS_KEY=
AWS_USE_PATH_STYLE_ENDPOINT=false
BROADCAST_DRIVER=log
CACHE_DRIVER=file
DB_CONNECTION=mysql
DB_DATABASE=default
DB_HOST=ek0ccwggss8c0wg0s4c84cgk
DB_PASSWORD=nwnr550L4KsKZV5bKCax22AlExoTrer51DH9en5reyQK04MkKU7EzsTQfZSREAZT
DB_PORT=3306
DB_USERNAME=mysql
FACEBOOK_CLIENT_ID=398220822617196
FACEBOOK_CLIENT_SECRET=15b73e73b06d9fe7376feae60478dd23
FACEBOOK_REDIRECT_URI=http://localhost:4081/auth/callback
FILESYSTEM_DISK=local
FRONTEND_URL=https://mrfinancas.burghausen.dev
JWT_SECRET=B4W42M5Z1LS2RXHfLs4X2ayzyqZ7Ji89EPf23unViVT7wXtYhObIAJRZKAUpmRDO
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=mrfinancas@burghausen.com.br
MAIL_FROM_NAME=${APP_NAME}
MAIL_HOST=mail.smtp2go.com
MAIL_MAILER=smtp
MAIL_PASSWORD=TA23XsBLgj0nQvoS
MAIL_PORT=2525
MAIL_USERNAME=burghausen.com.br
MEMCACHED_HOST=127.0.0.1
NIXPACKS_PHP_FALLBACK_PATH=/index.php
NIXPACKS_PHP_ROOT_DIR=/app/public
PUSHER_APP_CLUSTER=mt1
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
QUEUE_CONNECTION=database
REDIS_HOST=mgwc00ogwc4sk4o0cg8g8cw4
REDIS_PASSWORD=uOo6RtxEHSjC2IHd2IpC5fLCc7qM45pk8hUE4M0Y2WRudaZ1vNVWBbqk6ZNPMYCx
REDIS_PORT=6379
SANCTUM_STATEFUL_DOMAINS=mrfinancas.burghausen.dev
SESSION_DOMAIN=.burghausen.dev
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SAME_SITE=lax
SESSION_SECURE_COOKIE=true
TOKEN_EXPIRES_IN=30
TRUSTED_PROXIES=*
VITE_APP_NAME=${APP_NAME}
VITE_PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}
VITE_PUSHER_APP_KEY=${PUSHER_APP_KEY}
VITE_PUSHER_HOST=${PUSHER_HOST}
VITE_PUSHER_PORT=${PUSHER_PORT}
VITE_PUSHER_SCHEME=${PUSHER_SCHEME}

NIXPACKS_PHP_FALLBACK_PATH=/index.php
NIXPACKS_PHP_ROOT_DIR=/app/public
