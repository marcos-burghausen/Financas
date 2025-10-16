# 🚨 CORREÇÕES CRÍTICAS - .env Produção (Coolify)

## ⚠️ ATUALIZAÇÃO IMPORTANTE (16/10/2025)

**Redis causou erro de build!** Ver solução completa em: `ERRO_REDIS_BUILD_SOLUCAO.md`

**Correção aplicada:** Usar `file` ao invés de `redis` até instalar extensão PHP Redis.

---

## ❌ PROBLEMAS ENCONTRADOS no seu .env

### 🔴 CRÍTICOS (CORRIGIR IMEDIATAMENTE)

#### 1. **APP_ENV está ERRADO**

```bash
# ❌ ATUAL
APP_ENV=local

# ✅ CORRETO
APP_ENV=production
```

**Por quê?** Laravel usa `APP_ENV` para determinar o ambiente. Com `local`, ele pode expor informações sensíveis.

---

#### 2. **FRONTEND_URL e SANCTUM_STATEFUL_DOMAINS FALTANDO**

```bash
# ❌ FALTANDO - Sanctum NÃO VAI FUNCIONAR sem isso!

# ✅ ADICIONAR (no final do arquivo):
FRONTEND_URL=https://seu-dominio-frontend.com
SANCTUM_STATEFUL_DOMAINS=seu-dominio-frontend.com,www.seu-dominio-frontend.com
```

**⚠️ CRÍTICO:** Sem essas variáveis, o Sanctum não reconhece o frontend e retorna erro 401!

---

#### 3. **SESSION_DOMAIN FALTANDO**

```bash
# ❌ FALTANDO - Cookies NÃO funcionam entre domínios!

# ✅ ADICIONAR:
SESSION_DOMAIN=.burghausen.dev  # Com ponto inicial!
SESSION_SECURE_COOKIE=true      # HTTPS obrigatório
SESSION_SAME_SITE=lax           # ou 'none' se domínios diferentes
```

**Por quê?** Sem `SESSION_DOMAIN`, os cookies não são compartilhados entre backend e frontend.

---

#### 4. **FACEBOOK_REDIRECT_URI está com localhost**

```bash
# ❌ ATUAL (localhost não funciona em produção!)
FACEBOOK_REDIRECT_URI=http://localhost:4081/auth/callback

# ✅ CORRETO
FACEBOOK_REDIRECT_URI=https://mrfinancas.burghausen.dev/api/auth/callback
```

**⚠️ IMPORTANTE:**

- Usar a URL do BACKEND, não do frontend
- Deve ser a mesma URL configurada no Facebook App

---

#### 5. **SESSION_DRIVER e CACHE_DRIVER - MANTIDOS COMO FILE** ⚠️

```bash
# ✅ MANTER file por enquanto
SESSION_DRIVER=file
CACHE_DRIVER=file
```

**⚠️ IMPORTANTE:**

- **NÃO USE redis** até instalar a extensão PHP Redis!
- Causou erro de build: "Class Redis not found"
- Ver solução completa em: `ERRO_REDIS_BUILD_SOLUCAO.md`

**Futuro:** Depois de instalar extensão Redis, pode mudar para `redis` (melhor performance).

---

### ⚠️ ALERTAS (Recomendado corrigir)

#### 7. **LOG_LEVEL muito verboso**

```bash
# ⚠️ ATUAL (gera logs demais)
LOG_LEVEL=debug

# ✅ RECOMENDADO
LOG_LEVEL=error  # ou 'warning'
```

---

#### 8. **TOKEN_EXPIRES_IN muito curto**

```bash
# ⚠️ ATUAL (1 dia)
TOKEN_EXPIRES_IN=1

# ✅ RECOMENDADO
TOKEN_EXPIRES_IN=30  # 30 dias
```

---

## ✅ ARQUIVO .env CORRIGIDO COMPLETO

Copie e cole este conteúdo no Coolify:

```bash
# ========================================
# AMBIENTE
# ========================================
APP_NAME="Mr Finanças"
APP_ENV=production                    # ← CORRIGIDO (era 'local')
APP_DEBUG=false
APP_KEY=base64:mJIUN6KJn0qPnuc3rkwF1H15eTghBhd27p55k7Zu/e0=
APP_URL=https://mrfinancas.burghausen.dev

# ========================================
# FRONTEND & SANCTUM (CRÍTICO - FALTAVA!)
# ========================================
FRONTEND_URL=https://mrfinancas.burghausen.dev     # ← AJUSTE SE FRONTEND TIVER DOMÍNIO DIFERENTE
SANCTUM_STATEFUL_DOMAINS=mrfinancas.burghausen.dev # ← AJUSTE SE FRONTEND TIVER DOMÍNIO DIFERENTE

# ========================================
# SESSÃO (CRÍTICO - FALTAVA CONFIG!)
# ========================================
SESSION_DRIVER=file                   # ← file por enquanto (redis precisa extensão)
SESSION_LIFETIME=120
SESSION_DOMAIN=.burghausen.dev        # ← ADICIONADO (com ponto inicial!)
SESSION_SECURE_COOKIE=true            # ← ADICIONADO (HTTPS obrigatório)
SESSION_SAME_SITE=lax                 # ← ADICIONADO

# ========================================
# CACHE & QUEUE
# ========================================
CACHE_DRIVER=file                     # ← file por enquanto (redis precisa extensão)
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
# REDIS
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
FACEBOOK_REDIRECT_URI=https://mrfinancas.burghausen.dev/api/auth/callback  # ← CORRIGIDO

# ========================================
# JWT (se ainda usar)
# ========================================
JWT_SECRET=B4W42M5Z1LS2RXHfLs4X2ayzyqZ7Ji89EPf23unViVT7wXtYhObIAJRZKAUpmRDO
TOKEN_EXPIRES_IN=30                   # ← CORRIGIDO (era 1)

# ========================================
# LOGS
# ========================================
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error                       # ← CORRIGIDO (era 'debug')

# ========================================
# STORAGE
# ========================================
FILESYSTEM_DISK=local

# ========================================
# AWS (se usar)
# ========================================
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# ========================================
# PUSHER (se usar)
# ========================================
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# ========================================
# VITE (Frontend build)
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

## 🎯 COMO APLICAR NO COOLIFY

### Passo 1: Acessar Coolify

1. Acesse o painel do Coolify
2. Vá em **Applications** → Sua aplicação backend
3. Clique em **Environment Variables**

---

### Passo 2: Adicionar/Editar Variáveis

**Adicione estas variáveis NOVAS:**

| Variável                   | Valor                               | Tipo |
| -------------------------- | ----------------------------------- | ---- |
| `APP_ENV`                  | `production`                        | Text |
| `FRONTEND_URL`             | `https://mrfinancas.burghausen.dev` | Text |
| `SANCTUM_STATEFUL_DOMAINS` | `mrfinancas.burghausen.dev`         | Text |
| `SESSION_DOMAIN`           | `.burghausen.dev`                   | Text |
| `SESSION_SECURE_COOKIE`    | `true`                              | Text |
| `SESSION_SAME_SITE`        | `lax`                               | Text |
| `SESSION_DRIVER`           | `redis`                             | Text |
| `CACHE_DRIVER`             | `redis`                             | Text |
| `LOG_LEVEL`                | `error`                             | Text |
| `TOKEN_EXPIRES_IN`         | `30`                                | Text |

**Edite esta variável:**

| Variável                | Valor Novo                                            |
| ----------------------- | ----------------------------------------------------- |
| `FACEBOOK_REDIRECT_URI` | `https://mrfinancas.burghausen.dev/api/auth/callback` |

---

### Passo 3: Restart da Aplicação

No Coolify:

1. Clique em **Restart**
2. Aguarde o container reiniciar

---

### Passo 4: Executar Comandos (OBRIGATÓRIO)

No Coolify, abra o **Terminal** da aplicação e execute:

```bash
# Limpar caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Recriar caches otimizados
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar configurações
php artisan config:show session
php artisan config:show cors
```

---

## 🧪 TESTAR APÓS APLICAR

### Teste 1: Verificar Variáveis

```bash
# No terminal do Coolify:
php artisan tinker

# Execute:
config('app.env')         // Deve retornar: "production"
config('app.frontend_url') // Deve retornar: "https://mrfinancas.burghausen.dev"
config('session.domain')   // Deve retornar: ".burghausen.dev"
config('session.driver')   // Deve retornar: "redis"
```

---

### Teste 2: Testar Rota de Login

```bash
curl -X POST https://mrfinancas.burghausen.dev/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "seu_email@teste.com",
    "password": "sua_senha"
  }'
```

**Resposta esperada:**

```json
{
  "token": "1|...",
  "user": {...},
  "mesAno": "2025-10",
  "summary": {...}
}
```

---

### Teste 3: Testar CSRF Cookie

No navegador (F12 → Console), no seu frontend:

```javascript
fetch("https://mrfinancas.burghausen.dev/sanctum/csrf-cookie", {
  credentials: "include",
}).then(() => console.log("Cookie CSRF obtido!"));
```

**Deve retornar status 204** ✅

---

### Teste 4: Testar Login pelo Frontend

1. Abra https://mrfinancas.burghausen.dev
2. Tente fazer login
3. Verifique se:
   - ✅ Login funciona
   - ✅ Cookies são criados (`laravel_session`, `XSRF-TOKEN`)
   - ✅ Redirecionamento funciona

---

## ⚠️ ATENÇÃO: Frontend também precisa de config!

Seu **frontend** precisa apontar para o backend correto.

### Verificar frontend/.env:

```bash
# Deve ter:
VITE_API_BASE_URL=https://mrfinancas.burghausen.dev/api
VITE_APP_URL=https://mrfinancas.burghausen.dev
```

---

## 📋 CHECKLIST Final

Antes de testar, verifique:

- [ ] `APP_ENV=production` no .env
- [ ] `FRONTEND_URL` configurado
- [ ] `SANCTUM_STATEFUL_DOMAINS` configurado
- [ ] `SESSION_DOMAIN=.burghausen.dev` configurado
- [ ] `SESSION_DRIVER=file` configurado (⚠️ NÃO usar redis ainda!)
- [ ] `CACHE_DRIVER=file` configurado (⚠️ NÃO usar redis ainda!)
- [ ] `SESSION_SECURE_COOKIE=true` configurado
- [ ] `FACEBOOK_REDIRECT_URI` com URL de produção
- [ ] Aplicação reiniciada no Coolify
- [ ] Caches limpos (`php artisan config:clear`)
- [ ] Caches recriados (`php artisan config:cache`)
- [ ] Rota de login testada
- [ ] CSRF cookie testado
- [ ] Login pelo frontend testado

---

## 🔥 SE TUDO FALHAR

### Rollback Rápido:

1. No Coolify, vá em **Deployments**
2. Clique em **Redeploy** no deploy anterior
3. Aguarde container reiniciar

---

## 📞 Problemas Comuns

### ❌ Ainda dá erro 401

**Verificar:**

```bash
php artisan config:show cors
php artisan config:show sanctum
```

**Deve mostrar:**

- `cors.allowed_origins` incluindo seu FRONTEND_URL
- `sanctum.stateful` incluindo seu domínio frontend

---

### ❌ Cookies não são salvos

**Verificar:**

- `SESSION_DOMAIN` tem ponto inicial? (`.burghausen.dev`)
- `SESSION_SECURE_COOKIE=true`?
- Ambos domínios usam HTTPS?

---

### ❌ CSRF token mismatch

**Solução:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

---

## 🎯 Resumo das Mudanças

| Item                       | Antes       | Depois            | Impacto        |
| -------------------------- | ----------- | ----------------- | -------------- |
| `APP_ENV`                  | `local`     | `production`      | 🔴 CRÍTICO     |
| `FRONTEND_URL`             | ❌ Faltando | ✅ Configurado    | 🔴 CRÍTICO     |
| `SANCTUM_STATEFUL_DOMAINS` | ❌ Faltando | ✅ Configurado    | 🔴 CRÍTICO     |
| `SESSION_DOMAIN`           | ❌ Faltando | `.burghausen.dev` | 🔴 CRÍTICO     |
| `SESSION_SECURE_COOKIE`    | ❌ Faltando | `true`            | 🔴 CRÍTICO     |
| `SESSION_DRIVER`           | `file`      | `redis`           | ⚠️ Performance |
| `CACHE_DRIVER`             | `file`      | `redis`           | ⚠️ Performance |
| `LOG_LEVEL`                | `debug`     | `error`           | ⚠️ Performance |
| `FACEBOOK_REDIRECT_URI`    | localhost   | produção          | 🔴 CRÍTICO     |

---

**Criado em:** 15/10/2025  
**Plataforma:** Coolify  
**Status:** 🔴 Correções Obrigatórias  
**Tempo estimado:** 10-15 minutos
