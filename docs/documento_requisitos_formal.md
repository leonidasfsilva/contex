# Documento de Requisitos - Sistema CONTEX

## 1. Introdução

### 1.1 Objetivo
Este documento especifica os requisitos funcionais e não funcionais do Sistema CONTEX, um sistema web de gestão financeira pessoal desenvolvido em PHP usando o framework CodeIgniter.

### 1.2 Escopo
O sistema CONTEX permite aos usuários gerenciar suas finanças pessoais através de módulos específicos para controle financeiro, clientes, produtos, serviços, ordens de serviço, vendas, usuários e relatórios.

### 1.3 Definições e Abreviações
- RF: Requisito Funcional
- RNF: Requisito Não Funcional
- UC: Caso de Uso
- CRUD: Create, Read, Update, Delete

## 2. Requisitos Funcionais

### 2.1 Módulo de Autenticação e Autorização

**RF-001:** O sistema deve permitir login de usuários com email e senha
- **Pré-condição:** Usuário possui conta cadastrada
- **Pós-condição:** Usuário autenticado e sessão iniciada
- **Critérios de Aceitação:**
  - Campo email obrigatório e válido
  - Campo senha obrigatório
  - Redirecionamento para dashboard após login
  - Mensagem de erro para credenciais inválidas

**RF-002:** O sistema deve controlar permissões por módulo
- **Pré-condição:** Usuário logado
- **Pós-condição:** Acesso liberado ou negado baseado em permissões
- **Critérios de Aceitação:**
  - Permissões: visualizar (v), adicionar (a), editar (e), excluir (d)
  - Controle granular por módulo
  - Usuário administrador tem acesso total

### 2.2 Módulo Financeiro - Lançamentos

**RF-003:** O sistema deve permitir cadastro de entradas financeiras
- **Pré-condição:** Usuário logado com permissão 'aLancamentos'
- **Pós-condição:** Lançamento de entrada registrado
- **Critérios de Aceitação:**
  - Valor positivo obrigatório
  - Data de lançamento obrigatória
  - Descrição obrigatória
  - Forma de pagamento obrigatória
  - Tipo = 1 (entrada)

**RF-004:** O sistema deve permitir cadastro de saídas financeiras
- **Pré-condição:** Usuário logado com permissão 'aLancamentos'
- **Pós-condição:** Lançamento de saída registrado
- **Critérios de Aceitação:**
  - Valor negativo obrigatório
  - Data de lançamento obrigatória
  - Descrição obrigatória
  - Forma de pagamento obrigatória
  - Tipo = 2 (saída)

**RF-005:** O sistema deve permitir filtros de lançamentos por período
- **Pré-condição:** Lançamentos existentes
- **Pós-condição:** Lista filtrada exibida
- **Critérios de Aceitação:**
  - Períodos: 3, 5, 7, 15, 30, 60, 90 dias
  - Período mensal
  - Período específico (data inicial/final)
  - Filtro por status (pendente/efetivado)

### 2.3 Módulo Financeiro - Faturas de Cartão

**RF-006:** O sistema deve permitir cadastro de cartões de crédito
- **Pré-condição:** Usuário logado com permissão 'aFaturas'
- **Pós-condição:** Cartão cadastrado e dados criptografados
- **Critérios de Aceitação:**
  - Número criptografado
  - Nome do titular obrigatório
  - Validade obrigatória
  - CVC criptografado
  - Bandeira obrigatória
  - Apelido opcional

**RF-007:** O sistema deve permitir compras à vista em cartão
- **Pré-condição:** Cartão ativo e fatura aberta
- **Pós-condição:** Lançamento associado à fatura
- **Critérios de Aceitação:**
  - Valor total obrigatório
  - Data da compra obrigatória
  - Descrição obrigatória
  - Parcela única (1/1)

**RF-008:** O sistema deve permitir compras parceladas em cartão
- **Pré-condição:** Cartão ativo e fatura aberta
- **Pós-condição:** Parcelas distribuídas em faturas futuras
- **Critérios de Aceitação:**
  - Valor total obrigatório
  - Quantidade de parcelas (2-12)
  - Valor por parcela calculado automaticamente
  - Faturas futuras criadas automaticamente
  - Máximo 48 parcelas

**RF-009:** O sistema deve permitir compras de terceiros
- **Pré-condição:** Compra em cartão
- **Pós-condição:** Vinculação com cliente e geração de pendência
- **Critérios de Aceitação:**
  - Nome do terceiro obrigatório
  - Cliente selecionado
  - Geração automática de pendência financeira

### 2.4 Módulo Clientes

**RF-010:** O sistema deve permitir cadastro de clientes
- **Pré-condição:** Usuário logado com permissão 'aCliente'
- **Pós-condição:** Cliente cadastrado
- **Critérios de Aceitação:**
  - Nome obrigatório
  - CPF/CNPJ obrigatório e único
  - Telefone obrigatório
  - Email obrigatório e válido
  - Endereço completo opcional
  - Data de nascimento opcional

**RF-011:** O sistema deve permitir edição de dados do cliente
- **Pré-condição:** Cliente existente
- **Pós-condição:** Dados atualizados
- **Critérios de Aceitação:**
  - Validação de campos obrigatórios
  - Padronização de strings (nomes, endereços)

**RF-012:** O sistema deve permitir exclusão lógica de clientes
- **Pré-condição:** Cliente sem vínculos ativos
- **Pós-condição:** Cliente marcado como excluído
- **Critérios de Aceitação:**
  - Verificação de dependências (OS, vendas, lançamentos)
  - Status alterado para 0 (excluído)

### 2.5 Módulo Produtos

**RF-013:** O sistema deve permitir cadastro de produtos
- **Pré-condição:** Usuário logado com permissão 'aProduto'
- **Pós-condição:** Produto cadastrado
- **Critérios de Aceitação:**
  - Descrição obrigatória
  - Preço de compra opcional
  - Preço de venda obrigatório
  - Estoque inicial obrigatório
  - Estoque mínimo opcional

**RF-014:** O sistema deve controlar estoque automaticamente
- **Pré-condição:** Produto cadastrado
- **Pós-condição:** Estoque atualizado
- **Critérios de Aceitação:**
  - Entrada: vendas diminuem estoque
  - Saída: compras aumentam estoque
  - Validação de estoque mínimo
  - Bloqueio de vendas sem estoque

### 2.6 Módulo Serviços

**RF-015:** O sistema deve permitir cadastro de serviços
- **Pré-condição:** Usuário logado com permissão 'aServico'
- **Pós-condição:** Serviço cadastrado
- **Critérios de Aceitação:**
  - Nome obrigatório
  - Descrição opcional
  - Preço obrigatório (formato decimal)

### 2.7 Módulo Ordens de Serviço (OS)

**RF-016:** O sistema deve permitir criação de OS
- **Pré-condição:** Usuário logado com permissão 'aOs'
- **Pós-condição:** OS criada
- **Critérios de Aceitação:**
  - Cliente obrigatório
  - Responsável (usuário) obrigatório
  - Descrição do produto obrigatório
  - Status inicial: Aberto
  - Data inicial automática

**RF-017:** O sistema deve permitir adicionar produtos à OS
- **Pré-condição:** OS existente
- **Pós-condição:** Produto associado
- **Critérios de Aceitação:**
  - Produto selecionado obrigatório
  - Quantidade obrigatória
  - SubTotal calculado automaticamente
  - Estoque atualizado

**RF-018:** O sistema deve permitir adicionar serviços à OS
- **Pré-condição:** OS existente
- **Pós-condição:** Serviço associado
- **Critérios de Aceitação:**
  - Serviço selecionado obrigatório
  - SubTotal calculado automaticamente

**RF-019:** O sistema deve controlar status da OS
- **Pré-condição:** OS existente
- **Pós-condição:** Status atualizado
- **Critérios de Aceitação:**
  - Status: Aberto, Orçamento, Aprovado, Em Andamento, Cancelado, Pronto, Faturado
  - Transições controladas
  - Campo obrigatório

**RF-020:** O sistema deve permitir upload de anexos à OS
- **Pré-condição:** OS existente
- **Pós-condição:** Arquivo anexado
- **Critérios de Aceitação:**
  - Tipos permitidos: imagens, documentos
  - Tamanho máximo definido
  - Thumb gerado automaticamente

**RF-021:** O sistema deve permitir faturamento da OS
- **Pré-condição:** OS pronta
- **Pós-condição:** Lançamento financeiro gerado
- **Critérios de Aceitação:**
  - Status alterado para Faturado
  - Lançamento de receita criado
  - Valor total calculado

### 2.8 Módulo Vendas

**RF-022:** O sistema deve permitir criação de vendas
- **Pré-condição:** Usuário logado com permissão 'aVenda'
- **Pós-condição:** Venda criada
- **Critérios de Aceitação:**
  - Cliente obrigatório
  - Responsável obrigatório
  - Data automática
  - Status inicial: não faturado

**RF-023:** O sistema deve permitir adicionar produtos à venda
- **Pré-condição:** Venda existente
- **Pós-condição:** Produto associado
- **Critérios de Aceitação:**
  - Produto com estoque disponível
  - Quantidade obrigatória
  - SubTotal calculado
  - Estoque atualizado

**RF-024:** O sistema deve permitir faturamento de vendas
- **Pré-condição:** Venda criada
- **Pós-condição:** Lançamento financeiro gerado
- **Critérios de Aceitação:**
  - Status alterado para Faturado
  - Lançamento de receita criado
  - Desconto opcional

### 2.9 Módulo Usuários

**RF-025:** O sistema deve permitir cadastro de usuários
- **Pré-condição:** Usuário administrador
- **Pós-condição:** Usuário cadastrado
- **Critérios de Aceitação:**
  - Nome obrigatório
  - CPF único obrigatório
  - Email único obrigatório
  - Senha criptografada
  - Permissões atribuídas

**RF-026:** O sistema deve permitir edição de usuários
- **Pré-condição:** Usuário administrador
- **Pós-condição:** Dados atualizados
- **Critérios de Aceitação:**
  - Validação de campos únicos
  - Senha opcional (mantém atual se vazio)

### 2.10 Módulo Relatórios

**RF-027:** O sistema deve gerar relatórios financeiros
- **Pré-condição:** Dados existentes
- **Pós-condição:** Relatório gerado
- **Critérios de Aceitação:**
  - Filtros por período
  - Totais calculados
  - Formato de impressão

**RF-028:** O sistema deve gerar relatórios de clientes
- **Pré-condição:** Clientes existentes
- **Pós-condição:** Relatório gerado
- **Critérios de Aceitação:**
  - Histórico financeiro
  - Estatísticas de consumo

## 3. Requisitos Não Funcionais

### 3.1 Performance
**RNF-001:** O sistema deve responder consultas em até 2 segundos
**RNF-002:** O sistema deve suportar até 100 usuários simultâneos
**RNF-003:** O sistema deve processar relatórios em até 30 segundos

### 3.2 Segurança
**RNF-004:** Dados sensíveis devem ser criptografados (senhas, dados de cartão)
**RNF-005:** Sessões devem expirar após 2 horas de inatividade
**RNF-006:** Controle de permissões deve ser granular por módulo

### 3.3 Usabilidade
**RNF-007:** Interface deve ser responsiva (mobile/desktop)
**RNF-008:** Navegação deve ser intuitiva com breadcrumbs
**RNF-009:** Mensagens de erro devem ser claras e em português

### 3.4 Compatibilidade
**RNF-010:** Sistema deve funcionar em Chrome, Firefox, Safari, Edge
**RNF-011:** Sistema deve ser compatível com PHP 7.4+
**RNF-012:** Banco deve ser MySQL/MariaDB 5.7+

## 4. Regras de Negócio

### 4.1 Financeiras
- Valores positivos = entradas, negativos = saídas
- Compras parceladas criam faturas futuras automaticamente
- Primeiro cartão cadastrado vincula todas as faturas existentes
- Cartões adicionais precisam de aprovação do titular
- Faturas fechadas não podem ser alteradas

### 4.2 Controle de Estoque
- Vendas só podem ser feitas com produtos em estoque
- OS consomem estoque no momento da criação
- Controle automático de estoque mínimo
- Exclusão de produtos verifica dependências

### 4.3 Relacionamentos
- Cliente excluído logicamente mantém histórico
- OS faturada não pode ser alterada
- Usuário excluído perde acesso mas mantém dados
- Pendências pagas geram lançamentos automaticamente

### 4.4 Versionamento
- Formato: YYYY.Q.R (Ano.Trimestre.Release)
- Commits em inglês com mensagens curtas
- Pedir permissão antes de push para origin

## 5. Casos de Uso

### UC-001: Login no Sistema
**Ator:** Usuário
**Pré-condições:** Conta cadastrada
**Fluxo Principal:**
1. Usuário acessa página de login
2. Sistema apresenta formulário
3. Usuário informa email e senha
4. Sistema valida credenciais
5. Sistema inicia sessão
6. Sistema redireciona para dashboard

### UC-002: Cadastrar Cliente
**Ator:** Usuário com permissão
**Pré-condições:** Logado no sistema
**Fluxo Principal:**
1. Usuário acessa módulo Clientes
2. Sistema apresenta formulário
3. Usuário preenche dados obrigatórios
4. Sistema valida dados
5. Sistema salva cliente
6. Sistema exibe mensagem de sucesso

### UC-003: Registrar Compra no Cartão
**Ator:** Usuário com permissão
**Pré-condições:** Cartão ativo, fatura aberta
**Fluxo Principal:**
1. Usuário acessa fatura
2. Sistema apresenta formulário de lançamento
3. Usuário informa dados da compra
4. Sistema valida dados
5. Sistema associa à fatura atual
6. Se parcelada: cria parcelas em faturas futuras
7. Sistema atualiza valor da fatura

## 6. Critérios de Aceitação Gerais

- Todos os campos obrigatórios devem ser validados
- Mensagens de erro devem ser claras e específicas
- Dados devem ser padronizados (strings maiúsculas/minúsculas)
- Operações críticas devem ter confirmação
- Sistema deve manter integridade referencial
- Logs devem registrar operações importantes
- Interface deve ser consistente em todos os módulos

## 7. Considerações Técnicas

### 7.1 Banco de Dados
- Engine: InnoDB
- Charset: UTF-8
- Foreign Keys obrigatórias
- Índices otimizados para consultas frequentes

### 7.2 API
- Endpoints RESTful
- Autenticação por token
- Respostas em JSON
- Documentação Swagger

### 7.3 Logs
- Registro de todas as operações CRUD
- Logs de erro detalhados
- Auditoria de acessos

### 7.4 Backup
- Backup automático diário
- Restauração point-in-time
- Arquivos de configuração protegidos