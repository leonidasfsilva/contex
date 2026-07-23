# GitHub CLI local

Use `pr.sh` para criar, editar e validar corpos de Pull Requests do Contex.

```bash
.agents/git/pr.sh create --title "Titulo" --body-file .agents/git/pr-body.md
.agents/git/pr.sh edit --pr 44 --body-file .agents/git/pr-body.md
.agents/git/pr.sh verify --pr 44
```

O helper normaliza o arquivo para UTF-8 real, rejeita sequencias `\\n` literais e caracteres de controle e envia o corpo por `--body-file`. Depois de criar ou editar um PR, executar `verify` e conferir a renderizacao no GitHub.

## Branches baseadas em cards do Trello

Antes de criar ou alterar uma branch local, atualizar as referencias remotas com `git fetch` e sincronizar a branch base com `git pull`.

Para tarefas originadas de cards do Trello, criar a branch a partir da `master` atualizada usando obrigatoriamente:

```text
{escopo}/{numero-card}-{slug-do-card}
```

O escopo deve refletir a natureza da tarefa, como `feature`, `bugfix`, `hotfix`, `chore` ou `docs`.

Exemplo para o card `273 - Titulo do card`:

```text
feature/273-titulo-do-card
```

Nao inserir o prefixo `card-` antes do numero.
