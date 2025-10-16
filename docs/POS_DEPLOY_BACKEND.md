# 🚀 Checklist Pós-Deploy - Backend Laravel

## 📋 Índice

1. [Configurações Essenciais](#1-configurações-essenciais)
2. [Comandos Obrigatórios](#2-comandos-obrigatórios)
3. [Configurações do Sanctum](#3-configurações-do-sanctum)
4. [Configurações de Sessão](#4-configurações-de-sessão)
5. [Testes Pós-Deploy](#5-testes-pós-deploy)
6. [Troubleshooting](#6-troubleshooting)
7. [Rollback](#7-rollback)

---

## 1. Configurações Essenciais

### 1.1 Variáveis de Ambiente (.env)

Após o deploy, **ANTES** de qualquer comando, configure o `.env` de produção:

```bash
# 🔴 CONECTE NO SERVIDOR (SSH, painel, etc)
# Navegue até o diretório do backend
cd /caminho/do/backend
```

#### ✅ Variáveis CRÍTICAS para Autenticação

```bash
# ========================================
# AMBIENTE
# ========================================
APP_ENV=production
APP_DEBUG=false  # 🔴 NUNCA true em produção!
APP_KEY=  # ⚠️ Será gerado no passo 2.1

# ========================================
# URL DA APLICAÇÃO
# ========================================
APP_URL=https://seu-dominio-backend.com  # 🔴 URL do BACKEND (com HTTPS!)

# ========================================
# FRONTEND (para CORS e Sanctum)
# ========================================
FRONTEND_URL=https://seu-dominio-frontend.com  # 🔴 URL do FRONTEND (com HTTPS!)
SANCTUM_STATEFUL_DOMAINS=seu-dominio-frontend.com,www.seu-dominio-frontend.com

# ========================================
# SESSÃO (CRÍTICO para Sanctum funcionar)
# ========================================
SESSION_DRIVER=cookie  # 🔴 Pode ser 'cookie' ou 'database'
SESSION_LIFETIME=120
SESSION_DOMAIN=.seu-dominio.com  # 🔴 Com ponto inicial para incluir subdomínios
SESSION_SECURE_COOKIE=true  # 🔴 OBRIGATÓRIO com HTTPS
SESSION_SAME_SITE=lax  # ou 'none' se frontend/backend em domínios diferentes

# ========================================
# BANCO DE DADOS
# ========================================
DB_CONNECTION=mysql
DB_HOST=localhost  # ou IP do servidor de banco
DB_PORT=3306
DB_DATABASE=nome_do_banco_producao
DB_USERNAME=usuario_producao
DB_PASSWORD=senha_forte_aqui

# ========================================
# CACHE & QUEUE (Recomendado para produção)
# ========================================
CACHE_DRIVER=redis  # ou 'file' se não tiver Redis
QUEUE_CONNECTION=redis  # ou 'database' se não tiver Redis

# ========================================
# REDIS (se usar)
# ========================================
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# ========================================
# FACEBOOK OAUTH (se usar)
# ========================================
FACEBOOK_CLIENT_ID=seu_facebook_app_id
FACEBOOK_CLIENT_SECRET=seu_facebook_app_secret
FACEBOOK_REDIRECT_URI=https://seu-dominio-backend.com/api/auth/facebook/callback

# ========================================
# EMAIL (para recuperação de senha)
# ========================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.seuservidor.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@dominio.com
MAIL_PASSWORD=senha_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seu-dominio.com
MAIL_FROM_NAME="${APP_NAME}"

# ========================================
# LOGS
# ========================================
LOG_CHANNEL=daily  # Recomendado para produção
LOG_LEVEL=error  # 'debug' só em caso de problemas
```

---

## 2. Comandos Obrigatórios

### 2.1 Gerar APP_KEY (SE NÃO EXISTIR)

```bash
# 🔴 IMPORTANTE: Só execute se APP_KEY estiver vazio no .env
php artisan key:generate
```

**⚠️ ATENÇÃO:**

- Se você já tem um `APP_KEY` funcionando, **NÃO execute** este comando
- Gerar nova chave vai **invalidar todas as sessões** e dados criptografados

---

### 2.2 Otimizar Configurações (OBRIGATÓRIO)

```bash
# Limpar caches antigos
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Recriar caches otimizados para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Por quê?**

- `config:cache` - Carrega todas configs em 1 arquivo (mais rápido)
- `route:cache` - Pré-compila todas as rotas (mais rápido)
- `view:cache` - Pré-compila todos os Blade templates

---

### 2.3 Instalar/Atualizar Dependências

```bash
# Instalar dependências de produção (sem dev)
composer install --optimize-autoloader --no-dev

# Ou se já tem vendor instalado, atualizar:
composer update --optimize-autoloader --no-dev
```

**Flags importantes:**

- `--optimize-autoloader` - Cria autoloader otimizado (40% mais rápido)
- `--no-dev` - Não instala pacotes de desenvolvimento (mais leve)

---

### 2.4 Rodar Migrações (SE HOUVER NOVAS)

```bash
# Ver quais migrações faltam
php artisan migrate:status

# Executar migrações pendentes
php artisan migrate --force  # --force necessário em produção
```

**⚠️ BACKUP ANTES:** Sempre faça backup do banco antes de migrar!

---

### 2.5 Criar Storage Link (SE NÃO EXISTIR)

```bash
# Criar link simbólico de storage/app/public para public/storage
php artisan storage:link
```

**Para verificar se já existe:**

```bash
ls -la public/storage  # Se mostrar -> storage é um link
```

---

### 2.6 Ajustar Permissões (CRÍTICO)

```bash
# Dar permissão de escrita para o servidor web
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # ou usuário do seu servidor

# Se usar logs:
chmod -R 775 storage/logs
chown -R www-data:www-data storage/logs
```

**Usuários comuns por servidor:**

- Apache: `www-data` (Ubuntu/Debian) ou `apache` (CentOS)
- Nginx: `www-data` ou `nginx`
- Seu painel: pode ser seu usuário (ex: `cpanelusuario`)

---

## 3. Configurações do Sanctum

### 3.1 Verificar config/sanctum.php

Após deploy, verifique se o arquivo está correto:

```php
// config/sanctum.php

'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    Sanctum::currentApplicationUrlWithPort()
))),
```

**No .env deve ter:**

```bash
SANCTUM_STATEFUL_DOMAINS=seu-dominio-frontend.com,www.seu-dominio-frontend.com
```

---

### 3.2 Verificar config/cors.php

```php
// config/cors.php

'paths' => ['api/*', 'sanctum/csrf-cookie'],

'allowed_origins' => [env('FRONTEND_URL')],  // ou ['*'] temporariamente para testar

'supports_credentials' => true,  // 🔴 OBRIGATÓRIO para Sanctum
```

**No .env deve ter:**

```bash
FRONTEND_URL=https://seu-dominio-frontend.com
```

---

### 3.3 Verificar Middleware Sanctum

Confirme em `app/Http/Kernel.php`:

```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

---

## 4. Configurações de Sessão

### 4.1 Configurar Session Driver

**Opção 1: Cookie (mais simples)**

```bash
# .env
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true  # HTTPS obrigatório
```

**Opção 2: Database (mais seguro)**

```bash
# .env
SESSION_DRIVER=database

# Criar tabela de sessões
php artisan session:table
php artisan migrate --force
```

**Opção 3: Redis (mais performático)**

```bash
# .env
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

### 4.2 Configurar Session Domain

```bash
# .env
SESSION_DOMAIN=.seu-dominio.com  # Com ponto inicial!
```

**Exemplos:**

- Backend: `api.dominio.com` + Frontend: `app.dominio.com` → `SESSION_DOMAIN=.dominio.com`
- Backend: `dominio.com/api` + Frontend: `dominio.com` → `SESSION_DOMAIN=dominio.com`

---

### 4.3 Configurar SameSite

```bash
# .env
SESSION_SAME_SITE=lax  # Mesmo domínio/subdomínio
# ou
SESSION_SAME_SITE=none  # Domínios diferentes (requer SESSION_SECURE_COOKIE=true)
```

---

## 5. Testes Pós-Deploy

### 5.1 Testar Rota de Health Check

```bash
# Teste básico
curl https://seu-dominio-backend.com/api/health
# Deve retornar: {"status":"ok"}

# Teste com autenticação (se tiver rota protegida)
curl https://seu-dominio-backend.com/api/user -H "Authorization: Bearer seu-token"
```

---

### 5.2 Testar CORS e CSRF

```bash
# Do navegador, abra o console (F12) no frontend e execute:
fetch('https://seu-dominio-backend.com/sanctum/csrf-cookie', {
  credentials: 'include'
}).then(() => console.log('CSRF cookie obtido!'))
```

**Deve:**

- ✅ Retornar status 204
- ✅ Criar cookie `XSRF-TOKEN`

---

### 5.3 Testar Login

No frontend, tente fazer login:

```javascript
// Deve funcionar com as novas telas LoginView.vue e RegisterView.vue
```

**Verifique:**

- ✅ Login com email/senha funciona
- ✅ Cookies são criados (`laravel_session`, `XSRF-TOKEN`)
- ✅ Redirecionamento por role funciona (admin/trader)
- ✅ Facebook OAuth funciona (se configurado)

---

### 5.4 Testar Registro

Tente criar nova conta:

**Verifique:**

- ✅ Registro com senha forte funciona
- ✅ Password strength indicator funciona
- ✅ Validações de backend funcionam
- ✅ Usuário é criado no banco

---

### 5.5 Verificar Logs

```bash
# Ver últimos erros
tail -n 50 storage/logs/laravel.log

# Monitorar em tempo real (Ctrl+C para sair)
tail -f storage/logs/laravel.log
```

---

## 6. Troubleshooting

### ⚠️ ERRO CRÍTICO ENCONTRADO NO SEU PROJETO

### ❌ Erro: "The POST method is not supported for route api/sanctum/login. Supported methods: OPTIONS."

**Causa:** Cache de rotas desatualizado após mudanças em `routes/api.php`

**✅ SOLUÇÃO IMEDIATA:**

```bash
# EXECUTE ESTES COMANDOS NO SERVIDOR:
php artisan route:clear
php artisan config:clear

# Em produção, recrie os caches:
php artisan route:cache
php artisan config:cache
```

**📋 Verificação:**

```bash
# Ver se a rota foi registrada
php artisan route:list | grep -i "login"

# Deve aparecer:
# POST api/login ................. SanctumAuthController@login
# POST api/sanctum/login ......... SanctumAuthController@login
```

**🔍 Por que aconteceu?**

- As rotas foram atualizadas em `routes/api.php`
- Mas o Laravel ainda estava usando o cache antigo
- **SEMPRE** limpe o cache após alterar rotas!

---

### ❌ Erro: "CSRF token mismatch"

**Causa:** Configuração de sessão/CORS incorreta

**Soluções:**

```bash
# 1. Verificar SESSION_DOMAIN no .env
SESSION_DOMAIN=.seu-dominio.com  # Com ponto!

# 2. Verificar SESSION_SECURE_COOKIE
SESSION_SECURE_COOKIE=true  # Obrigatório com HTTPS

# 3. Limpar cache
php artisan config:clear
php artisan config:cache

# 4. Verificar SANCTUM_STATEFUL_DOMAINS
SANCTUM_STATEFUL_DOMAINS=seu-frontend.com
```

---

### ❌ Erro: "The POST method is not supported for route api/sanctum/login"

**Causa:** Cache de rotas desatualizado ou rota não registrada

**Soluções:**

```bash
# 1. Limpar cache de rotas (SEMPRE após mudanças em routes/)
php artisan route:clear
php artisan config:clear

# 2. Verificar se rota existe
php artisan route:list | grep -i login

# 3. Verificar routes/api.php
# Deve ter FORA do middleware auth:sanctum:
Route::post('/login', [SanctumAuthController::class, 'login']);
Route::post('/sanctum/login', [SanctumAuthController::class, 'login']); // Alias

# 4. Recriar cache (somente em produção)
php artisan route:cache
php artisan config:cache
```

---

### ❌ Erro: "Unauthenticated" (401)

**Causa:** Sanctum não reconhece frontend como stateful

**Soluções:**

```bash
# 1. Verificar FRONTEND_URL no .env
FRONTEND_URL=https://seu-frontend.com  # SEM barra no final!

# 2. Verificar cors.php
'allowed_origins' => [env('FRONTEND_URL')],
'supports_credentials' => true,

# 3. Limpar cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

### ❌ Erro: "Access to XMLHttpRequest blocked by CORS"

**Causa:** CORS não configurado

**Soluções:**

```bash
# 1. Adicionar frontend ao CORS
# config/cors.php
'allowed_origins' => [env('FRONTEND_URL')],

# 2. Ou temporariamente (TESTE APENAS):
'allowed_origins' => ['*'],

# 3. Verificar credentials
'supports_credentials' => true,

# 4. Limpar cache
php artisan config:cache
```

---

### ❌ Erro: "500 Internal Server Error"

**Causa:** Vários motivos possíveis

**Soluções:**

```bash
# 1. Verificar logs
tail -n 100 storage/logs/laravel.log

# 2. Verificar permissões
chmod -R 775 storage bootstrap/cache

# 3. Verificar .env
# - APP_KEY está preenchido?
# - Credenciais de banco corretas?
# - APP_DEBUG=true temporariamente para ver erro

# 4. Recriar caches
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

---

### ❌ Cookies não estão sendo salvos

**Causa:** Configuração de sessão/domínio

**Soluções:**

```bash
# 1. Verificar SESSION_DOMAIN
SESSION_DOMAIN=.dominio.com  # Deve incluir ambos backend e frontend

# 2. Verificar HTTPS
SESSION_SECURE_COOKIE=true  # Obrigatório com HTTPS

# 3. Verificar SameSite
SESSION_SAME_SITE=lax  # ou 'none' se domínios totalmente diferentes

# 4. Limpar cache
php artisan config:clear
php artisan config:cache
```

---

### ❌ Facebook OAuth não funciona

**Causa:** Configuração do Facebook App

**Soluções:**

```bash
# 1. Verificar .env
FACEBOOK_CLIENT_ID=seu_id
FACEBOOK_CLIENT_SECRET=seu_secret
FACEBOOK_REDIRECT_URI=https://seu-backend.com/api/auth/facebook/callback

# 2. Verificar Facebook App Settings:
# - Valid OAuth Redirect URIs deve incluir a URL do callback
# - App deve estar em produção (não modo desenvolvimento)
# - Domínio deve estar na whitelist do Facebook

# 3. Verificar HTTPS
# Facebook OAuth REQUER HTTPS em produção
```

---

## 7. Rollback

Se algo der errado, como voltar:

### 7.1 Rollback de Código

```bash
# Via Git (se usar)
git log --oneline -10  # Ver últimos commits
git revert <hash_do_commit_problema>
git push origin main

# Ou fazer checkout do commit anterior
git checkout <hash_do_commit_bom>
```

---

### 7.2 Rollback de Migração

```bash
# Ver migrações executadas
php artisan migrate:status

# Voltar 1 migração
php artisan migrate:rollback --step=1 --force

# Voltar todas migrações do último batch
php artisan migrate:rollback --force

# Voltar TUDO (cuidado!)
php artisan migrate:reset --force
```

---

### 7.3 Restaurar Backup do Banco

```bash
# MySQL
mysql -u usuario -p nome_banco < backup.sql

# PostgreSQL
psql -U usuario -d nome_banco < backup.sql
```

---

## 📋 Checklist Final - Copy & Paste

Use esta lista após cada deploy:

```bash
# ========================================
# 1. CONECTAR NO SERVIDOR
# ========================================
ssh usuario@seu-servidor.com
cd /caminho/do/backend

# ========================================
# 2. BACKUP (SEMPRE!)
# ========================================
mysqldump -u usuario -p nome_banco > backup_$(date +%Y%m%d_%H%M%S).sql

# ========================================
# 3. ATUALIZAR CÓDIGO (se não for automático)
# ========================================
git pull origin main

# ========================================
# 4. INSTALAR DEPENDÊNCIAS
# ========================================
composer install --optimize-autoloader --no-dev

# ========================================
# 5. CONFIGURAR .ENV (verificar manualmente)
# ========================================
nano .env  # ou vi, vim, etc
# Verificar: APP_URL, FRONTEND_URL, SESSION_*, DB_*, SANCTUM_*

# ========================================
# 6. LIMPAR CACHES
# ========================================
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# ========================================
# 7. OTIMIZAR CACHES
# ========================================
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ========================================
# 8. MIGRAÇÕES (se houver)
# ========================================
php artisan migrate:status
php artisan migrate --force

# ========================================
# 9. PERMISSÕES
# ========================================
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# ========================================
# 10. STORAGE LINK (se necessário)
# ========================================
php artisan storage:link

# ========================================
# 11. RESTART SERVIÇOS (se necessário)
# ========================================
# Apache
sudo systemctl restart apache2

# Nginx + PHP-FPM
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm  # ou sua versão

# Queue Worker (se usar)
php artisan queue:restart

# ========================================
# 12. TESTAR
# ========================================
# Abrir frontend e testar:
# - Login
# - Registro
# - Facebook OAuth
# - Rotas protegidas

# ========================================
# 13. MONITORAR LOGS
# ========================================
tail -f storage/logs/laravel.log
# Ctrl+C para sair
```

---

## 🎯 Resumo das Variáveis .env Críticas

| Variável                   | Valor Exemplo             | Obrigatório?     |
| -------------------------- | ------------------------- | ---------------- |
| `APP_ENV`                  | `production`              | ✅ Sim           |
| `APP_DEBUG`                | `false`                   | ✅ Sim           |
| `APP_KEY`                  | `base64:...`              | ✅ Sim           |
| `APP_URL`                  | `https://api.dominio.com` | ✅ Sim           |
| `FRONTEND_URL`             | `https://app.dominio.com` | ✅ Sim           |
| `SANCTUM_STATEFUL_DOMAINS` | `app.dominio.com`         | ✅ Sim           |
| `SESSION_DRIVER`           | `cookie` ou `database`    | ✅ Sim           |
| `SESSION_DOMAIN`           | `.dominio.com`            | ✅ Sim           |
| `SESSION_SECURE_COOKIE`    | `true`                    | ✅ Sim (HTTPS)   |
| `SESSION_SAME_SITE`        | `lax` ou `none`           | ✅ Sim           |
| `DB_*`                     | Credenciais do banco      | ✅ Sim           |
| `FACEBOOK_*`               | Credenciais OAuth         | ⚠️ Se usar OAuth |

---

## ⏱️ Tempo Estimado

| Etapa           | Tempo         |
| --------------- | ------------- |
| Configurar .env | 5-10 min      |
| Rodar comandos  | 2-5 min       |
| Testar          | 5-10 min      |
| **TOTAL**       | **15-25 min** |

---

## 📞 Em Caso de Problemas

1. **Verifique logs:** `tail -n 100 storage/logs/laravel.log`
2. **Teste endpoints:** Use Postman ou cURL
3. **Revise .env:** Todas variáveis preenchidas?
4. **Limpe caches:** `php artisan config:clear && php artisan config:cache`
5. **Se persistir:** Ative `APP_DEBUG=true` temporariamente

---

**Criado em:** 15/10/2025  
**Versão:** 1.0  
**Status:** ✅ Completo  
**Compatível com:** Laravel 10/11 + Sanctum + Nova Autenticação
