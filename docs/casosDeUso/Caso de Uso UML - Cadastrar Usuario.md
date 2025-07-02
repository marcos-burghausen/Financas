🔙 [Retornar à documentação principal](../../README.md)

# Caso de Uso: Cadastrar Usuário

## Diagrama de Caso de Uso

<img width="710px" src="./img/cadastrar usuario.svg" />

```mermaid
@startuml

actor "Usuário" as Usuario
actor "ProvedorOAuth" as Provedor

( Cadastrar Usuário )

Usuario --> ( Cadastrar Usuário )
Usuario --> Provedor : usa

@enduml
```

## Descrição

Permite que um usuário se cadastre no sistema usando e-mail/senha ou OAuth (Facebook, Google, LinkedIn), com verificação de e-mail para cadastros tradicionais.

## Atores

- **Primário**: Usuário
- **Secundário**: Provedor OAuth

## Pré-condições

- Usuário não possui conta no sistema.
- Provedores OAuth estão configurados.

## Pós-condições

- Usuário é cadastrado em `users` com status `email_verified` (OAuth: true, tradicional: false).
- E-mail de verificação é enviado (tradicional).
- E-mail de boas-vindas é enviado (OAuth).
- Log de cadastro é registrado em `logs`.

## Fluxo Principal

1. Usuário acessa a tela de cadastro no frontend.
2. Usuário escolhe método:
   - **Tradicional**:
     - Preenche formulário `{ email, password, name }`.
     - Frontend envia `POST /api/register`.
     - Backend valida: e-mail único (`users.email`), senha forte (mínimo 8 caracteres, letras e números).
     - Cria usuário em `users` com `email_verified = false`.
     - Envia e-mail de verificação via AWS SES (endpoint `GET /api/verify-email?token`).
     - Frontend exibe "Verifique seu e-mail".
     - Usuário clica no link, backend atualiza `email_verified = true`.
   - **OAuth**:
     - Usuário clica em botão OAuth.
     - Frontend redireciona para provedor via Laravel Socialite.
     - Provedor retorna `{ email, name }`.
     - Backend cria/atualiza usuário em `users` com `email_verified = true`.
     - Envia e-mail de boas-vindas.
     - Frontend redireciona para o dashboard.
3. Backend registra cadastro em `logs` (ação: `register`, detalhes: e-mail, timestamp).

## Fluxos Alternativos

- **A1**: E-mail já registrado → Backend retorna erro 422 ("E-mail já registrado").
- **A2**: Link de verificação inválido → Backend retorna erro 422 ("Link inválido") e oferece reenvio via `POST /api/resend-verification`.
- **A3**: Falha na autenticação OAuth → Backend retorna erro 500 ("Falha na autenticação OAuth").

## Regras de Negócio

- E-mails são únicos (`users.email` tem índice único).
- Senhas: `^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$`.
- Links de verificação expiram em 24 horas.
- Usuários OAuth não precisam de verificação adicional.

## Notas Técnicas

- **Frontend**: Vue.js com Vuetify, Axios.
- **Backend**: Laravel com Socialite, Eloquent (modelo `User`).
- **Endpoints**:
  - `POST /api/register`: `{ email: string, password: string, name: string }` → `{ message: string }`.
  - `GET /api/verify-email?token`: Valida token.
  - `POST /api/resend-verification`: `{ email: string }` → Reenvia e-mail.
- **Banco**: Tabela `users` (`email`, `password`, `name`, `email_verified`); `logs` (`action`, `details`).
- **Integrações**:
  - Laravel Socialite (OAuth).
  - AWS SES (Email Service).
  - Laravel Mail com filas (Redis).
- **Segurança**: Bcrypt, tokens temporários, HTTPS.
