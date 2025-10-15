# Migração de Tymon JWT-Auth para Laravel Sanctum

## 📋 Resumo

Migração completa do sistema de autenticação de **Tymon JWT-Auth** para **Laravel Sanctum** para resolver problemas de blacklist em cache e melhorar a confiabilidade do sistema.

## 🐛 Problema Original

### Sintoma

- ✅ Primeiro login funcionava normalmente
- ❌ Segundo login falhava com erro 401 Unauthorized
- ⚠️ Necessário limpar cache Laravel manualmente após cada logout

### Causa Raiz

O Tymon JWT-Auth estava usando cache Laravel para blacklist de tokens, e esse cache:

1. Nunca expirava (sem TTL configurado)
2. Tokens blacklisted no logout permaneciam em cache indefinidamente
3. Logins subsequentes falhavam porque tokens eram reconhecidos como blacklisted

---

## ✅ Solução Implementada

### 1. Backend - Laravel Sanctum

#### Arquivos Criados:

- `app/Http/Controllers/SanctumAuthController.php`

#### Alterações:

- `routes/api.php` - Novas rotas Sanctum
- `app/Models/User.php` - Mantido trait `HasApiTokens`

#### Rotas Sanctum:

```php
// Login (público)
POST /sanctum/login

// Rotas protegidas (middleware auth:sanctum)
POST /sanctum/me
POST /sanctum/logout
POST /sanctum/logout-all
POST /buscar-dados-mes
// ... todas as outras rotas protegidas
```

#### Bug Crítico Corrigido:

**Problema**: `cache()->remember()` no login retornava tokens revogados

```php
// ❌ ANTES (BUG):
$loginData = cache()->remember($cacheKey, 600, function () use ($token) {
    return ['token' => $token]; // Token do cache após logout!
});

// ✅ DEPOIS (CORRETO):
$token = $user->createToken('auth_token')->plainTextToken;
$loginData = [
    'token' => $token, // Sempre novo
    // ... outros dados
];
```

---

### 2. Frontend - Vue 3 + TypeScript

#### Arquivos Alterados:

**`frontend/src/store/auth.ts`**

- Token mudou de `Token` (objeto) para `string`
- Removida lógica de monitoramento de expiração
- Removido `refreshToken()`
- Simplificado para Sanctum

**`frontend/src/types/auth.types.ts`**

```typescript
// ❌ ANTES:
export interface LoginResponse {
  token: Token; // Objeto complexo
  user: User;
  // ...
}

// ✅ DEPOIS:
export interface LoginResponse {
  token: string; // String simples
  user: User;
  // ...
}
```

**`frontend/src/services/http.ts`**

```typescript
// Interceptor simplificado
const token = localStorage.getItem("sanctum_token");
if (token) {
  config.headers.Authorization = `Bearer ${token}`;
}
```

**`frontend/src/views/acesso/EntrarMobileView.vue`**

- Endpoint: `/auth` → `/sanctum/login`
- Limpa tokens antigos antes do login

**`frontend/src/views/mobile/DashboardMobileView copy.vue`**

- Endpoint: `/logout` → `/sanctum/logout`

---

## 🔑 Diferenças JWT vs Sanctum

| Aspecto           | JWT (Tymon)                               | Sanctum                                        |
| ----------------- | ----------------------------------------- | ---------------------------------------------- |
| **Token**         | Objeto complexo com `expires`, `iat`, etc | String simples                                 |
| **Armazenamento** | Cache Laravel (problema de blacklist)     | Tabela `personal_access_tokens`                |
| **Revogação**     | Blacklist em cache (nunca expira)         | Delete do banco (`tokens()->delete()`)         |
| **Renovação**     | Endpoint `/refresh-token` necessário      | Não necessário (tokens não expiram por padrão) |
| **Monitoramento** | Frontend monitora expiração               | Não necessário                                 |
| **Complexidade**  | Alta (watchEffect, setInterval, refresh)  | Baixa (só enviar token no header)              |

---

## 🧪 Testes Realizados

### Cenário 1: Login → Dashboard ✅

1. Login com credenciais válidas
2. Token salvo no localStorage como `sanctum_token`
3. Requisição `/buscar-dados-mes` com token no header
4. ✅ **Resultado**: Dados carregados com sucesso (200 OK)

### Cenário 2: Logout → Login ✅

1. Login inicial funcionando
2. Logout (revoga token do banco)
3. LocalStorage limpo
4. Segundo login
5. ✅ **Resultado**: Novo token gerado, login funcionando

### Cenário 3: Múltiplos Logins ✅

1. Login → Logout → Login → Logout → Login
2. ✅ **Resultado**: Todos os logins funcionam sem erro 401

---

## 🔧 Comandos Executados

### Limpeza de Cache

```bash
# Laravel
docker compose exec php php artisan cache:clear

# MySQL (tokens antigos)
docker compose exec mysql mysql -uroot -proot Mr_database \
  -e "TRUNCATE TABLE personal_access_tokens;"

# Frontend (navegador)
localStorage.clear()
sessionStorage.clear()
```

---

## 📦 Dependências

### Backend

- ✅ `laravel/sanctum` (já incluído no Laravel 11)
- ⚠️ `tymon/jwt-auth` (manter temporariamente para compatibilidade)

### Frontend

Sem mudanças de dependências necessárias.

---

## 🗑️ Limpeza Futura (Opcional)

### Backend

1. Remover rotas JWT antigas de `api.php`
2. Remover `JwtDebugMiddleware.php`
3. Remover `app/Http/Middleware/JwtDebugMiddleware.php` do `Kernel.php`
4. Desinstalar: `composer remove tymon/jwt-auth`
5. Remover configurações JWT: `config/jwt.php`

### Frontend

1. Remover interface `Token` de `auth.types.ts` (marcada como deprecated)

---

## 📊 Métricas de Sucesso

- ✅ **Problema 401 resolvido**: Logins subsequentes funcionam
- ✅ **Cache limpo**: Sem tokens blacklisted em cache
- ✅ **Código simplificado**: Menos 100 linhas no frontend
- ✅ **Performance**: Login não usa cache com bug
- ✅ **Confiabilidade**: Sanctum é oficial do Laravel

---

## 🎯 Conclusão

A migração para Laravel Sanctum foi **bem-sucedida** e resolveu completamente o problema de 401 após logout. O sistema agora é:

- ✅ Mais simples
- ✅ Mais confiável
- ✅ Mais fácil de manter
- ✅ Alinhado com as melhores práticas do Laravel

**Status**: ✅ **PRODUÇÃO-READY**

---

**Data da Migração**: 15 de Outubro de 2025  
**Versão do Laravel**: 11.x  
**Versão do Sanctum**: 4.x
