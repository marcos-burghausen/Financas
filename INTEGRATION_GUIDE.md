# 🚀 GUIA RÁPIDO - INTEGRAÇÃO DE NOVAS VIEWS

## Estrutura Padrão

```typescript
// 1. Imports (sempre estes)
import { useRouter } from "vue-router";
import { useAuthStore } from "@/store/auth";
import { useUserStore } from "@/store/user";
import { useToastStore } from "@/store/toast";
import { useService } from "@/services/use.service"; // Seu service

// 2. Setup
const router = useRouter();
const authStore = useAuthStore();
const userStore = useUserStore();
const toastStore = useToastStore();

// 3. Estado reativo
const data = ref({});
const loading = ref(false);

// 4. Tratamento de erro (padrão)
const errorTranslations: { [key: string]: string } = {
  "Erro em inglês": "Erro em português",
};

function translateError(error: string): string {
  for (const [en, pt] of Object.entries(errorTranslations)) {
    if (error.includes(en)) return pt;
  }
  return error;
}

// 5. Handler principal (try/catch padrão)
async function handleAction() {
  try {
    loading.value = true;
    const response = await useService.method();

    // Processar response

    toastStore.addToast({
      message: "Sucesso!",
      color: "success",
      timeout: 2000,
      icon: "mdi-check-circle",
    });
  } catch (error: any) {
    let errorMessage = "Erro";
    if (error.response?.data?.message)
      errorMessage = error.response.data.message;
    else if (error.message) errorMessage = error.message;

    toastStore.addToast({
      message: translateError(errorMessage),
      color: "error",
      timeout: 4000,
      icon: "mdi-alert-circle",
    });
  } finally {
    loading.value = false;
  }
}
```

## Criar um Service

```typescript
// src/services/myfeature.service.ts
import http from "./http";

export interface MyRequest {
  id?: number;
  name: string;
}

export interface MyResponse {
  success: string;
  data?: any;
}

class MyFeatureService {
  async list(): Promise<any[]> {
    try {
      const response = await http.get("/myfeature");
      return response.data.data || response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  async create(data: MyRequest): Promise<MyResponse> {
    try {
      const response = await http.post("/myfeature", data);
      return response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  async update(id: number, data: MyRequest): Promise<MyResponse> {
    try {
      const response = await http.put(`/myfeature/${id}`, data);
      return response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  async delete(id: number): Promise<void> {
    try {
      await http.delete(`/myfeature/${id}`);
    } catch (error) {
      throw this.handleError(error);
    }
  }

  private handleError(error: any): any {
    if (error.response?.data?.message) {
      return new Error(error.response.data.message);
    }
    return error;
  }
}

export default new MyFeatureService();
```

## Criar Endpoint Backend

```php
// app/Http/Controllers/MyFeatureController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyFeatureController extends Controller
{
    public function index()
    {
        // Retornar lista
        return response()->json([
            'data' => [],
            'success' => 'Listagem realizada'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string']
        ], [
            'name.required' => 'Nome é obrigatório'
        ]);

        // Criar recurso
        return response()->json([
            'success' => 'Criado com sucesso',
            'data' => []
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string']
        ]);

        // Atualizar
        return response()->json([
            'success' => 'Atualizado com sucesso'
        ]);
    }

    public function destroy($id)
    {
        // Deletar
        return response()->json([
            'success' => 'Deletado com sucesso'
        ]);
    }
}
```

## Registrar Rota

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('myfeature', MyFeatureController::class);
});
```

## Checklist para Nova View

- [ ] Criar componente Vue em `/views`
- [ ] Criar service em `/services`
- [ ] Criar controller Laravel
- [ ] Registrar rota em `routes/api.php`
- [ ] Adicionar ao router.ts
- [ ] Testar endpoint com curl
- [ ] Integrar em componente Vue
- [ ] Testar fluxo completo
- [ ] Adicionar ao layout/menu

## Testes

```bash
# Backend
curl -X GET http://localhost:4080/api/myfeature \
  -H "Authorization: Bearer {token}"

# Frontend
npm run dev  # Vite auto-recarrega

# TypeScript
npm run type-check
```

## Padrão de Resposta

**Success (200):**

```json
{
  "success": "Operação realizada",
  "data": {}
}
```

**Error (422):**

```json
{
  "message": "Validation error",
  "errors": {
    "field": ["Error message"]
  }
}
```

**Auth Error (401):**

```json
{
  "message": "Unauthenticated"
}
```

## Boas Práticas

✅ Sempre usar try/catch nos services
✅ Sempre traduzir mensagens de erro para português
✅ Sempre mostrar toast de feedback
✅ Sempre validar dados no backend
✅ Sempre usar loading states
✅ Sempre limpar timeouts/intervals
✅ Usar Pinia para state global
✅ Usar localStorage com cuidado (dados públicos)
✅ Sempre documentar tipos TypeScript

## Exemplo Completo

```vue
<template>
  <v-container>
    <h1>My Feature</h1>

    <v-list v-if="items.length">
      <v-list-item v-for="item in items" :key="item.id">
        {{ item.name }}
      </v-list-item>
    </v-list>

    <v-btn @click="handleCreate" :loading="loading"> Criar </v-btn>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useToastStore } from "@/store/toast";
import myService from "@/services/myfeature.service";

const items = ref([]);
const loading = ref(false);
const toastStore = useToastStore();

onMounted(async () => {
  try {
    items.value = await myService.list();
  } catch (error: any) {
    toastStore.addToast({
      message: error.message || "Erro ao carregar",
      color: "error",
    });
  }
});

async function handleCreate() {
  try {
    loading.value = true;
    await myService.create({ name: "New Item" });
    toastStore.addToast({ message: "Criado!", color: "success" });
    items.value = await myService.list();
  } catch (error: any) {
    toastStore.addToast({
      message: error.message,
      color: "error",
    });
  } finally {
    loading.value = false;
  }
}
</script>
```

---

**Use este guia para adicionar novas views rapidamente mantendo consistência!**
