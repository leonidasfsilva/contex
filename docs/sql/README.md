# Controle de scripts SQL em produção

Os scripts desta pasta não são considerados aplicados em produção apenas porque foram integrados ao Git ou executados no banco de desenvolvimento.

## Fila oficial

O estado de implantação em PRD é registrado em [`deploy_prd_pendente.md`](deploy_prd_pendente.md) e espelhado no card operacional do Trello.

Estados aceitos:

- `PENDENTE`: ainda não aplicado em PRD;
- `APLICADO`: executado e validado em PRD;
- `CANCELADO`: não deve mais ser aplicado, com justificativa registrada.

## Ao adicionar uma SQL

1. Criar o script idempotente em `docs/sql/`.
2. Adicionar uma linha `PENDENTE` em `deploy_prd_pendente.md` no mesmo commit.
3. Adicionar um item não concluído na checklist do card operacional no Trello.
4. Informar o card e o PR de origem.

## Ao aplicar em PRD

1. Fazer backup ou confirmar o mecanismo de rollback adequado ao script.
2. Executar a SQL na ordem registrada.
3. Rodar a consulta de verificação documentada.
4. Alterar o estado para `APLICADO`, informando data e responsável.
5. Concluir o item correspondente no Trello.

O card operacional só pode ser finalizado quando não houver itens `PENDENTE`.
