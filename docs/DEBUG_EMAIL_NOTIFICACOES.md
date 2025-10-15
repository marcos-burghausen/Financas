# 📧 Debug: Email de Notificações Não Está Chegando

## ✅ **Item 1: Cartões de Crédito adicionado ao menu!**

Agora você tem 10 itens no menu:

1. Admin
2. Trader
3. Dashboard
4. Contas
5. Receitas
6. Despesas
7. Categorias
8. **Cartões de Crédito** ← NOVO!
9. Notificações
10. Perfil

---

## 🔍 Problema: Email de teste diz "enviado" mas não chega

### Configuração Atual (✅ CORRETA):

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.smtp2go.com
MAIL_PORT=2525
MAIL_USERNAME=burghausen.com.br
MAIL_PASSWORD=TA23XsBLgj0nQvoS
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=mrfinancas@burghausen.com.br
```

---

## 🧪 Testes para Fazer

### Teste 1: Verificar se há cartão cadastrado

```bash
# Entrar no MySQL
docker compose exec mysql mysql -u root -p'root' financas

# Listar cartões
SELECT id, nome, limite, user_id, tipo_conta FROM contas WHERE tipo_conta = 'Cartão de Crédito';

# Se não houver nenhum, o teste de notificação retorna erro
```

**Solução**: Cadastre um cartão de crédito em `/contas`.

---

### Teste 2: Testar envio de email manualmente via Tinker

```bash
# Entrar no Tinker do Laravel
docker compose exec php php artisan tinker

# Dentro do Tinker, execute:
>>> $user = App\Models\User::find(1);
>>> $user->notify(new App\Notifications\LimiteCartaoNotification('Teste', 1000, 500, 50));

# Pressione Enter e aguarde
# Se der erro, copie a mensagem de erro
```

---

### Teste 3: Testar SMTP diretamente

```bash
# Instalar ferramenta de teste
docker compose exec php apt-get update && apt-get install -y swaks

# Testar envio direto
docker compose exec php swaks \
  --to seu-email@gmail.com \
  --from mrfinancas@burghausen.com.br \
  --server mail.smtp2go.com:2525 \
  --auth LOGIN \
  --auth-user burghausen.com.br \
  --auth-password TA23XsBLgj0nQvoS \
  --header "Subject: Teste SMTP" \
  --body "Teste de envio direto"
```

Se **não** funcionar → Problema no SMTP2GO  
Se funcionar → Problema no Laravel

---

### Teste 4: Verificar logs do Laravel em tempo real

```bash
# Terminal 1: Monitorar logs
docker compose exec php tail -f storage/logs/laravel.log

# Terminal 2: Enviar teste de notificação pelo frontend
# Clique em "Enviar Teste" no frontend

# Procure por:
# - "Mail" ou "email" nos logs
# - Erros de conexão
# - Timeout
```

---

### Teste 5: Verificar se o email está indo para SPAM

- ✅ Verifique sua caixa de **SPAM**
- ✅ Procure por emails de: `mrfinancas@burghausen.com.br`
- ✅ Procure nos últimos 10 minutos

---

## 🔧 Possíveis Causas

### Causa 1: SMTP2GO bloqueou o domínio

- SMTP2GO pode estar bloqueando emails de teste
- Verifique o dashboard do SMTP2GO: https://www.smtp2go.com/
- Veja se há alertas ou bloqueios

### Causa 2: Laravel está em modo Queue

```bash
# Verificar se há fila configurada
docker compose exec php cat .env | grep QUEUE

# Se retornar QUEUE_CONNECTION=database ou redis:
docker compose exec php php artisan queue:work

# Deixe rodando em um terminal separado
```

### Causa 3: Credenciais inválidas

```bash
# Testar autenticação
docker compose exec php php artisan tinker

>>> Mail::raw('Teste', function($msg) {
...   $msg->to('seu-email@gmail.com')->subject('Teste Laravel');
... });

# Se der erro de autenticação, as credenciais estão erradas
```

---

## 🎯 Solução Rápida: Usar Mailtrap para Testes

Se o SMTP2GO não estiver funcionando, use o Mailtrap (gratuito):

1. **Criar conta**: https://mailtrap.io/
2. **Copiar credenciais** da inbox
3. **Atualizar .env**:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario_mailtrap
MAIL_PASSWORD=sua_senha_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=test@test.com
```

4. **Restart backend**:

```bash
docker compose restart php
```

5. **Testar novamente** - Os emails aparecerão no Mailtrap!

---

## 📝 Checklist de Debug

- [ ] Verifique se tem cartão cadastrado no banco
- [ ] Teste envio via Tinker
- [ ] Verifique logs do Laravel
- [ ] Verifique pasta SPAM
- [ ] Teste SMTP diretamente com swaks
- [ ] Verifique dashboard do SMTP2GO
- [ ] Verifique se QUEUE está configurada (e se sim, rode queue:work)
- [ ] Se nada funcionar, mude para Mailtrap

---

## 🆘 Me avise:

1. **O que você vê nos logs** quando tenta enviar?
2. **Tem cartão cadastrado** no sistema?
3. **Qual email você está usando** para receber (Gmail, Outlook, etc)?
4. **Já verificou SPAM?**

---

**Status**: Aguardando testes do usuário  
**Próximo passo**: Verificar logs e testar Tinker
