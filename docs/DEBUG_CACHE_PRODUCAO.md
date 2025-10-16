# 🔥 LIMPAR CACHE URGENTE - Coolify

## ❌ Erro: 405 Method Not Allowed em /api/me/permissions

**Causa:** Cache de rotas/config antigo sendo usado em produção!

---

## ✅ SOLUÇÃO IMEDIATA

### Opção 1: Via Terminal do Coolify (RECOMENDADO)

1. **Abra Coolify** → Sua aplicação backend
2. **Clique em "Terminal"** ou "Execute Command"
3. **Cole e execute TODOS estes comandos:**

```bash
# 1. Limpar TODOS os caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Verificar se rota existe
php artisan route:list | grep -i "permissions"

# 3. Recriar caches otimizados (IMPORTANTE!)
php artisan config:cache
php artisan route:cache

# 4. Restart do PHP-FPM/Apache (se necessário)
# Coolify geralmente faz isso automaticamente
```

**Deve aparecer:**

```
GET|HEAD  api/me/permissions ......... RoleController@myPermissions
```

---

### Opção 2: Redeployar (SE OPÇÃO 1 NÃO FUNCIONAR)

1. **No Coolify** → Backend
2. **Clique em "Redeploy"**
3. **Aguarde build completo**
4. **Teste novamente**

---

## 🔍 Verificar se Rota Existe

```bash
# No terminal do Coolify:
php artisan route:list | grep "me/permissions"
```

**Deve retornar:**

```
GET|HEAD  api/me/permissions ......... RoleController@myPermissions
```

**Se NÃO aparecer:** A rota não está no código de produção! Precisa fazer push do código atualizado.

---

## 🚨 SE A ROTA NÃO EXISTIR

Significa que o código com a rota nova ainda não foi para produção!

### Verificar routes/api.php local vs produção:

**Deve ter esta linha (local):**

```php
Route::get('/me/permissions', [RoleController::class, 'myPermissions']);
```

**Se tiver local mas não em produção:**

1. **Commit e Push:**

```bash
cd /home/rafa/projetos/github/Financas
git add backend/routes/api.php
git commit -m "fix: adiciona rota GET /me/permissions"
git push origin main
```

2. **Aguardar deploy automático**

3. **Executar comandos de cache** (Opção 1 acima)

---

## 📝 Script Completo Copy & Paste

**Execute no Terminal do Coolify:**

```bash
#!/bin/bash
echo "=== Limpando caches ==="
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "=== Verificando rota /me/permissions ==="
php artisan route:list | grep "me/permissions"

echo ""
echo "=== Recriando caches ==="
php artisan config:cache
php artisan route:cache

echo ""
echo "=== CONCLUÍDO ==="
echo "Teste agora no frontend!"
```

---

## 🧪 Testar Após Limpar Cache

### No navegador (F12 → Console):

```javascript
// Teste direto da rota
fetch("https://mrfinancas.burghausen.dev/api/me/permissions", {
  method: "GET",
  headers: {
    Authorization: "Bearer SEU_TOKEN_AQUI",
    Accept: "application/json",
  },
  credentials: "include",
})
  .then((r) => r.json())
  .then((d) => console.log(d))
  .catch((e) => console.error(e));
```

**Resposta esperada (200 OK):**

```json
{
  "roles": ["USER"],
  "permissions": [...]
}
```

---

## ⚠️ Problema com Método HTTP

Se a rota existe mas ainda dá 405, pode ser que o frontend esteja chamando com método errado!

### Verificar no código frontend:

**Procure por:** `fetchMyPermissions` ou chamada à rota `/me/permissions`

**Deve ser:**

```typescript
// ✅ CORRETO
http.get("/me/permissions");

// ❌ ERRADO
http.post("/me/permissions");
```

---

## 🔍 Debug Avançado

Se ainda não funcionar, verifique:

### 1. Logs do Laravel

```bash
# No Coolify, terminal:
tail -n 100 storage/logs/laravel.log
```

### 2. Ver todas as rotas registradas

```bash
php artisan route:list --path=me
```

### 3. Verificar middleware

```bash
php artisan route:list | grep "me/permissions" -A 2
```

**Deve mostrar:**

- Method: GET|HEAD
- Middleware: auth:sanctum
- Controller: RoleController@myPermissions

---

## 📋 Checklist de Resolução

- [ ] Executar `php artisan config:clear`
- [ ] Executar `php artisan cache:clear`
- [ ] Executar `php artisan route:clear`
- [ ] Executar `php artisan view:clear`
- [ ] Executar `php artisan route:list | grep permissions`
- [ ] Verificar se rota aparece
- [ ] Executar `php artisan config:cache`
- [ ] Executar `php artisan route:cache`
- [ ] Testar no frontend
- [ ] Se não funcionar, fazer Redeploy no Coolify

---

## 🎯 Resumo

| Problema                   | Solução                               |
| -------------------------- | ------------------------------------- |
| **405 Method Not Allowed** | Cache antigo                          |
| **Solução Rápida**         | Limpar caches via terminal            |
| **Se persistir**           | Redeploy no Coolify                   |
| **Se ainda falhar**        | Verificar se código foi para produção |

---

## 🚀 Após Resolver

**Adicione ao processo de deploy:**

Sempre que fizer deploy, execute:

```bash
php artisan config:clear && php artisan route:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache
```

Ou crie um arquivo `deploy.sh`:

```bash
#!/bin/bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
echo "Cache limpo e recriado!"
```

---

**Criado em:** 16/10/2025 02:30  
**Status:** 🔴 Cache Antigo em Produção  
**Tempo para resolver:** 2-3 minutos
