# 🔍 Debug: Menu Lateral - Notificações e Perfil

## 🐛 Problema Reportado

Itens "Notificações" e "Perfil" não aparecem no menu lateral.

---

## ✅ Checklist de Verificação

### 1. Verificar Console do Browser (F12)

Abra o console (F12) e procure por logs:

```
🔍 MenuLateral - Base Items: 7
🔍 MenuLateral - Items com base: ['DashBoard', 'Contas', 'Receitas', 'Despesas', 'Categorias', 'Notificações', 'Perfil']
🎯 MenuLateral - Items finais: 7 (ou 8 ou 9 dependendo das roles)
```

**Se NÃO aparecer**: O componente não está sendo recarregado.

---

### 2. Limpar Cache do Browser

#### Chrome/Edge:

1. Pressione `Ctrl + Shift + Delete`
2. Selecione "Imagens e arquivos em cache"
3. Clique em "Limpar dados"
4. Ou use `Ctrl + F5` (hard refresh)

#### Firefox:

1. `Ctrl + Shift + Delete`
2. Marque "Cache"
3. Limpar

---

### 3. Verificar Código Fonte no Browser

1. Abra DevTools (F12)
2. Vá em "Sources" ou "Debugger"
3. Procure por `MenuLateral.vue`
4. Verifique se o array `baseItems` tem 7 itens:

```typescript
const baseItems = [
    { name: "DashBoard", ... },
    { name: "Contas", ... },
    { name: "Receitas", ... },
    { name: "Despesas", ... },
    { name: "Categorias", ... },
    { name: "Notificações", ... },  // ← DEVE ESTAR AQUI
    { name: "Perfil", ... },        // ← DEVE ESTAR AQUI
];
```

**Se estiver com apenas 5 itens**: O arquivo não foi recompilado.

---

### 4. Forçar Rebuild do Frontend

```bash
# Opção 1: Restart do container
docker compose restart node

# Opção 2: Rebuild completo
docker compose down
docker compose up -d --build

# Opção 3: Apenas frontend
docker compose up -d --force-recreate node
```

---

### 5. Verificar Rota no Router

Abra: `frontend/src/router/index.ts`

Deve conter:

```typescript
{
    path: "/configuracoes/notificacoes",
    name: "notificacoes",
    component: () => import("../views/configuracoes/NotificacoesView.vue"),
    meta: { auth: true }
},
{
    path: "/perfil",
    name: "perfil",
    component: () => import("../views/configuracoes/PerfilView.vue"),
    meta: { auth: true }
}
```

---

### 6. Teste Manual no Console do Browser

No console (F12), execute:

```javascript
// Verificar se as rotas existem
console.log($router.getRoutes().filter((r) => r.name === "notificacoes"));
console.log($router.getRoutes().filter((r) => r.name === "perfil"));

// Deve retornar os objetos de rota
```

---

### 7. Acessar Diretamente pela URL

Tente acessar diretamente:

```
http://localhost:4081/configuracoes/notificacoes
http://localhost:4081/perfil
```

**Se funcionar**: O problema é apenas no menu lateral  
**Se der 404**: As rotas não foram registradas

---

### 8. Verificar Scroll do Menu

Inspecione o elemento `.menu-lateral` no DevTools:

Deve ter:

```css
.menu-lateral {
  overflow-y: auto;
  overflow-x: hidden;
  height: 100%;
}
```

**Se não tiver `overflow-y: auto`**: O CSS não foi aplicado.

---

### 9. Contar Itens Visualmente

No menu lateral, conte quantos itens você vê:

- **5 itens**: Faltam Notificações + Perfil
- **6 itens**: Falta um deles
- **7 itens**: ✅ Todos básicos presentes
- **8 itens**: ✅ + Admin (para ADMIN/FULL)
- **9 itens**: ✅ + Admin + Trader

---

### 10. Verificar se Menu está Expandido

O menu lateral deve estar **expandido** para ver os nomes.

Se ver apenas ícones:

- Clique no ícone de hambúrguer/menu
- Ou verifique se `props.menuExpandido` está true

---

## 🔧 Soluções Rápidas

### Solução 1: Hard Refresh

```
Ctrl + Shift + R  (Linux/Windows)
Cmd + Shift + R   (Mac)
```

### Solução 2: Modo Anônimo

Abra o sistema em uma janela anônima:

```
Ctrl + Shift + N  (Chrome)
Ctrl + Shift + P  (Firefox)
```

Se funcionar em anônimo = problema de cache.

### Solução 3: Rebuild Forçado

```bash
cd /home/rafa/projetos/github/Financas
docker compose down
docker compose up -d --build
```

Aguarde 1-2 minutos para compilar.

---

## 📊 Debugging Avançado

### Ver Logs em Tempo Real

```bash
# Terminal 1: Logs do frontend
docker compose logs -f node

# Terminal 2: Fazer mudança no código
# Você deve ver: "[vite] hmr update..."
```

### Verificar se Arquivo Existe

```bash
# No container do frontend
docker compose exec node ls -la /app/src/views/configuracoes/

# Deve listar:
# NotificacoesView.vue
# PerfilView.vue
```

### Verificar se Componente Compila

```bash
docker compose exec node cat /app/src/components/MenuLateral.vue | grep -A 5 "baseItems"

# Deve mostrar array com 7 itens
```

---

## 🎯 Teste Definitivo

Execute este teste no console do browser (F12):

```javascript
// 1. Verificar rotas
console.log(
  "Rotas:",
  $router
    .getRoutes()
    .map((r) => r.name)
    .filter((n) => n)
);

// 2. Tentar navegar
$router.push("/perfil");
$router.push("/configuracoes/notificacoes");

// Se funcionar, o problema é só o menu lateral
```

---

## ✅ Resultado Esperado

Após todas as correções, você deve ver:

### Menu Lateral (Usuário Normal):

1. ✅ DashBoard
2. ✅ Contas
3. ✅ Receitas
4. ✅ Despesas
5. ✅ Categorias
6. ✅ **Notificações** 🔔
7. ✅ **Perfil** 👤

### Menu Lateral (Admin):

1-7. (itens acima) 8. ✅ **Admin** 👑

### Menu Lateral (Trader):

1-7. (itens acima) 8. ✅ **Trader** 📈

### Menu Lateral (FULL):

1-9. (todos os itens)

---

## 🆘 Se Nada Funcionar

1. **Restart completo**:

```bash
docker compose down
docker compose up -d
```

2. **Aguardar 2 minutos** para tudo subir

3. **Limpar cache do browser** (Ctrl + Shift + Delete)

4. **Hard refresh** (Ctrl + F5)

5. **Verificar console** (F12) para erros

6. **Tirar screenshot** do menu e do console para análise

---

**Status**: Aguardando teste do usuário  
**Última atualização**: 15/10/2025 21:40
