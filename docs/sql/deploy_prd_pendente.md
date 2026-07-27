# Fila de deploy SQL em PRD

Este arquivo controla scripts versionados que ainda precisam ser aplicados manualmente no banco de produção.

| Estado | Ordem | Script | Origem | Adicionado em | Aplicado em | Responsável |
| --- | ---: | --- | --- | --- | --- | --- |
| PENDENTE | 1 | `feature_278_controle_operacional_spa.sql` | Card 278 / PR 54 | 2026-07-27 | - | - |

## Verificação da pendência atual

Depois de executar `feature_278_controle_operacional_spa.sql`, confirmar:

```sql
SELECT id, descricao, setor, ativo, status
FROM configs_opcoes
WHERE id = 100;
```

Resultado esperado: um registro com ID `100`, setor `SISTEMA`, `ativo = 1` e `status = 1`.

## Observação operacional

O script do Card 278 permanece pendente porque o acesso ao banco PRD da Devply está temporariamente indisponível pelo ISP. O banco local de desenvolvimento já recebeu e validou a alteração.
