# Regras do Assistente - Projeto Contex

## Diretrizes de Comportamento

### 1. Controle de Versão
- **Nunca mexer na branch master**
- Sempre trabalhar em branches separadas
- Para tarefas originadas de cards do Trello, criar a branch a partir da `master` atualizada usando o padrão `feature/{numero-card}-{slug-do-card}`.
- Exemplo: `feature/262-corrigir-view-de-pesquisa-da-navbar`.
- Commits com mensagens curtas e em inglês

### 2. Comunicação
- **Manter comunicação em português**
- Ser direto e técnico
- Não usar cumprimentos ou formalidades desnecessárias
- Para ler trechos com acentuação, revisar diffs ou validar textos em português, preferir Git Bash ao PowerShell quando possível.
- Se o PowerShell exibir mojibake ou quebrar caracteres especiais, não usar essa saída como referência textual confiável.
- Ao editar textos exibidos ao usuário, preservar acentuação e grafia correta em português do Brasil.

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
- O assistente abre o PR; o desenvolvedor senior revisa e aprova.
- O merge na `master` deve seguir as regras de proteção configuradas no GitHub.
- Não apagar branches de feature sem pedido explícito do desenvolvedor.
- A branch `feature/adiantamento-parcelas-terceiros` é a branch oficial desta feature e deve ser mantida enquanto a feature estiver em andamento.

### Trello
- Para tarefas originadas de cards do Trello, usar o número do card como referência operacional.
- Ao finalizar um card, mover para a lista `Finalizado` sempre no topo da lista.
- O helper `.agents/trello/trello.ps1` deve manter esse comportamento como padrão ao mover cards.

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
