# 🚨 SOLUÇÃO URGENTE - Erro Redis no Build

## ❌ Erro Encontrado

```
Error: Class "Redis" not found
at vendor/laravel/framework/src/Illuminate/Redis/Connectors/PhpRedisConnector.php:79
```

**Causa:** Configurou `CACHE_DRIVER=redis` mas a extensão PHP Redis não está instalada no container.

---

## ✅ SOLUÇÃO RÁPIDA (Escolha UMA)

### 🎯 Opção 1: Usar File Cache (MAIS RÁPIDO - Recomendado)

**No Coolify, mude estas variáveis:**

```bash
# Voltar para file (funciona sem extensão Redis)
CACHE_DRIVER=file
SESSION_DRIVER=file
```

**Depois:**

1. Clique em **Restart** no Coolify
2. O build vai funcionar! ✅

**Prós:**

- ✅ Build funciona imediatamente
- ✅ Não precisa alterar Dockerfile
- ✅ Funciona para aplicações pequenas/médias

**Contras:**

- ⚠️ Performance menor que Redis
- ⚠️ Não ideal para alta concorrência

---

### 🎯 Opção 2: Instalar Redis Extension no Dockerfile

**Edite o Dockerfile do backend:**

Procure pela linha que instala extensões PHP e adicione `redis`:

```dockerfile
# ANTES (provavelmente tem algo assim)
RUN apt-get update && apt-get install -y \
    php-mysql \
    php-mbstring \
    ...

# DEPOIS (adicionar redis)
RUN apt-get update && apt-get install -y \
    php-mysql \
    php-mbstring \
    php-redis \
    ...
```

**OU se usar `docker-php-ext-install`:**

```dockerfile
# Adicionar esta linha
RUN pecl install redis && docker-php-ext-enable redis
```

**Depois:**

1. Commit e push
2. Aguardar rebuild
3. Vai funcionar com Redis! ✅

**Prós:**

- ✅ Melhor performance
- ✅ Ideal para produção

**Contras:**

- ⚠️ Precisa modificar Dockerfile
- ⚠️ Build mais demorado (primeira vez)

---

### 🎯 Opção 3: Usar Database para Sessão/Cache

**No Coolify:**

```bash
CACHE_DRIVER=database
SESSION_DRIVER=database
```

**Depois, no Terminal do Coolify:**

```bash
# Criar tabelas necessárias
php artisan cache:table
php artisan session:table
php artisan migrate --force
```

**Prós:**

- ✅ Não precisa Redis
- ✅ Melhor que file
- ✅ Funciona bem

**Contras:**

- ⚠️ Mais queries no banco
- ⚠️ Precisa criar tabelas

---

## 🚀 RECOMENDAÇÃO IMEDIATA

### Para você agora (Coolify):

**Use Opção 1 - File Cache** para desbloquear o deploy:

1. **Vá no Coolify** → Backend → Environment Variables
2. **Mude:**
   - `CACHE_DRIVER` = `file`
   - `SESSION_DRIVER` = `file`
3. **Clique em Restart**
4. **Aguarde deploy** (vai funcionar!)

---

## 📝 Atualização do .env de Produção

```bash
# ========================================
# CACHE & SESSION (CORRIGIDO)
# ========================================
CACHE_DRIVER=file              # ← CORRIGIDO (era redis)
SESSION_DRIVER=file            # ← CORRIGIDO (era redis)
QUEUE_CONNECTION=database      # ← OK (database não precisa de Redis)

# ========================================
# REDIS (manter, mas não usar para cache/session por enquanto)
# ========================================
REDIS_HOST=mgwc00ogwc4sk4o0cg8g8cw4
REDIS_PORT=6379
REDIS_PASSWORD=uOo6RtxEHSjC2IpC5fLCc7qM45pk8hUE4M0Y2WRudaZ1vNVWBbqk6ZNPMYCx
```

---

## 🔧 Se Quiser Usar Redis no Futuro

### Passo 1: Verificar se Redis Extension Está Disponível

```bash
# No terminal do container (após deploy)
php -m | grep redis
```

**Se aparecer "redis":** ✅ Extensão instalada, pode usar  
**Se NÃO aparecer:** ❌ Precisa instalar no Dockerfile

---

### Passo 2: Instalar no Dockerfile (Nixpacks)

Como você usa **Coolify com Nixpacks**, crie um arquivo na raiz do backend:

**`backend/nixpacks.toml`:**

```toml
[phases.setup]
aptPkgs = ['php-redis']

[phases.install]
cmds = ['composer install --no-dev --optimize-autoloader']

[phases.build]
cmds = ['php artisan config:clear', 'php artisan route:clear', 'php artisan view:clear']
```

**Depois:**

1. Commit esse arquivo
2. Push
3. Coolify vai reinstalar com Redis

---

## ⚠️ OUTROS PROBLEMAS NO LOG

### 1. PSR-4 Warning (não crítico)

```
Class App\Http\Controllers\LancamentoController located in ./app/Http/Controllers/RevenueController.php
does not comply with psr-4 autoloading standard
```

**Causa:** Arquivo `RevenueController.php` tem classe `LancamentoController` dentro.

**Solução:**

```bash
# Renomear o arquivo para corresponder ao nome da classe
mv app/Http/Controllers/RevenueController.php app/Http/Controllers/LancamentoController.php
```

**OU** renomear a classe dentro do arquivo para `RevenueController`.

---

### 2. NPM Vulnerabilities (não crítico)

```
3 vulnerabilities (1 low, 2 moderate)
```

**Solução (opcional):**

```bash
cd backend
npm audit fix
```

---

## 📋 CHECKLIST - Deploy Corrigido

- [ ] Mudar `CACHE_DRIVER=file` no Coolify
- [ ] Mudar `SESSION_DRIVER=file` no Coolify
- [ ] Manter outras variáveis do documento anterior:
  - [ ] `APP_ENV=production`
  - [ ] `FRONTEND_URL=https://mrfinancas.burghausen.dev`
  - [ ] `SANCTUM_STATEFUL_DOMAINS=mrfinancas.burghausen.dev`
  - [ ] `SESSION_DOMAIN=.burghausen.dev`
  - [ ] `SESSION_SECURE_COOKIE=true`
  - [ ] `SESSION_SAME_SITE=lax`
  - [ ] `FACEBOOK_REDIRECT_URI=https://mrfinancas.burghausen.dev/api/auth/callback`
- [ ] Clicar em **Restart** no Coolify
- [ ] Aguardar deploy completo
- [ ] Testar login no frontend

---

## 🎯 .env FINAL CORRIGIDO (Copiar e Colar)

```bash
# ========================================
# AMBIENTE
# ========================================
APP_NAME="Mr Finanças"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:mJIUN6KJn0qPnuc3rkwF1H15eTghBhd27p55k7Zu/e0=
APP_URL=https://mrfinancas.burghausen.dev

# ========================================
# FRONTEND & SANCTUM
# ========================================
FRONTEND_URL=https://mrfinancas.burghausen.dev
SANCTUM_STATEFUL_DOMAINS=mrfinancas.burghausen.dev

# ========================================
# SESSÃO (CORRIGIDO - file ao invés de redis)
# ========================================
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=.burghausen.dev
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# ========================================
# CACHE (CORRIGIDO - file ao invés de redis)
# ========================================
CACHE_DRIVER=file
QUEUE_CONNECTION=database
BROADCAST_DRIVER=log

# ========================================
# BANCO DE DADOS
# ========================================
DB_CONNECTION=mysql
DB_HOST=ek0ccwggss8c0wg0s4c84cgk
DB_PORT=3306
DB_DATABASE=default
DB_USERNAME=mysql
DB_PASSWORD=nwnr550L4KsKZV5bKCax22AlExoTrer51DH9en5reyQK04MkKU7EzsTQfZSREAZT

# ========================================
# REDIS (disponível mas não usado por enquanto)
# ========================================
REDIS_HOST=mgwc00ogwc4sk4o0cg8g8cw4
REDIS_PORT=6379
REDIS_PASSWORD=uOo6RtxEHSjC2IpC5fLCc7qM45pk8hUE4M0Y2WRudaZ1vNVWBbqk6ZNPMYCx

# ========================================
# EMAIL
# ========================================
MAIL_MAILER=smtp
MAIL_HOST=mail.smtp2go.com
MAIL_PORT=2525
MAIL_USERNAME=burghausen.com.br
MAIL_PASSWORD=TA23XsBLgj0nQvoS
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=mrfinancas@burghausen.com.br
MAIL_FROM_NAME="${APP_NAME}"

# ========================================
# FACEBOOK OAUTH
# ========================================
FACEBOOK_CLIENT_ID=398220822617196
FACEBOOK_CLIENT_SECRET=15b73e73b06d9fe7376feae60478dd23
FACEBOOK_REDIRECT_URI=https://mrfinancas.burghausen.dev/api/auth/callback

# ========================================
# JWT
# ========================================
JWT_SECRET=B4W42M5Z1LS2RXHfLs4X2ayzyqZ7Ji89EPf23unViVT7wXtYhObIAJRZKAUpmRDO
TOKEN_EXPIRES_IN=30

# ========================================
# LOGS
# ========================================
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# ========================================
# STORAGE
# ========================================
FILESYSTEM_DISK=local

# ========================================
# AWS
# ========================================
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# ========================================
# PUSHER
# ========================================
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# ========================================
# VITE
# ========================================
VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# ========================================
# COOLIFY / NIXPACKS
# ========================================
NIXPACKS_PHP_ROOT_DIR=/app/public
NIXPACKS_PHP_FALLBACK_PATH=/index.php

# ========================================
# OUTROS
# ========================================
MEMCACHED_HOST=127.0.0.1
```

---

## 📊 Resumo das Mudanças

| Variável                             | Valor Anterior | Valor Novo | Motivo                           |
| ------------------------------------ | -------------- | ---------- | -------------------------------- |
| `CACHE_DRIVER`                       | `redis`        | `file`     | 🔴 Redis extension não instalada |
| `SESSION_DRIVER`                     | `redis`        | `file`     | 🔴 Redis extension não instalada |
| _(demais variáveis do doc anterior)_ | -              | ✅         | Correções Sanctum                |

---

## ✅ Após Deploy Funcionar

1. **Testar login** no frontend
2. **Verificar logs** (não deve ter erros Redis)
3. **Se tudo OK**, depois você pode:
   - Instalar Redis extension no Dockerfile
   - Mudar de volta para `redis` (melhor performance)

---

## 🆘 Se Ainda Falhar

**Envie o novo log de erro** e eu ajudo!

---

**Criado em:** 16/10/2025 02:22  
**Status:** 🔴 URGENTE - Deploy Bloqueado  
**Solução:** Mudar para file cache  
**Tempo para corrigir:** 2 minutos
