# Trello CLI local

Ferramenta local para o Agente IA consultar e atualizar cards do Trello via REST API.

## Configuração

Copie o arquivo de exemplo:

```powershell
Copy-Item .agents/trello/config.example.json .agents/trello/config.local.json
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

```powershell
.agents/trello/trello.ps1 test
.agents/trello/trello.ps1 boards
.agents/trello/trello.ps1 lists
.agents/trello/trello.ps1 cards -ListId "ID_DA_LISTA"
.agents/trello/trello.ps1 create-card -ListId "ID_DA_LISTA" -Name "Título" -Desc "Descrição"
.agents/trello/trello.ps1 comment -CardId "ID_DO_CARD" -Text "Comentário"
.agents/trello/trello.ps1 move-card -CardId "ID_DO_CARD" -ListId "ID_DA_LISTA_DESTINO"
```

