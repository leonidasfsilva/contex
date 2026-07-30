# Regras do Assistente - Projeto Contex

## Diretrizes de Comportamento

### 1. Controle de Versão
- **Nunca mexer na branch master**
- Sempre trabalhar em branches separadas
- Para tarefas originadas de cards do Trello, criar a branch a partir da `master` atualizada usando o padrão `feature/{numero-card}-{slug-do-card}`.
- Exemplo: `feature/262-corrigir-view-de-pesquisa-da-navbar`.
- Quando o desenvolvedor relatar um bug sem card prévio, criar a branch sem número de card, usando o padrão `bugfix/{descricao-do-bug}`.
- Não associar a branch de correção ao número de um card antigo apenas porque a regressão foi causada ou introduzida pelo escopo daquele card.
- Commits com mensagens curtas e em inglês

### 2. Comunicação
- **Manter comunicação em português**
- Ser direto e técnico
- Não usar cumprimentos ou formalidades desnecessárias
- No projeto Contex, usar Bash/Git Bash como shell padrão operacional para comandos de terminal sempre que a ferramenta permitir.
- Se o runtime do assistente expuser PowerShell como shell interno, chamar Bash explicitamente para comandos sensíveis a ambiente, quoting, paths, Composer, Git Bash/MINGW ou encoding.
- Usar PowerShell apenas quando for inevitável ou quando o comando for simples e não houver risco de divergência com o ambiente real do desenvolvedor.
- Para ler trechos com acentuação, revisar diffs ou validar textos em português, preferir Git Bash ao PowerShell quando possível.
- Se o PowerShell exibir mojibake ou quebrar caracteres especiais, não usar essa saída como referência textual confiável.
- Ao editar textos exibidos ao usuário, preservar acentuação e grafia correta em português do Brasil.
- Agentes DEVEM editar e salvar arquivos em UTF-8 real sempre que houver texto acentuado; se a ferramenta/shell usada não garantir UTF-8, trocar de ferramenta antes de escrever.
- Após editar mensagens exibidas ao usuário, validar o diff ou o arquivo por um meio confiável em UTF-8, sem depender de saída mojibake do PowerShell.
- Todo texto livre publicado por automação em serviço externo deve passar pelo guard comum `.agents/text/utf8_guard.php` antes da requisição. Isso inclui títulos, descrições, comentários, checklists, nomes de anexos, corpos de PR, prompts e mensagens.
- É proibido usar chamadas cruas como `gh api`, `gh pr create`, `curl` ou `Invoke-RestMethod` para publicar texto quando existir um conector protegido do projeto. Novos conectores devem integrar o guard antes da primeira escrita.
- O guard deve normalizar Unicode NFC, aceitar conversão de Windows-1252 somente quando a origem não for UTF-8 válida e rejeitar caracteres de controle, `U+FFFD`, assinaturas de mojibake e perda de caracteres representada por `??` em palavras.
- Após publicação externa, reler e validar o conteúdo armazenado quando o serviço oferecer consulta, antes de reportar a entrega como concluída.

### 3. Processo de Decisão
- **Sempre responder primeiro às perguntas antes de tomar qualquer ação**
- Aguardar confirmação do usuário antes de executar tarefas
- Não assumir ações automáticas
- Leitura de arquivos, código e documentação pode ser feita quando necessário para entendimento.
- Qualquer escrita em arquivo exige aprovação explícita do desenvolvedor antes da execução.
- Qualquer ação em Git exige aprovação explícita do desenvolvedor antes da execução.
- Criar branch, commit, migration, documentação nova ou editar documentação existente somente com aprovação explícita.
- Ideias ou inferências do assistente devem ser apresentadas primeiro no chat como sugestão/pergunta.
- **Quaisquer ideias ou inferências devem obrigatoriamente passar pelo crivo/approve do desenvolvedor antes de serem efetivamente registradas ou editadas em quaisquer arquivos do projeto.**
- Se o desenvolvedor pedir para documentar o que foi conversado, registrar apenas o que foi conversado/aprovado, sem acrescentar backlog, features futuras ou interpretações não aprovadas.
- Se o assistente quiser acrescentar algo como "possível melhoria futura", deve perguntar antes se o desenvolvedor quer registrar isso no arquivo.
- Para alterações de código em tarefas médias ou repetitivas, o estagiário local deve gerar patch/rascunho primeiro.
- Se o rascunho do estagiário local vier inadequado, o primeiro passo deve ser pedir que ele corrija a própria proposta com feedback objetivo.
- O Agente IA deve atuar como revisor/integrador final nesses casos, evitando implementar diretamente antes de testar o fluxo com o estagiário local.
- Exceções: hotfix de uma linha, bug crítico, tarefa muito sensível ou pedido explícito do desenvolvedor para o Agente IA implementar diretamente.

### 4. Estilo de Commits
- **Mensagens curtas e em inglês**
- Formato: `action: description`
- Exemplos: `feat: add user auth`, `fix: resolve db connection`, `docs: update migration guide`

### 5. Banco de Dados e SQL
- Antes de qualquer escrita direta em banco de dados, inclusive no ambiente de desenvolvimento, o agente deve executar somente consultas de leitura para inspecionar o estado atual, identificar registros, vínculos, duplicidades e padrões já existentes.
- Após a inspeção, o agente deve apresentar no chat o diagnóstico e a SQL exata proposta, informando o impacto esperado.
- `INSERT`, `UPDATE`, `DELETE`, alterações de schema, execução de scripts, migrations e qualquer outra mutação no banco só podem ser executados após autorização explícita do desenvolvedor para aquela operação.
- Uma autorização para criar ou corrigir o arquivo SQL não implica autorização para executá-lo no banco. Frases que indiquem apenas possibilidade técnica, como “pode apagar e refazer”, não devem ser interpretadas como ordem de execução sem confirmação explícita.
- Antes de referenciar ou inserir uma chave primária manualmente, o agente deve inspecionar o schema, o `AUTO_INCREMENT`, os registros existentes e os vínculos relacionados. Não escolher IDs por faixa presumida, conveniência ou para evitar colisões sem evidência do banco.
- Em tabelas com chave primária `AUTO_INCREMENT`, novos registros devem omitir a coluna da PK e deixar o banco atribuir o ID. IDs explícitos só podem ser usados quando constituírem uma chave técnica estável já definida pelo projeto e forem aprovados pelo desenvolvedor.
- Regras funcionais não devem depender de IDs auto-incrementais arbitrários. O código deve localizar o registro por chave funcional estável ou por relacionamento persistido, conforme o padrão do projeto.
- Scripts SQL versionados devem preservar compatibilidade com o fluxo operacional do projeto: exportar de produção e injetar diretamente no banco local via HeidiSQL, com `Criar Tabela` habilitado.
- Ao criar tabelas novas, não usar `utf8mb4` sem `COLLATE` explícito.
- Para tabelas novas do Contex, usar por padrão:
  - `ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci`
- Se houver motivo técnico para usar `utf8mb4`, o agente deve pedir aprovação antes e cravar collation compatível com o ambiente local, preferencialmente:
  - `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`
- Nunca deixar o MariaDB/MySQL escolher automaticamente collations modernas como `uca1400` ou `0900`, pois elas podem quebrar o dump/inject em ambientes locais mais antigos.
- Para colunas textuais em tabelas novas, quando houver risco de herdar collation da sessão, declarar explicitamente `CHARACTER SET` e `COLLATE`.
- Campos opcionais em modelagem SQL devem permitir `NULL` e declarar `DEFAULT NULL`; não usar `NOT NULL` quando o dado só será preenchido por evento posterior do sistema.
- Após criar ou alterar SQL estrutural, validar com `SHOW CREATE TABLE` e garantir que não apareçam collations incompatíveis como `utf8mb4_uca1400_ai_ci`, `utf8mb3_uca1400_ai_ci` ou `utf8mb4_0900_ai_ci`.
- Não propor script de inicialização no HeidiSQL como solução permanente para incompatibilidade criada por SQL versionado. A correção deve estar no schema/SQL do projeto.
- Toda SQL que exigir aplicação manual em produção deve entrar como `PENDENTE` em `docs/sql/deploy_prd_pendente.md` no mesmo commit.
- A pendência deve ser espelhada como item não concluído no card operacional de deploy SQL do Trello.
- Nunca marcar uma SQL como aplicada em PRD apenas porque foi executada localmente, integrada ao Git ou publicada em um PR.
- Após aplicação e verificação em PRD, registrar data e responsável no arquivo e concluir o item correspondente no Trello.

## Configuração de Git/GitHub do Assistente

### Identidade GitHub
- O assistente deve usar a conta GitHub `webmaster-devply`.
- E-mail da conta: `webmaster@devply.net`.
- Esta conta está cadastrada como contribuidor do projeto Contex no GitHub.

### GitHub CLI
- O GitHub CLI (`gh`) deve estar autenticado como `webmaster-devply`.
- Verificação obrigatória antes de criar PRs:
  - `gh auth status`
  - `gh api user --jq .login`
- O login esperado é `webmaster-devply`.
- Não expor tokens, chaves ou credenciais no chat.
- O `gh` foi configurado com armazenamento acessível ao processo do assistente porque o keyring do terminal interativo pode não estar acessível pelo ambiente do Agente IA.

### Commits
- Antes de qualquer operação Git local que dependa do histórico ou altere o repositório, executar `git fetch`/`git pull` para sincronizar a branch com a origem. Depois, verificar o status e os conflitos antes de editar, criar commits ou fazer push.
- Nunca subir alterações sem confirmar que a branch está atualizada em relação à sua base e ao remoto; se houver conflito, interromper o fluxo, resolver e validar antes do push.
- O assistente não deve alterar a configuração global ou local de `user.name`/`user.email` do desenvolvedor sem aprovação explícita.
- Para commits feitos pelo assistente, usar autor separado quando solicitado:
  - `webmaster-devply <webmaster@devply.net>`
- Quando necessário, usar `git commit --author="webmaster-devply <webmaster@devply.net>" -m "mensagem"`.
- O push pode continuar usando a credencial Git disponível no ambiente, desde que o PR seja aberto via `gh` como `webmaster-devply`.
- O hook de commit possui escopo conhecido e determinístico: novo commit -> verifica regra -> incrementa a versão em `application/config/constants.php`.
- Não gastar comandos/tokens verificando o efeito do hook a cada commit.
- Verificar o hook apenas se houver erro, arquivo inesperado no status, alteração direta no versionamento/hook ou pedido explícito do desenvolvedor.

### Pull Requests
- Criar PRs pelo GitHub CLI autenticado como `webmaster-devply`.
- PRs devem apontar para `master`, salvo orientação diferente do desenvolvedor.
- Título e descrição de PRs do Contex devem ser escritos em português do Brasil.
- A descrição padrão de PR deve usar as seções `Resumo` e `Validação`.
- Não misturar inglês e português na descrição do PR. Commits continuam seguindo a regra própria de mensagens curtas em inglês.
- Títulos, corpos e comentários de PR devem ser preparados e enviados pelo helper `.agents/git/pr.sh`, que aplica a proteção UTF-8 e antimojibake comum.
- Após criar ou editar um PR, executar `.agents/git/pr.sh verify --pr <numero>` e conferir a renderização no GitHub antes de reportar a conclusão.
- Antes de criar ou editar PR, usar PRs recentes do projeto como referência de formato quando houver dúvida.
- Antes de subir alterações solicitadas pelo desenvolvedor com objetivo de atualizar ou abrir PR, verificar no GitHub a existência e o status do PR anterior da branch/card atual.
- Se o PR anterior já estiver `MERGED` ou `CLOSED`, não assumir que o push atualiza aquele PR; criar novo PR ou pedir confirmação quando o fluxo não estiver claro.
- O assistente abre o PR; o desenvolvedor senior revisa e aprova.
- O merge na `master` deve seguir as regras de proteção configuradas no GitHub.
- Não apagar branches de feature sem pedido explícito do desenvolvedor.
- A branch `feature/adiantamento-parcelas-terceiros` é a branch oficial desta feature e deve ser mantida enquanto a feature estiver em andamento.

### Trello
- Para tarefas originadas de cards do Trello, usar o número do card como referência operacional.
- Todo card novo solicitado pelo desenvolvedor deve ser criado no topo da lista indicada, independentemente da coluna.
- Nas descrições dos cards, todos os tópicos e seções devem usar o padrão Markdown de Título 3: `### Nome da seção`. Não usar texto simples como título de seção.
- Nas descrições dos cards, identificadores técnicos devem usar código inline com crases. Isso inclui nomes de tabelas, campos, métodos, funções, classes, arquivos, caminhos, rotas, variáveis de ambiente, comandos e valores literais, por exemplo: `usuarios_passkeys`, `id_usuario` e `reconciliarFinanceiro()`.
- Todo texto enviado ao Trello deve estar em UTF-8 real. É proibido enviar título, descrição, comentário ou nome de anexo em ANSI/Windows-1252 ou confiar em saída com mojibake.
- O agente pode usar qualquer ferramenta adequada para acessar o Trello, desde que envie texto em UTF-8 real e cumpra as regras de conteúdo, validação e posicionamento dos cards.
- Após criar ou atualizar um card, consultar novamente o card e validar título, descrição, formatação das seções com `###`, identificadores técnicos com código inline e posição na lista. Sequências percentuais literais como `%E7`, `%E3`, mojibake ou caracteres corrompidos devem ser corrigidos imediatamente antes de reportar conclusão.
- Escopo, requisitos e decisões do projeto devem ser registrados na descrição do card. Comentários são reservados ao registro de entrega, resultado, PR e fechamento.
- Quando a descrição de um card contiver etapas executáveis de infraestrutura, arquitetura, código, configuração, testes ou homologação, criar no próprio card uma ou mais checklists correspondentes antes de iniciar a execução.
- A descrição deve preservar contexto, decisões e critérios de conclusão; as checklists devem decompor apenas as etapas verificáveis, agrupadas por assunto quando isso tornar a execução mais clara.
- Não criar checklist para conteúdo apenas informativo, sem ação concreta a executar ou validar.
- Ao finalizar um card, mover para a lista `Finalizado` sempre no topo da lista.
- Independentemente da ferramenta utilizada, o agente deve conferir a posição do card após a movimentação.

## Contexto do Projeto

### Sistema Contex
- Sistema de gestão financeira pessoal
- CodeIgniter 3.x
- Migração para CodeIgniter 4 suspensa
- A pasta `contex2` foi removida e não deve ser considerada no planejamento atual

### Equipe
- Desenvolvedor senior + Assistente IA
- Foco em eficiência e qualidade
- Cronogramas realistas baseados na colaboração

## Referências
- [Plano de Migração](plano_migracao_ci4.md)
- [Conflitos CI3→CI4](conflitos_ci3_ci4.md)
- [Documento de Requisitos](documento_requisitos.md)
