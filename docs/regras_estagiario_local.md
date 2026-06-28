# Regras - Estagiário Local

## Contexto

O projeto Contex possui um assistente local auxiliar, chamado informalmente de **estagiário**.

O estagiário existe para reduzir uso desnecessário de tokens do Agente IA em tarefas médias, repetitivas ou bem delimitadas. Ele é uma ferramenta auxiliar, não uma fonte de decisão.

## Stack Configurada

- LM Studio
- Modelo: `Qwen2.5-Coder-14B-Instruct-GGUF`
- Quantização: `Q4_K_M`
- Servidor local OpenAI-compatible:
  - `http://127.0.0.1:1234/v1`
- Uso manual no VS Code:
  - Cline
- Uso programático pelo Agente IA:
  - `.agents/estagiario/consultar-estagiario.ps1`

## Regras Persistentes

- Regras do Cline ficam em `.clinerules/estagiario.md`.
- Regras gerais do Agente IA ficam em `docs/regras_assistente.md`.
- O script `.agents/estagiario/consultar-estagiario.ps1` deve carregar essas regras antes de chamar a API local do LM Studio.

## Disponibilidade

Antes de tentar usar o estagiário local, o Agente IA deve confirmar se ele está disponível no ambiente atual quando houver dúvida.

Motivos:

- o desenvolvedor pode estar em outro computador;
- o LM Studio pode não estar instalado;
- o modelo pode não ter sido baixado;
- o servidor local pode não estar rodando;
- o Cline ou o script local podem não estar configurados.

Pergunta recomendada:

```text
O estagiário local está disponível neste ambiente para eu delegar esta tarefa?
```

## Quando Usar

O estagiário local pode ser usado para tarefas auxiliares e bem delimitadas, como:

- ler trechos de arquivos e resumir a lógica;
- gerar rascunhos de funções pequenas;
- criar SQL inicial;
- sugerir estrutura de HTML/modal;
- revisar ou transformar pequenos trechos de código;
- gerar textos técnicos iniciais;
- produzir alternativas de implementação para avaliação.

## Quando Não Usar

Não usar o estagiário local para:

- decidir arquitetura;
- tomar decisões de produto ou escopo;
- mexer em branch, commit, push ou PR;
- executar tarefas diretamente na `master`;
- substituir a revisão do Agente IA ou do desenvolvedor sênior;
- aplicar alterações sem aprovação explícita.

## Regras de Segurança

- O estagiário pode ler arquivos do projeto quando a tarefa exigir análise, correção ou implementação.
- O estagiário deve pedir confirmação antes de editar arquivos, executar comandos, criar branches, commits ou PRs.
- Saídas do estagiário devem ser tratadas como rascunho.
- O Agente IA deve revisar, adaptar e validar qualquer saída antes de propor aplicação no Contex.
- Se o estagiário errar, a primeira tentativa deve ser pedir que ele corrija a própria proposta com feedback objetivo.
- Não expor credenciais, tokens, chaves ou valores sensíveis encontrados no projeto.

## Fluxo Recomendado

```text
Desenvolvedor sênior -> Agente IA
Agente IA -> delega tarefa pequena ao estagiário local via LM Studio API
Estagiário -> gera análise, rascunho ou pseudo-patch
Agente IA -> revisa, pede correção ao estagiário se necessário e adapta
Agente IA -> apresenta proposta ao desenvolvedor
Desenvolvedor sênior -> aprova, ajusta ou rejeita
```

## Uso Programático

Exemplo:

```powershell
.agents/estagiario/consultar-estagiario.ps1 `
  -Prompt "Analise estes trechos e proponha um rascunho de solução. Não aplique alterações." `
  -ContextFiles @(
    "application/views/financeiro/lancamentos.php#365-520",
    "application/views/faturas/lancamentos_terceiros.php#55-125"
  )
```

Formato de `ContextFiles`:

```text
caminho/arquivo.php
caminho/arquivo.php#linha-inicial-linha-final
```

Use recortes de linhas sempre que possível para não estourar o contexto do modelo local.
