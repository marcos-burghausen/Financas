# 🚨 PROBLEMA ENCONTRADO - SESSION_DOMAIN null!

## ❌ Erro Real

Você executou os comandos e vi o problema:

```bash
session .....
domain ...................................................................................................................................... null
secure ...................................................................................................................................... null
```

**O `SESSION_DOMAIN` está NULL em produção!** 🔴

Isso causa o erro 405 porque:

1. Frontend tenta fazer requisição
2. Backend não reconhece o domínio (domain=null)
3. Middleware Sanctum bloqueia
4. Retorna 405

---

## ✅ SOLUÇÃO IMEDIATA

### No Coolify → Backend → Environment Variables:

**ADICIONE estas variáveis (estão faltando!):**

```bash
SESSION_DOMAIN=.burghausen.dev
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

**IMPORTANTE:** O `SESSION_DOMAIN` deve ter o **ponto inicial**: `.burghausen.dev`

---

## 🔍 Verificação do Config Atual

Você mostrou:

```
domain ...... null   ← 🔴 PROBLEMA!
secure ...... null   ← 🔴 PROBLEMA!
```

**Deveria ser:**

```
domain ...... .burghausen.dev
secure ...... true
```

---

## 📝 Variáveis Que FALTAM no Coolify

Adicione estas NO COOLIFY (não no .env local):

| Variável                   | Valor                               |
| -------------------------- | ----------------------------------- |
| `SESSION_DOMAIN`           | `.burghausen.dev`                   |
| `SESSION_SECURE_COOKIE`    | `true`                              |
| `SESSION_SAME_SITE`        | `lax`                               |
| `FRONTEND_URL`             | `https://mrfinancas.burghausen.dev` |
| `SANCTUM_STATEFUL_DOMAINS` | `mrfinancas.burghausen.dev`         |

---

## 🚀 Passo a Passo no Coolify

1. **Abra Coolify**
2. **Backend → Environment Variables**
3. **Clique em "+ Add Variable"**
4. **Adicione cada variável acima**
5. **Clique em "Save"**
6. **Clique em "Restart"**
7. **Aguarde reiniciar**
8. **Execute novamente no terminal:**

```bash
php artisan config:clear
php artisan config:cache
php artisan config:show session
```

**Agora deve mostrar:**

```
domain ...... .burghausen.dev  ← ✅
secure ...... true             ← ✅
```

---

## 🧪 Testar Após Adicionar Variáveis

1. **Restart no Coolify**
2. **Limpar cache:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

3. **Verificar:**

```bash
php artisan config:show session | grep domain
php artisan config:show session | grep secure
```

4. **Testar no frontend** ✅

---

## 📋 Por Que Aconteceu?

Você tem um `.env` local com essas variáveis, mas **o Coolify não lê o arquivo `.env` do repositório**!

O Coolify usa as **Environment Variables** configuradas no painel.

---

## ⚠️ IMPORTANTE

**Arquivo `.env` no repositório ≠ Variáveis no Coolify**

- `.env` no repo: Usado localmente (Docker Compose)
- Environment Variables no Coolify: Usado em produção

**Você precisa configurar as variáveis NO COOLIFY!**

---

## 📊 Resumo

| Item                       | Status Atual | Precisa Ser                            |
| -------------------------- | ------------ | -------------------------------------- |
| `SESSION_DOMAIN`           | ❌ null      | ✅ `.burghausen.dev`                   |
| `SESSION_SECURE_COOKIE`    | ❌ null      | ✅ `true`                              |
| `SESSION_SAME_SITE`        | ✅ lax       | ✅ lax                                 |
| `FRONTEND_URL`             | ❌ ?         | ✅ `https://mrfinancas.burghausen.dev` |
| `SANCTUM_STATEFUL_DOMAINS` | ❌ ?         | ✅ `mrfinancas.burghausen.dev`         |

---

## 🎯 Checklist Completo

- [ ] Abrir Coolify
- [ ] Ir em Backend → Environment Variables
- [ ] Adicionar `SESSION_DOMAIN=.burghausen.dev`
- [ ] Adicionar `SESSION_SECURE_COOKIE=true`
- [ ] Adicionar `SESSION_SAME_SITE=lax`
- [ ] Adicionar `FRONTEND_URL=https://mrfinancas.burghausen.dev`
- [ ] Adicionar `SANCTUM_STATEFUL_DOMAINS=mrfinancas.burghausen.dev`
- [ ] Salvar
- [ ] Clicar em Restart
- [ ] Aguardar restart completo
- [ ] Executar `php artisan config:clear`
- [ ] Executar `php artisan config:cache`
- [ ] Verificar `php artisan config:show session`
- [ ] Testar no frontend

---

**Criado em:** 16/10/2025 02:35  
**Status:** 🔴 SESSION_DOMAIN null em produção  
**Solução:** Adicionar variáveis no Coolify  
**Tempo:** 5 minutos
