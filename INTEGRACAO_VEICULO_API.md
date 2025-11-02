# Integração VeiculoView com Backend API

## Resumo da Implementação

Nesta sessão, foi completada a integração entre o frontend (VeiculoView.vue) e o backend API de gestão de veículos e manutenções.

## Mudanças Realizadas

### 1. **Criação do Serviço de API** (`frontend/src/services/veiculoService.ts`)

Novo arquivo com tipos TypeScript e serviços para comunicação com a API:

```typescript
// Interfaces TypeScript
- Veiculo: Estrutura de veículo com id, marca, modelo, placa, etc
- Manutencao: Estrutura de manutenção com veiculo_id, tipo, data, oficina, itens
- ManutencaoItem: Itens da manutenção com nome, descrição, quantidade, valor

// Serviços
- veiculoService: CRUD para veículos (create, read, update, delete)
- manutencaoService: CRUD para manutenções com suporte a items
```

**Endpoints utilizados:**

- `GET /api/veiculos` - Listar veículos
- `POST /api/veiculos` - Criar veículo
- `GET /api/veiculos/{id}` - Obter veículo
- `PUT /api/veiculos/{id}` - Atualizar veículo
- `DELETE /api/veiculos/{id}` - Deletar veículo
- `GET /api/manutencoes` - Listar manutenções
- `POST /api/manutencoes` - Criar manutenção
- `PUT /api/manutencoes/{id}` - Atualizar manutenção
- `DELETE /api/manutencoes/{id}` - Deletar manutenção

### 2. **Atualização da VeiculoView.vue**

#### Mudanças na estrutura:

- Removido: dados hardcoded (3 veículos, 5 manutenções)
- Adicionado: carregamento dinâmico via API
- Integrado: axios com interceptadores existentes do projeto

#### Ciclo de vida:

```
onMounted → loadData() → loadVeiculos() + loadManutencoes()
                      ↓
            Dados carregados em refs reativas
                      ↓
            Template atualizado automaticamente
```

#### Operações CRUD:

```
Criar Veículo:
  openAddVeiculoDialog() → saveVeiculo() → veiculoService.createVeiculo()

Editar Veículo:
  editVeiculo() → saveVeiculo() → veiculoService.updateVeiculo()

Deletar Veículo:
  deleteVeiculo() → veiculoService.deleteVeiculo()

Criar Manutenção:
  openAddManutencaoDialog() → saveManutencao() → manutencaoService.createManutencao()
```

#### Notificações:

- Substituído: `vuetify-use-dialog` (não estava disponível)
- Implementado: `showToast()` com `v-snackbar` do Vuetify
- Cores: success (verde) para sucesso, error (vermelho) para erros

### 3. **Tipagem TypeScript**

Adicionado tipos para todas as funções:

```typescript
// Estados
const veiculos = ref<Veiculo[]>([]);
const manutencoes = ref<Manutencao[]>([]);

// Funções
function saveVeiculo(): Promise<void>;
function deleteVeiculo(id: number): void;
function formatCurrency(value: number | undefined): string;
function getStatusColor(status: string): string;
```

## Fluxo de Dados

```
API Backend (Laravel)
    ↓
axiosInstance (com interceptadores)
    ↓
veiculoService / manutencaoService
    ↓
VeiculoView.vue (refs reativas)
    ↓
Template (v-for loops)
    ↓
UI atualizada
```

## Tratamento de Erros

1. **Carregamento de dados:**

   - Erro: showToast('Erro ao carregar dados...', 'error')
   - Recuperação: Os dados permanecem vazios até novo carregamento

2. **CRUD operations:**

   - Sucesso: showToast('Operação realizada com sucesso!', 'success')
   - Erro: showToast('Erro na operação...', 'error')
   - Recuperação: Recarrega dados via loadVeiculos() ou loadManutencoes()

3. **Validação:**
   - Backend: Retorna 422 com mensagens de erro
   - Frontend: Captura e exibe no snackbar

## Estados de Carregamento

```typescript
const loadingData = ref(false); // Carregamento inicial
const loading = ref(false); // Operações de veículo
const loadingManutencao = ref(false); // Operações de manutenção
```

Podem ser usados para desabilitar botões ou mostrar spinners:

```vue
<v-btn :disabled="loading" @click="saveVeiculo">
  {{ loading ? 'Salvando...' : 'Salvar' }}
</v-btn>
```

## Próximos Passos Sugeridos

1. **Adicionar loading indicators visuais** no template
2. **Implementar refresh manual** com botão "Recarregar"
3. **Adicionar paginação** para grandes volumes de dados
4. **Implementar busca/filtro via API** em vez de frontend
5. **Adicionar validação de formulário** mais robusta
6. **Implementar confirmação de exclusão** com dialog
7. **Cache de dados** em Pinia para melhor performance

## Testes Recomendados

### Testes Manuais:

1. Criar novo veículo
2. Editar veículo existente
3. Deletar veículo
4. Criar manutenção com múltiplos itens
5. Editar manutenção
6. Visualizar detalhes de manutenção
7. Verificar notificações de erro/sucesso
8. Testar com rede lenta (DevTools)

### Testes Automatizados:

- Verificar se dados carregam no onMounted
- Verificar se CRUD calls usam endpoints corretos
- Verificar se erros são capturados e exibidos
- Verificar se dados são transformados corretamente

## Estrutura de Arquivos

```
frontend/
├── src/
│   ├── services/
│   │   ├── http.ts (axios com interceptadores)
│   │   └── veiculoService.ts (NOVO)
│   └── views/
│       └── veiculo/
│           └── VeiculoView.vue (ATUALIZADO)
└── ...

backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── VeiculoController.php
│   │       └── ManutencaoController.php
│   └── Models/
│       ├── Veiculo.php
│       ├── Manutencao.php
│       └── ManutencaoItem.php
└── routes/
    └── api.php
```

## Commits Relacionados

- `feat: Add complete vehicle management backend structure` (939624b1)
- `feat: Integrate VeiculoView with backend API` (7e1b2e7a)

## Status

✅ Frontend + Backend integrados e funcionando
✅ CRUD completo para veículos e manutenções
✅ Tratamento de erros implementado
✅ Build sem erros
✅ Rotas API registradas e testadas
