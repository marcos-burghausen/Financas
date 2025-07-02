🔙 [Retornar à documentação principal](../../README.md)

# Diagrama de Classes: Sistema de Finanças Pessoal

Este diagrama modela as entidades principais do sistema, seus atributos, métodos e relacionamentos, alinhados com o backend Laravel (modelos Eloquent) e o banco MySQL.

## Diagrama de Classes

<img width="710px" src="./img/diagrama de classes.svg" />

## Descrição

### Classes e Relacionamentos

- **User**: Representa o usuário, com autenticação (e-mail/senha, OAuth) e perfil (avatar, tema).
  - Relacionamentos: Possui múltiplas contas, transações, cartões, categorias e notificações.
- **Account**: Representa contas bancárias, com saldo e personalização (ícone, cor).
  - Relacionamentos: Vinculada a um usuário, usada em transações e cartões.
- **Transaction**: Representa lançamentos financeiros (despesas, receitas, cartões, estornos).
  - Relacionamentos: Vinculada a usuário, conta, cartão (opcional) e categoria.
- **Card**: Representa cartões de crédito, com limite e faturas.
  - Relacionamentos: Vinculada a usuário e conta, usada em transações, gera faturas.
- **Category**: Representa categorias/subcategorias, com hierarquia.
  - Relacionamentos: Vinculada a usuário, usada em transações, pode ter subcategorias.
- **Notification**: Representa configurações de notificações (vencimentos, metas).
  - Relacionamentos: Vinculada a usuário.
- **Invoice**: Representa faturas de cartões, com vencimento e total.
  - Relacionamentos: Vinculada a cartão.

### Notas Técnicas

- **Laravel**: Cada classe corresponde a um modelo Eloquent, com tabelas no MySQL.
  - Ex.: `User` mapeia para tabela `users`, com colunas `id`, `name`, `email`, etc.
  - Relacionamentos são implementados com métodos Eloquent (ex.: `hasMany`, `belongsTo`).
- **Atributos**: Refletem colunas do banco, com tipos compatíveis (ex.: `decimal` para valores monetários).
- **Métodos**: Representam ações principais (ex.: `create`, `update`, `delete`), implementadas como métodos nos modelos ou controladores.
- **Soft Deletes**: Usado em todas as classes para evitar exclusão física (coluna `deleted_at`).
- **Criptografia**: Senhas e dados sensíveis (ex.: documentos) são criptografados.
