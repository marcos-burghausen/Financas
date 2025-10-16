# 🚨 ERRO RESOLVIDO - "Method Not Allowed"

## ❌ Problema Encontrado

```
exception: "Symfony\\Component\\HttpKernel\\Exception\\MethodNotAllowedHttpException"
message: "The POST method is not supported for route api/sanctum/login. Supported methods: OPTIONS."
```

---

## ✅ O Que Foi Feito

### 1. **Corrigido routes/api.php**

Adicionada rota que estava faltando:

```php
// ANTES (só tinha dentro de auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sanctum/login', [SanctumAuthController::class, 'login']); // ❌ ERRADO
});

// DEPOIS (fora do middleware)
Route::post('/login', [SanctumAuthController::class, 'login']);
Route::post('/sanctum/login', [SanctumAuthController::class, 'login']); // Alias para compatibilidade

Route::middleware('auth:sanctum')->group(function () {
    // Rotas protegidas aqui...
});
```

**Por quê?**

- Login deve ser **público** (não precisa estar autenticado para fazer login!)
- Estava dentro do grupo `auth:sanctum`, que exige token

---

### 2. **Limpado Cache de Rotas**

```bash
docker compose exec php php artisan route:clear
docker compose exec php php artisan config:clear
```

---

### 3. **Verificado Rotas Registradas**

```bash
docker compose exec php php artisan route:list | grep -E "POST.*login"
```

**Resultado:**

```
POST  api/login .................... SanctumAuthController@login
POST  api/sanctum/login ............ SanctumAuthController@login
```

✅ **Ambas rotas agora funcionam!**

---

## 🎯 Como Testar Agora

### Opção 1: Via Frontend (Recomendado)

1. Abra o frontend: http://localhost:4081
2. Tente fazer login com:
   - Email: seu_email@teste.com
   - Senha: sua_senha

**Deve funcionar!** ✅

---

### Opção 2: Via cURL

```bash
curl -X POST http://localhost:4080/api/login \
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
  "user": {
    "id": 1,
    "name": "Seu Nome",
    "email": "seu_email@teste.com",
    "type": "USER"
  },
  "mesAno": "2025-10",
  "summary": {...}
}
```

---

### Opção 3: Via Postman/Insomnia

**Request:**

- Method: `POST`
- URL: `http://localhost:4080/api/login`
- Body (JSON):

```json
{
  "email": "seu_email@teste.com",
  "password": "sua_senha"
}
```

---

## 📝 Atualizado Documentação

✅ Arquivo atualizado: `docs/POS_DEPLOY_BACKEND.md`

**Adicionada seção:**

- **6. Troubleshooting** → Erro "Method Not Allowed"
- Explicação da causa
- Solução passo a passo
- Como verificar

---

## 🚀 Próximos Passos para Deploy

### 1. **Commitar as Mudanças**

```bash
cd /home/rafa/projetos/github/Financas

git add backend/routes/api.php
git add docs/POS_DEPLOY_BACKEND.md
git commit -m "fix: adiciona rota pública /login e /sanctum/login para autenticação Sanctum"
git push origin main
```

---

### 2. **Após Deploy Automático no Servidor**

Conecte via SSH e execute:

```bash
# Limpar caches (OBRIGATÓRIO!)
php artisan route:clear
php artisan config:clear

# Recriar caches otimizados
php artisan route:cache
php artisan config:cache

# Verificar rotas
php artisan route:list | grep login
```

**Deve ver:**

```
POST api/login .................... SanctumAuthController@login
POST api/sanctum/login ............ SanctumAuthController@login
```

---

### 3. **Testar no Servidor**

```bash
# Substitua pelo seu domínio real
curl -X POST https://seu-dominio-backend.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "usuario@teste.com",
    "password": "senha123"
  }'
```

---

## ⚠️ IMPORTANTE para Produção

### Sempre após mudanças em `routes/`:

```bash
php artisan route:clear   # Limpa cache de rotas
php artisan route:cache   # Recria cache (só em produção)
```

### Sempre após mudanças em `config/`:

```bash
php artisan config:clear  # Limpa cache de config
php artisan config:cache  # Recria cache (só em produção)
```

---

## 🎉 Resumo

| Item                         | Status           |
| ---------------------------- | ---------------- |
| ❌ Erro "Method Not Allowed" | ✅ **RESOLVIDO** |
| 📝 routes/api.php            | ✅ **CORRIGIDO** |
| 🔄 Cache limpo               | ✅ **FEITO**     |
| 📚 Documentação atualizada   | ✅ **COMPLETO**  |
| 🧪 Rotas verificadas         | ✅ **OK**        |

---

## 📞 Se Ainda Houver Problemas

### 1. Verificar logs:

```bash
tail -n 50 backend/storage/logs/laravel.log
```

### 2. Modo debug (TEMPORÁRIO):

```bash
# backend/.env
APP_DEBUG=true  # ⚠️ Só para testar! Voltar para false depois!
```

### 3. Verificar se banco está acessível:

```bash
docker compose exec php php artisan migrate:status
```

---

**Criado em:** 15/10/2025  
**Status:** ✅ RESOLVIDO  
**Próximo passo:** Git push para deploy
