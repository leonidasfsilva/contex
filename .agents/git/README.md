# GitHub CLI local

Use `pr.sh` para criar, editar e validar corpos de Pull Requests do Contex.

```bash
.agents/git/pr.sh create --title "Titulo" --body-file .agents/git/pr-body.md
.agents/git/pr.sh edit --pr 44 --body-file .agents/git/pr-body.md
.agents/git/pr.sh verify --pr 44
```

O helper normaliza o arquivo para UTF-8 real, rejeita sequencias `\\n` literais e caracteres de controle e envia o corpo por `--body-file`. Depois de criar ou editar um PR, executar `verify` e conferir a renderizacao no GitHub.
