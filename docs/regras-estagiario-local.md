# Regras - Estagiario Local

## Contexto

O projeto Contex possui um assistente local auxiliar, chamado informalmente de **estagiario**.

Esse estagiario foi configurado para executar tarefas auxiliares de codigo localmente, com o objetivo de reduzir uso desnecessario de tokens do Codex em tarefas medias, repetitivas ou bem delimitadas.

## Stack Configurada

- LM Studio
- Modelo: `Qwen2.5-Coder-14B-Instruct-GGUF`
- Quantizacao: `Q4_K_M`
- Servidor local OpenAI-compatible:
  - `http://127.0.0.1:1234/v1`
- Ferramenta de edicao/agente:
  - `aider`
- Alias no Git Bash:
  - `estagiario`

Alias configurado:

```bash
alias estagiario='OPENAI_API_KEY=lm-studio OPENAI_API_BASE=http://127.0.0.1:1234/v1 aider --no-auto-commits --model openai/qwen2.5-coder-14b-instruct'
```

## Disponibilidade

Antes de tentar usar o estagiario local, o agente principal deve perguntar ao desenvolvedor se ele esta disponivel no ambiente atual.

Motivos:

- o desenvolvedor pode estar em outro computador;
- o LM Studio pode nao estar instalado;
- o modelo pode nao ter sido baixado;
- o servidor local pode nao estar rodando;
- o alias `estagiario` pode nao existir no ambiente atual.

Pergunta recomendada:

```text
O estagiario local esta disponivel neste ambiente para eu delegar esta tarefa?
```

## Quando Usar

O estagiario local pode ser usado para tarefas medias, auxiliares e bem delimitadas, como:

- gerar rascunhos de funcoes pequenas;
- criar SQL inicial;
- sugerir estrutura de HTML/modal;
- revisar ou transformar pequenos trechos de codigo;
- gerar textos tecnicos iniciais;
- produzir alternativas de implementacao para avaliacao.

## Quando Nao Usar

Nao usar o estagiario local para:

- decidir arquitetura;
- alterar multiplos arquivos sensiveis sem supervisao;
- mexer em branch, commit, push ou PR;
- executar tarefas diretamente na `master`;
- substituir a revisao do Codex ou do desenvolvedor senior;
- tomar decisoes de produto ou escopo.

## Regras de Seguranca

- Usar sempre com `--no-auto-commits`.
- Todo diff produzido pelo estagiario deve ser revisado antes de entrar no projeto.
- O estagiario nao deve ter autonomia para commitar.
- O estagiario nao deve alterar arquivos sem escopo claro.
- O agente principal deve validar e adaptar qualquer saida antes de propor aplicacao no Contex.

## Fluxo Recomendado

```text
Desenvolvedor senior -> Codex
Codex -> pergunta se o estagiario local esta disponivel
Codex -> delega tarefa pequena e bem delimitada
Estagiario -> gera rascunho/diff
Codex -> revisa, adapta e apresenta ao desenvolvedor
Desenvolvedor senior -> aprova ou ajusta
```

## Observacoes

O estagiario local e uma ferramenta auxiliar, nao uma fonte de decisao.

Ele deve economizar tempo e tokens em tarefas adequadas, mas nao deve aumentar o trabalho de revisao nem introduzir alteracoes sem controle.
