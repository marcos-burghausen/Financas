# Fix: Erro 500 ao Carregar Veículos

## Problema Identificado

**Erro original:**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'Mr_database.manutencaos' doesn't exist
```

O frontend estava recebendo erro 500 ao tentar listar veículos porque o backend estava procurando pela tabela `manutencaos` (plural com "s") em vez de `manutencoes` (com "ções").

## Causa Raiz

O Eloquent ORM do Laravel, por padrão, pluraliza nomes de modelos para determinar o nome da tabela:
- Model: `Manutencao` → Tabela padrão esperada: `manutencaos` ❌
- Tabela real criada: `manutencoes` ✅

Quando o VeiculoController tentava fazer `with('manutencoes')` no eager loading, o relacionamento usava o nome do modelo para query na tabela, causando o erro.

## Solução Implementada

Adicionado atributo explícito no modelo `Manutencao.php`:

```php
class Manutencao extends Model
{
    protected $table = 'manutencoes';  // ← Adicionado esta linha
    
    protected $fillable = [
        // ...
    ];
}
```

## Verificação

### Teste no Tinker:
```php
$veiculos = App\Models\Veiculo::with('manutencoes')->get();
// Agora funciona sem erro!
```

### Resultado:
```
Veículos encontrados: 2
- Fiat com 0 manutenções
- teste com 0 manutenções
```

## Impacto

- ✅ Frontend pode agora fazer requisições GET `/api/veiculos`
- ✅ Relacionamento eager loading funciona corretamente
- ✅ VeiculoView carrega dados da API sem erros 500
- ✅ Snackbar mostra mensagens de sucesso/erro adequadamente

## Arquivos Modificados

- `backend/app/Models/Manutencao.php` - Adicionada definição explícita de tabela

## Commit

```
fix: Add explicit table name to Manutencao model
```

## Próximas Considerações

1. **Autenticação**: O endpoint requer token Sanctum (estamos dentro do middleware 'auth:sanctum')
2. **Frontend**: Ao fazer requisições, certifique-se de incluir o token de autenticação no header
3. **Headers**: O axios já está configurado com interceptadores que adicionam o token automaticamente

## Status

✅ **RESOLVIDO** - API agora funciona corretamente
