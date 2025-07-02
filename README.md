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
- [Documentação e Diagramas](#documentação-e-diagramas)
- [Roadmap e Contribuição](#roadmap-e-contribuição)
- [Segurança, FAQ e Licença](#segurança-faq-e-licença)
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
- Consulte [FAQ](./docs/faq.md) para mais detalhes

## 📚 Documentação e Diagramas

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

## 🗺️ Roadmap e Contribuição

### Roadmap

**Versão 1.0.0 (Q4 2025)**  
[![Status](https://img.shields.io/badge/MVP-Q4_2025-blue)](https://github.com/marcos-burghausen/MrFinancas)

- [ ] Autenticação (e-mail/senha, JWT)
- [ ] Cadastro de receitas/despesas com categorização
- [ ] Dashboard com gráficos (Chart.js)
- [ ] Controle de contas e cartões
- [ ] Notificações por e-mail (vencimentos)

**Versão 1.1 (Q1 2026)**

- [ ] Notificações de pagamentos próximos
- [ ] Melhorias de UI/UX

**Versão 1.2 (Q2 2026)**

- [ ] Integração com Open Banking
- [ ] Importação automática de transações

**Versão 2.0 (Q3 2026)**

- [ ] Aplicativo móvel (iOS/Android)
- [ ] Sincronização em tempo real

**Versão 2.5 (Q4 2026)**

- [ ] Sistema de metas financeiras
- [ ] Recomendações de economia

**Versão 3.0 (Q4 2027)**

- [ ] Módulo de investimentos
- [ ] Análise preditiva com IA

### Como Contribuir

1. Verifique [issues](https://github.com/marcos-burghausen/MrFinancas/issues)
2. Faça fork do projeto
3. Crie branch (`git checkout -b feature/NovaFuncionalidade`)
4. Commit (`git commit -m 'Adiciona NovaFuncionalidade'`)
5. Push (`git push origin feature/NovaFuncionalidade`)
6. Abra Pull Request

Consulte o [Guia de Contribuição](./docs/guias/guia_contribuicao.md) e ajude a melhorar a documentação em `/docs`.

## 🔒 Segurança, FAQ e Licença

### Segurança e Privacidade

- **Criptografia**: AES-256 para dados sensíveis
- **Autenticação**: JWT, suporte a 2FA
- **Conformidade**: LGPD/GDPR
- **Monitoramento**: New Relic, backups automáticos
- Reporte vulnerabilidades: security@mrfinancas.com

### Perguntas Frequentes (FAQ)

- **O MrFinanças é gratuito?**  
  Sim, com funcionalidades básicas. Planos premium oferecem recursos avançados.
- **Posso acessar offline?**  
  Sim, via PWA com sincronização posterior.
- **Quais formatos de extrato são suportados?**  
  CSV e OFX (importação manual).
- **O módulo de investimentos será gratuito?**  
  Detalhes serão divulgados na v3.0 (2027).
- Veja mais em [FAQ](./docs/faq.md)

### Licença

Licenciado sob [MIT](LICENSE).

## 📞 Contato

Marcos Burghausen - [GitHub](https://github.com/marcos-burghausen)  
Projeto: [https://github.com/marcos-burghausen/MrFinancas](https://github.com/marcos-burghausen/MrFinancas)

---

⭐️ Se este projeto foi útil para você, considere deixar uma estrela no GitHub!
