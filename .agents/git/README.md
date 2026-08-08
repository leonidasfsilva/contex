# GitHub CLI local

Use `pr.sh` para criar, editar, comentar e validar Pull Requests do Contex.

Todo título, corpo e comentário publicado no GitHub passa pelo guard comum
`.agents/text/utf8_guard.php`. Ele bloqueia mojibake, perda por `??`, `U+FFFD`,
caracteres de controle e `\n` literal, além de normalizar Unicode em NFC.

Não use `gh pr create`, `gh pr edit`, `gh pr comment` ou `gh api` diretamente
para publicar texto. O único caminho autorizado é `pr.sh`: ele normaliza pelo
guard, envia o payload e relê o conteúdo publicado para validar o resultado.
para publicar texto.

```bash
.agents/git/pr.sh create --title "Titulo" --body-file .agents/git/pr-body.md
.agents/git/pr.sh edit --pr 44 --title "Titulo" --body-file .agents/git/pr-body.md
.agents/git/pr.sh comment --pr 44 --body-file .agents/git/comentario.md
.agents/git/pr.sh verify --pr 44
```

Depois de criar ou editar um PR, executar `verify` e conferir a renderização no GitHub.

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

## Bugs sem card prévio

Quando o desenvolvedor relatar um bug que não possua card prévio, criar a branch sem número de card:

```text
bugfix/{descricao-do-bug}
```

Exemplo:

```text
bugfix/corrige-limit-zero-ci-3113
```

Não reutilizar o número de um card antigo apenas porque a regressão foi causada por uma entrega relacionada a ele.
