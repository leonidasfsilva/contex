# Trello CLI local

Ferramenta local para o Agente IA consultar e atualizar cards do Trello via REST API.

## Configuração

Copie o arquivo de exemplo:

```bash
cp .agents/trello/config.example.json .agents/trello/config.local.json
```

Preencha `config.local.json` com:

- `key`: API key do Trello;
- `token`: token de acesso do Trello;
- `boardIds`: IDs dos boards por projeto (`contex` e `contex-spa`).

O arquivo `config.local.json` fica ignorado pelo Git e não deve ser commitado.

Também é possível usar variáveis de ambiente:

- `TRELLO_KEY`
- `TRELLO_TOKEN`
- `TRELLO_BOARD_ID`
- `TRELLO_PROJECT` (opcional; use `contex` ou `contex-spa` para sobrescrever a detecção automática)

## Uso

```bash
.agents/trello/trello.sh test
.agents/trello/trello.sh boards
.agents/trello/trello.sh lists
.agents/trello/trello.sh cards -ListId "ID_DA_LISTA"
.agents/trello/trello.sh card -CardNumber 264
.agents/trello/trello.sh create-card -ListId "ID_DA_LISTA" -Name "Título" -Desc "Descrição"
.agents/trello/trello.sh comment -CardId "ID_DO_CARD" -Text "Comentário"
.agents/trello/trello.sh move-card -CardId "ID_DO_CARD" -ListId "ID_DA_LISTA_DESTINO"
```

O board é detectado pelo diretório atual do projeto. Por exemplo, executando o script a partir de `C:\laragon\www\contex` será usado o board `contex`; a partir de `C:\laragon\www\contex-spa`, o board `contex-spa`.

Prioridade para escolher o board: `-BoardId`, `TRELLO_BOARD_ID`, `TRELLO_PROJECT`/diretório atual e, por último, o campo legado `boardId`.

O `trello.ps1` é a ferramenta operacional compartilhada pelos dois projetos. O arquivo `config.local.json` permanece apenas no projeto pai e nunca deve ser copiado para o `contex-spa`.
