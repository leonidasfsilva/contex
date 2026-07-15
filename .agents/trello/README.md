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
- `boardId`: board padrão, opcional.

O arquivo `config.local.json` fica ignorado pelo Git e não deve ser commitado.

Também é possível usar variáveis de ambiente:

- `TRELLO_KEY`
- `TRELLO_TOKEN`
- `TRELLO_BOARD_ID`

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

O script `trello.ps1` fica apenas como legado. O fluxo operacional dos agentes no Contex deve usar `trello.sh`.
