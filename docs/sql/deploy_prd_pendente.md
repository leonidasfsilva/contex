# Fila de deploy SQL em PRD

Este arquivo controla scripts versionados que ainda precisam ser aplicados manualmente no banco de produção.

| Estado | Ordem | Script | Origem | Adicionado em | Aplicado em | Responsável |
| --- | ---: | --- | --- | --- | --- | --- |
| APLICADO | 1 | `feature_278_controle_operacional_spa.sql` | Card 278 / PR 54 | 2026-07-27 | 2026-07-28 | Leônidas Ferreira |

## Verificação da pendência atual

Depois de executar `feature_278_controle_operacional_spa.sql`, confirmar:

```sql
SELECT id, descricao, setor, ativo, status
FROM configs_opcoes
WHERE descricao IN (
    'DESATIVAR API FRONTEND',
    'DESCONEXAO DE USUARIOS API FRONTEND',
    'DESCONECTAR USUARIOS CONECTADOS',
    'MODO MANUTENCAO'
) OR id IN (98, 99)
ORDER BY id;
```

Resultado esperado:

- um único registro `DESATIVAR API FRONTEND`, setor `SISTEMA`, `ativo = 0` e `status = 1`;
- um único registro `DESCONEXAO DE USUARIOS API FRONTEND`, setor `SISTEMA`, `ativo = 0` e `status = 1`;
- IDs atribuídos automaticamente pelo banco, sem valores fixos no script.
- ID `98`: `DESCONECTAR USUARIOS CONECTADOS`, setor `SISTEMA`, `ativo = 1` e `status = 1`;
- ID `99`: `MODO MANUTENCAO`, setor `SISTEMA`, `ativo = 1` e `status = 1`.

Os IDs `98` e `99` são exceções explícitas: constituem chaves técnicas legadas já fixadas em `configs_usuario_assoc` e no código do MVC.

## Observação operacional

A tabela `configs_opcoes` do banco DEV foi exportada integralmente e importada em PRD pelo desenvolvedor em 2026-07-28. Portanto, o script está registrado como aplicado e não deve ser executado novamente em produção.
