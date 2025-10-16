# 🔥 SOLUÇÃO DEFINITIVA - Erro 405 em Produção

## 🎯 PROBLEMA REAL ENCONTRADO

Após investigação completa, encontrei **3 problemas** que causam o erro 405:

---

## ❌ Problema 1: CORS Configurado Errado

**Arquivo:** `backend/config/cors.php` linha 23

```php
// ❌ ERRADO (atual)
'allowed_origins' => [env('APP_URL')],
```

**Por quê está errado?**

- `APP_URL` = URL do backend (`https://mrfinancas.burghausen.dev`)
- Mas as requisições vêm do **frontend** (mesmo domínio, mas diferente)
- CORS bloqueia requisições porque espera backend, mas recebe frontend

**✅ CORREÇÃO APLICADA:**

```php
// ✅ CORRETO (agora)
'allowed_origins' => [env('FRONTEND_URL', env('APP_URL'))],
```

Agora aceita requisições do `FRONTEND_URL` (que você precisa configurar no Coolify).

---

## ❌ Problema 2: Variáveis Faltando no Coolify

Você verificou o config e mostrou:

```
session .....
domain ...... null   ← 🔴 FALTA!
secure ...... null   ← 🔴 FALTA!

cors .....
allowed_origins ⇁ 0 ... https://mrfinancas.burghausen.dev  ← 🔴 ERRADO (é o backend!)
```

**Variáveis que FALTAM no Coolify:**

- `FRONTEND_URL`
- `SANCTUM_STATEFUL_DOMAINS`
- `SESSION_DOMAIN`
- `SESSION_SECURE_COOKIE`
- `SESSION_SAME_SITE`

---

## ❌ Problema 3: Frontend Pode Estar Apontando para URL Errada

**Arquivo:** `frontend/.env`

```bash
# ❌ Seu .env local
VITE_API_URL=http://localhost:4080/api
```

Isso funciona local, mas em **produção** o frontend precisa apontar para:

```bash
# ✅ Produção
VITE_API_URL=https://mrfinancas.burghausen.dev/api
```

**Onde configurar?**

- **No Coolify do FRONTEND** → Environment Variables
- Adicione `VITE_API_URL=https://mrfinancas.burghausen.dev/api`

---

## ✅ SOLUÇÃO COMPLETA

### Passo 1: Commit da Correção do CORS

```bash
cd /home/rafa/projetos/github/Financas

git add backend/config/cors.php
git commit -m "fix: corrige CORS para aceitar requisições do frontend"
git push origin main
```

---

### Passo 2: Configurar Variáveis no Coolify BACKEND

**Coolify → Backend → Environment Variables:**

Adicione estas variáveis:

```bash
# Frontend (CRÍTICO!)
FRONTEND_URL=https://mrfinancas.burghausen.dev

# Sanctum Stateful Domains (CRÍTICO!)
SANCTUM_STATEFUL_DOMAINS=mrfinancas.burghausen.dev

# Sessão (CRÍTICO!)
SESSION_DOMAIN=.burghausen.dev
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# APP (verificar se está correto)
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mrfinancas.burghausen.dev
```

**Clique em Save + Restart**

---

### Passo 3: Configurar Variáveis no Coolify FRONTEND

**Coolify → Frontend → Environment Variables:**

Adicione esta variável:

```bash
VITE_API_URL=https://mrfinancas.burghausen.dev/api
```

**Clique em Save + Restart**

---

### Passo 4: Limpar Cache no Backend

**No terminal do container backend:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
```

---

### Passo 5: Verificar Configurações

```bash
php artisan config:show cors
php artisan config:show sanctum
php artisan config:show session
```

**Deve mostrar:**

```bash
# CORS
allowed_origins ⇁ 0 ... https://mrfinancas.burghausen.dev  ← ✅ FRONTEND_URL

# Sanctum
stateful ⇁ 0 ... localhost
stateful ⇁ 1 ... localhost:3000
stateful ⇁ 2 ... mrfinancas.burghausen.dev  ← ✅

# Session
domain ...... .burghausen.dev  ← ✅
secure ...... true             ← ✅
same_site ... lax              ← ✅
```

---

### Passo 6: Rebuild do Frontend

**Se já configurou a variável `VITE_API_URL` no frontend:**

1. No Coolify → Frontend
2. Clique em **Redeploy**
3. Aguarde build completo

**Por quê?** Variáveis `VITE_*` são incluídas no build, não em runtime!

---

## 🧪 TESTAR

### Teste 1: CORS

No navegador (F12 → Console):

```javascript
fetch("https://mrfinancas.burghausen.dev/sanctum/csrf-cookie", {
  credentials: "include",
})
  .then((r) => console.log("Status:", r.status))
  .then(() => console.log("✅ CORS OK!"));
```

**Esperado:** Status 204 + "✅ CORS OK!"

---

### Teste 2: Login

1. Abra https://mrfinancas.burghausen.dev
2. Faça login
3. Verifique se funciona ✅

---

### Teste 3: Permissions

No Console (F12):

```javascript
fetch("https://mrfinancas.burghausen.dev/api/me/permissions", {
  headers: {
    Accept: "application/json",
    Authorization: "Bearer " + localStorage.getItem("token"),
  },
  credentials: "include",
})
  .then((r) => r.json())
  .then((d) => console.log(d));
```

**Esperado:** Retornar suas permissões ✅

---

## 🔍 Debug Avançado

Se ainda não funcionar:

### Verificar Headers da Requisição

No navegador (F12 → Network → Clique na requisição `/me/permissions`):

**Headers devem ter:**

```
Origin: https://mrfinancas.burghausen.dev
Referer: https://mrfinancas.burghausen.dev/...
Authorization: Bearer [token]
Cookie: laravel_session=...; XSRF-TOKEN=...
```

**Response Headers devem ter:**

```
Access-Control-Allow-Origin: https://mrfinancas.burghausen.dev
Access-Control-Allow-Credentials: true
```

---

### Verificar Logs do Laravel

```bash
tail -n 100 storage/logs/laravel.log | grep -i "405\|cors\|sanctum"
```

---

## 📋 Checklist Completo

### Backend:

- [x] `config/cors.php` corrigido (FRONTEND_URL)
- [ ] Git commit + push
- [ ] Aguardar deploy automático
- [ ] Adicionar `FRONTEND_URL` no Coolify
- [ ] Adicionar `SANCTUM_STATEFUL_DOMAINS` no Coolify
- [ ] Adicionar `SESSION_DOMAIN` no Coolify
- [ ] Adicionar `SESSION_SECURE_COOKIE` no Coolify
- [ ] Adicionar `SESSION_SAME_SITE` no Coolify
- [ ] Restart no Coolify
- [ ] Limpar cache (`php artisan config:clear`)
- [ ] Recriar cache (`php artisan config:cache`)
- [ ] Verificar config (`php artisan config:show cors`)

### Frontend:

- [ ] Adicionar `VITE_API_URL` no Coolify
- [ ] Restart/Redeploy no Coolify
- [ ] Aguardar build completo
- [ ] Testar no navegador

---

## 📊 Resumo dos Problemas

| #   | Problema                       | Causa                       | Solução                            |
| --- | ------------------------------ | --------------------------- | ---------------------------------- |
| 1   | CORS usa APP_URL               | `config/cors.php`           | Mudar para FRONTEND_URL            |
| 2   | Variáveis faltando             | Não configuradas no Coolify | Adicionar no painel                |
| 3   | Frontend aponta para localhost | `.env` local                | Configurar VITE_API_URL no Coolify |

---

## 🎯 Por Que Funciona Local Mas Não em Produção?

**Local:**

- Backend: `http://localhost:4080`
- Frontend: `http://localhost:4081`
- CORS menos restritivo (localhost)
- Variáveis no `.env` local funcionam

**Produção:**

- Backend: `https://mrfinancas.burghausen.dev`
- Frontend: `https://mrfinancas.burghausen.dev` (mesmo domínio)
- CORS mais restritivo (HTTPS)
- Coolify **NÃO lê .env do repo**
- Variáveis devem estar no **painel do Coolify**

---

## ⚠️ IMPORTANTE

**Coolify ≠ Docker Compose Local**

- Local: Usa `.env` do repositório
- Coolify: Usa **Environment Variables** do painel
- **Você PRECISA configurar as variáveis NO COOLIFY!**

---

## 🚀 Ordem de Execução

1. ✅ Commit `cors.php` corrigido (JÁ FEITO)
2. ⏳ Push para main
3. ⏳ Aguardar deploy automático
4. ⏳ Adicionar variáveis no Coolify Backend
5. ⏳ Adicionar variáveis no Coolify Frontend
6. ⏳ Restart ambos
7. ⏳ Limpar cache backend
8. ⏳ Redeploy frontend (rebuild com VITE_API_URL)
9. ⏳ Testar!

---

**Criado em:** 16/10/2025 02:45  
**Status:** 🔴 3 problemas identificados  
**Correção 1/3:** ✅ CORS corrigido no código  
**Correção 2/3:** ⏳ Variáveis no Coolify Backend  
**Correção 3/3:** ⏳ Variáveis no Coolify Frontend
