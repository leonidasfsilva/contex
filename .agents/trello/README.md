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
.agents/trello/trello.sh update-name -CardId "ID_DO_CARD" -Name "Novo título"
```

O script `trello.ps1` fica apenas como legado. O fluxo operacional dos agentes no Contex deve usar `trello.sh`.

## Codificacao de texto

O helper normaliza automaticamente os campos textuais enviados ao Trello para UTF-8. Entradas que chegarem em Windows-1252/ANSI sao convertidas antes da chamada da API.

A normalizacao e aplicada a:

- titulo do card;
- descricao;
- comentarios;
- nome de anexos.

Depois de criar ou atualizar um card, o agente deve consultar o card novamente e validar que o Trello nao armazenou sequencias percentuais literais, mojibake ou caracteres corrompidos.

## Formatacao das descricoes

Todos os topicos e secoes da descricao de um card devem usar o padrao Markdown de Titulo 3:

```markdown
### Nome da secao
```

Nao usar texto simples como titulo de secao. Depois de criar ou atualizar um card, o agente deve validar tambem a formatacao com `###` e a posicao correta do card na lista.

Identificadores tecnicos devem usar codigo inline com crases. A regra se aplica a nomes de tabelas, campos, metodos, funcoes, classes, arquivos, caminhos, rotas, variaveis de ambiente, comandos e valores literais. Exemplos:

```markdown
Criar a tabela `usuarios_passkeys` vinculada pelo campo `id_usuario`.
Executar `reconciliarFinanceiro()` pela rota `/api/v1/cron/rotinas/financeiro`.
```

Na validacao posterior, o agente deve confirmar que esses identificadores foram renderizados como codigo inline e nao permaneceram como texto comum.
