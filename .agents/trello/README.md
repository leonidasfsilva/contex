# Trello CLI local

Ferramenta local para o Agente IA consultar e atualizar cards do Trello via REST API.

Todos os campos textuais enviados pelos helpers passam pelo guard comum
`.agents/text/utf8_guard.php`, inclusive nomes, descrições, comentários,
checklists, itens e nomes de anexos. A função central de request do helper
PowerShell percorre o payload inteiro antes do envio.

Não faça escrita direta com `curl`, `Invoke-RestMethod` ou outro cliente contra
a API do Trello. As escritas devem passar pelos helpers protegidos e ser relidas
após a publicação quando o serviço permitir.

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
.agents/trello/trello.sh update-name -CardId "ID_DO_CARD" -Name "Novo título"
```

O script `trello.ps1` é o helper oficial no Windows; `trello.sh` é o helper oficial no Bash. Não é permitido usar `curl`, `Invoke-RestMethod`, SDK, MCP ou qualquer cliente direto para escrever no Trello fora desses helpers. Depois de cada escrita textual, o helper deve reler e validar o conteúdo publicado.

O board é detectado pelo diretório atual do projeto. Por exemplo, executando o script a partir de `C:\laragon\www\contex` será usado o board `contex`; a partir de `C:\laragon\www\contex-spa`, o board `contex-spa`.

Prioridade para escolher o board: `-BoardId`, `TRELLO_BOARD_ID`, `TRELLO_PROJECT`/diretório atual e, por último, o campo legado `boardId`.

O arquivo `config.local.json` permanece apenas no projeto pai e nunca deve ser copiado para o `contex-spa`.

## Padrões do projeto

Antes de criar ou renomear arquivos, classes, métodos, variáveis, rotas ou estruturas, o agente deve inspecionar implementações equivalentes já existentes no projeto e seguir o padrão predominante do Contex.

- O padrão local do projeto prevalece sobre convenções genéricas do framework, linguagem ou mercado, salvo decisão explícita em contrário.
- Padrões e decisões definidos ou impostos pelo responsável do projeto têm precedência e devem ser seguidos mesmo quando divergirem das convenções gerais.
- Não justificar uma nomenclatura apenas por ela ser aceita pelo CodeIgniter ou por outra ferramenta; ela também deve ser coerente com os arquivos vizinhos e componentes equivalentes do repositório.
- Somente em projetos, módulos ou estruturas realmente criados do zero, sem precedente local nem padrão definido pelo responsável, adotar as convenções oficiais e atuais da tecnologia utilizada, como no projeto Vue.
- Quando não houver precedente local claro, registrar a decisão técnica adotada antes de introduzir um novo padrão.

## Fluxo de implementação, homologação e Git

Durante a execução de uma tarefa ou correção, o agente deve modificar somente os arquivos necessários e aguardar a homologação do usuário antes de realizar operações de entrega no Git.

- Não criar commit, executar `push`, abrir PR ou atualizar um PR durante ciclos intermediários de implementação e correção.
- Correções solicitadas durante a homologação devem permanecer como alterações locais para que o usuário possa acompanhar e inspecionar o diff acumulado.
- Após cada ajuste, executar somente as verificações técnicas proporcionais ao risco e informar os arquivos alterados e o resultado dos testes, sem publicar a entrega.
- Realizar um único commit referente à tarefa-alvo e abrir ou atualizar um único PR somente após o usuário aprovar explicitamente a homologação.
- Não consumir tempo ou contexto com commits e atualizações remotas para tentativas intermediárias que ainda podem ser rejeitadas ou corrigidas.
- Exceções a esse fluxo exigem ordem explícita do usuário para commitar, enviar ou publicar antes da homologação.

## Codificação de texto

O helper normaliza automaticamente os campos textuais enviados ao Trello para UTF-8. Entradas que chegarem em Windows-1252/ANSI são convertidas antes da chamada da API.

A normalização é aplicada a:

- título do card;
- descrição;
- comentários;
- nome de anexos.

Depois de criar ou atualizar um card, o agente deve consultar o card novamente e validar que o Trello não armazenou sequências percentuais literais, mojibake ou caracteres corrompidos.

## Formatação das descrições

Todos os tópicos e seções da descrição de um card devem usar o padrão Markdown de título 3:

```markdown
### Nome da seção
```

Não usar texto simples como título de seção. Depois de criar ou atualizar um card, o agente deve validar também a formatação com `###` e a posição correta do card na lista.

Identificadores técnicos devem usar código inline com crases. A regra se aplica a nomes de tabelas, campos, métodos, funções, classes, arquivos, caminhos, rotas, variáveis de ambiente, comandos e valores literais.

## Checklists de implementação e homologação

Todo novo card ou novo escopo executável deve ter obrigatoriamente duas checklists separadas antes do início da execução:

- `Implementação`: itens de infraestrutura, arquitetura, código, configuração, documentação e verificações técnicas executadas pelo agente;
- `Homologação`: cenários funcionais que dependem da validação e confirmação do usuário.

- A descrição preserva contexto, decisões e critérios de conclusão.
- As checklists decompõem somente etapas verificáveis.
- O agente pode concluir itens da checklist `Implementação` após implementar e verificar tecnicamente a entrega.
- Verificações automatizadas, inspeções HTTP, lint, testes e build são evidências técnicas e não equivalem à homologação do usuário.
- Itens da checklist `Homologação` só podem ser concluídos após confirmação explícita do usuário para o cenário correspondente.
- Comentários do card devem distinguir claramente `validado tecnicamente pelo agente` de `homologado pelo usuário`.
- Não crie checklist para texto apenas informativo, sem ação concreta a executar ou validar.
