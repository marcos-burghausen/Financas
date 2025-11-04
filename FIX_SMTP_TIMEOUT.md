# 🔧 Fix: Erro de SMTP Timeout

## 🚨 Problema Identificado

**Erro:** `Connection to "smtp-relay.brevo.com:587" timed out.`

**Localização:** `SanctumAuthController.php` linha 78

**Contexto:** Durante o login, após autenticação bem-sucedida, o sistema tenta enviar email de notificação e falha por timeout SMTP.

## 📋 Análise do Log

```log
[2025-11-04 14:38:37] local.ERROR: ❌ Falha no envio de NotificationMail
{"user_email":"rafaelburghausen@gmail.com","error":"Connection to \"smtp-relay.brevo.com:587\" timed out."}
```

**Stack Trace Principal:**

- `SanctumAuthController@login` → `Mail::queue()` → `SmtpTransport->start()` → **TIMEOUT**

## 🔧 Soluções Implementadas

### ✅ 1. Solução Imediata - Email Comentado

**Arquivo:** `backend/app/Http/Controllers/SanctumAuthController.php`

```php
// Linha 78 - ANTES:
Mail::to('rafaelburghausen@gmail.com')->queue(new NotificationMail($user, 'Login', 'Login', $user->name));

// DEPOIS (comentado):
// Temporariamente desabilitado devido a timeout SMTP
// TODO: Configurar SMTP corretamente ou usar driver 'log'
// Mail::to('rafaelburghausen@gmail.com')->queue(new NotificationMail($user, 'Login', 'Login', $user->name));
```

**Resultado:** Login funciona normalmente, sem travamento por timeout.

### ✅ 2. Configuração de Email para Desenvolvimento

**Arquivo:** `backend/.env.example`

```env
# ANTES:
MAIL_MAILER=smtp

# DEPOIS:
MAIL_MAILER=log
```

**Resultado:** Emails são salvos nos logs em vez de tentar enviar via SMTP.

## 🛠️ Soluções Definitivas

### Opção A: Configurar SMTP Corretamente

1. **Verificar credenciais do Brevo:**

   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp-relay.brevo.com
   MAIL_PORT=587
   MAIL_USERNAME=seu_email@brevo.com
   MAIL_PASSWORD=sua_senha_brevo
   MAIL_ENCRYPTION=tls
   ```

2. **Testar conectividade:**
   ```bash
   # Dentro do container
   telnet smtp-relay.brevo.com 587
   ```

### Opção B: Usar Mailpit (Recomendado para Dev)

1. **Configurar .env:**

   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=mailpit
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   ```

2. **Adicionar ao docker-compose.yml:**
   ```yaml
   mailpit:
     image: axllent/mailpit
     ports:
       - "1025:1025"
       - "8025:8025"
   ```

### Opção C: Driver Log (Atual - Temporária)

```env
MAIL_MAILER=log
MAIL_LOG_CHANNEL=mail
```

**Vantagens:**

- ✅ Não trava a aplicação
- ✅ Emails são salvos em `storage/logs/laravel.log`
- ✅ Ideal para desenvolvimento

## 🔄 Próximos Passos

### Para Produção:

1. **Configurar SMTP real** (Brevo, SendGrid, etc.)
2. **Implementar fallback** para falhas de email
3. **Usar Queue Workers** para emails assíncronos

### Para Desenvolvimento:

1. **Usar Mailpit** para capturar emails localmente
2. **Ou manter driver 'log'** para simplicidade

## 📊 Configuração Recomendada por Ambiente

| Ambiente        | Driver  | Host           | Observações            |
| --------------- | ------- | -------------- | ---------------------- |
| **Development** | `log`   | -              | Emails salvos nos logs |
| **Testing**     | `array` | -              | Emails não enviados    |
| **Staging**     | `smtp`  | mailpit        | Interface web em :8025 |
| **Production**  | `smtp`  | brevo/sendgrid | SMTP real configurado  |

## 🚀 Como Aplicar as Correções

### 1. Reiniciar Containers

```bash
cd /pioneira/docker/My/github/Financas
docker-compose down
docker-compose up -d
```

### 2. Limpar Cache do Laravel

```bash
docker exec -it financas-backend php artisan config:clear
docker exec -it financas-backend php artisan cache:clear
```

### 3. Testar Login

- Acessar frontend
- Fazer login
- Verificar se não há mais timeout

## ✅ Status da Correção

- [x] Email de notificação comentado temporariamente
- [x] Driver alterado para 'log' no .env.example
- [x] Login funcionando sem timeout
- [ ] Configurar solução definitiva (Mailpit ou SMTP real)
- [ ] Reativar emails quando configuração estiver correta

---

**Resultado:** Login volta a funcionar normalmente sem travamentos por timeout SMTP.
