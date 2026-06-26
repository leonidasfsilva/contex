# Documento de Requisitos - Sistema de Gestão Empresarial

## 1. Visão Geral do Sistema

O sistema é uma aplicação web completa para gestão empresarial desenvolvida em PHP usando o framework CodeIgniter. Trata-se de um sistema multiusuário com controle de permissões que integra diversos módulos de gestão: financeiro, clientes, produtos, serviços, ordens de serviço, vendas, chamados de suporte, arquivos e usuários.

### 1.1 Arquitetura Técnica
- **Framework**: CodeIgniter 3.x
- **Banco de Dados**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript/jQuery, Bootstrap
- **Paradigma**: MVC (Model-View-Controller)
- **Sessões**: Database-driven sessions
- **Autenticação**: Password hashing com bcrypt

### 1.2 Ambiente de Configuração
- **Desenvolvimento**: localhost/contex
- **Produção**: contex.mxcode.net
- **Idioma**: Português Brasileiro
- **Timezone**: America/Sao_Paulo (UTC-3)

## 2. Módulos do Sistema

### 2.1 Módulo de Autenticação e Usuários

#### Funcionalidades:
- Login/logout com validação de credenciais
- Controle de permissões baseado em roles
- Gestão de usuários (CRUD)
- Recuperação de senha
- Perfil do usuário com foto de avatar
- Logs de auditoria

#### Regras de Negócio:
- Usuário administrador (ID=1) não pode ser desativado/excluído
- Validação de CPF único por usuário
- Senhas criptografadas com password_hash()
- Controle de permissões granular por módulo

#### Campos do Usuário:
- Nome, CPF, RG, email, senha
- Endereço completo (logradouro, número, complemento, bairro, cidade, UF, CEP)
- Telefone, celular
- Status (ativo/inativo)
- Permissões associadas

### 2.2 Módulo de Clientes

#### Funcionalidades:
- Cadastro completo de clientes (pessoa física/jurídica)
- Visualização detalhada com histórico
- Edição de dados
- Exclusão lógica (soft delete)
- Busca e filtros

#### Regras de Negócio:
- Cliente vinculado ao usuário logado
- Validação de email único
- Controle de pendências (crédito/débito) por cliente
- Histórico de OS vinculadas

#### Campos do Cliente:
- Nome, sexo, pessoa_física (boolean)
- Documento (CPF/CNPJ), telefone, celular, email
- Endereço completo
- Data de cadastro

### 2.3 Módulo de Produtos

#### Funcionalidades:
- Cadastro de produtos com controle de estoque
- Controle de preço de compra/venda
- Controle de estoque mínimo
- Ajuste automático de estoque em vendas/OS

#### Regras de Negócio:
- Estoque atualizado automaticamente em vendas e OS
- Controle de estoque mínimo
- Preços em formato monetário brasileiro

#### Campos do Produto:
- Descrição, unidade, preço_compra, preço_venda
- Estoque atual, estoque_mínimo

### 2.4 Módulo de Serviços

#### Funcionalidades:
- Cadastro de serviços com preços
- Vinculação a ordens de serviço
- Controle de preços

#### Campos do Serviço:
- Nome, descrição, preço

### 2.5 Módulo de Ordens de Serviço (OS)

#### Funcionalidades:
- Abertura de OS com cliente e responsável
- Adição de produtos e serviços
- Controle de status (Orçamento, Aberto, Em Andamento, etc.)
- Anexos de arquivos
- Faturamento direto
- Impressão de OS

#### Status da OS:
- Data Inicial/Final
- Garantia
- Descrição do produto/defeito
- Status atual
- Observações, laudo técnico
- Valor total
- Faturado (boolean)

#### Regras de Negócio:
- OS vinculada a cliente e usuário responsável
- Produtos retirados do estoque na adição
- Serviços podem ser adicionados sem retirada de estoque
- Possibilidade de anexar múltiplos arquivos
- Faturamento gera lançamento financeiro

### 2.6 Módulo de Vendas

#### Funcionalidades:
- Cadastro de vendas com cliente
- Adição de produtos à venda
- Controle de desconto
- Faturamento com geração de lançamento financeiro
- Impressão de venda

#### Regras de Negócio:
- Venda vinculada a cliente e vendedor
- Produtos retirados do estoque
- Desconto opcional
- Faturamento gera entrada no financeiro

#### Campos da Venda:
- Data da venda, valor total, desconto
- Cliente, vendedor
- Produtos vendidos com quantidades

### 2.7 Módulo Financeiro

#### 2.7.1 Lançamentos Financeiros

##### Funcionalidades:
- Lançamentos de receita e despesa
- Controle de data vencimento/pagamento
- Categorização por tipo
- Cliente/fornecedor associado
- Forma de pagamento
- Controle de baixado/oculto
- Filtros por período, status, tipo
- Auto-complete para descrições e fornecedores
- Pesquisa avançada

##### Tipos de Lançamento:
- Entrada (tipo = 1): valores positivos
- Saída (tipo = 2): valores negativos

##### Campos do Lançamento:
- Descrição, observações, valor
- Data lançamento, data pagamento
- Cliente/fornecedor, forma pagamento
- Tipo (entrada/saída), baixado, oculto
- Categoria, conta

#### 2.7.2 Cartões de Crédito

##### Funcionalidades:
- Cadastro de cartões com limite e vencimento
- Controle de faturas mensais
- Lançamento automático de compras
- Vinculação com lançamentos financeiros
- Controle de fechamento/pagamento de faturas
- Cartões adicionais (dependentes)
- Cartão principal por usuário
- Criptografia de dados sensíveis

##### Campos do Cartão:
- Bandeira, número (criptografado), validade
- Limite, dia vencimento
- Apelido, status (ativo/inativo)
- Cartão titular/adicional

##### Faturas:
- Mês/ano referência
- Valor total, status (aberta/fechada/paga)
- Data vencimento, data pagamento
- Vinculação automática com lançamentos
- Controle de terceiros (compras para outros)

#### 2.7.3 Despesas

##### Funcionalidades:
- Cadastro de despesas recorrentes/únicas
- Controle de parcelamento automático
- Vinculação automática com lançamentos
- Controle de vencimento mensal
- Integração automática com módulo financeiro
- Filtros avançados por período/status/tipo

##### Tipos de Despesa:
- Única (tipo = 1)
- Recorrente (tipo = 2)

##### Campos da Despesa:
- Descrição, fornecedor, nome_terceiro
- Valor parcela, valor total
- Dia vencimento, total parcelas
- Tipo despesa, forma pagamento
- Despesa parcelada, despesa terceiros, despesa oculta
- Auto vínculo

#### 2.7.4 Pendências

##### Funcionalidades:
- Controle de contas a receber/pagar
- Vinculação com clientes
- Controle de vencimento
- Quitação de pendências
- Geração automática de lançamentos financeiros
- Filtros por cliente/período/status

##### Tipos de Pendência:
- Crédito (tipo = 1): valores positivos
- Débito (tipo = 2): valores negativos

##### Campos da Pendência:
- Cliente, descrição, tipo
- Valor, data vencimento, data pagamento
- Quitado, forma pagamento

#### 2.7.5 Investimentos

##### Funcionalidades:
- Controle de aplicações e resgates
- Vinculação automática com lançamentos financeiros
- Controle de saldo de investimentos
- Filtros por período/status
- Débito automático da conta corrente

##### Tipos de Investimento:
- Aplicação (tipo = 1): valores positivos
- Resgate (tipo = 2): valores negativos

##### Campos do Investimento:
- Descrição, valor, data lançamento
- Forma pagamento, tipo

#### 2.7.6 Contas e Categorias

##### Contas:
- Banco, número da conta, saldo
- Tipo (corrente, poupança, investimento)

##### Categorias:
- Nome da categoria, tipo (receita/despesa)
- Status ativo/inativo

### 2.8 Módulo de Chamados de Suporte

#### Funcionalidades:
- Abertura de chamados por usuários
- Sistema de resposta (usuário ↔ administrador)
- Controle de status (Aberto, Respondido, Finalizado)
- Notificações automáticas

#### Regras de Negócio:
- Usuários comuns só veem seus próprios chamados
- Administradores veem todos os chamados
- Notificações de novas respostas

### 2.9 Módulo de Arquivos/Documentos

#### Funcionalidades:
- Upload de arquivos diversos
- Organização por data
- Download de arquivos
- Controle de tipos permitidos

#### Tipos Suportados:
- Imagens: jpg, png, gif, jpeg, bmp
- Documentos: pdf, docx, txt
- Outros: cdr

### 2.10 Módulo de Anúncios

#### Funcionalidades:
- Sistema de anúncios direcionados
- Controle de exibição por usuário
- Status habilitado/desabilitado

### 2.11 Módulo de Investimentos

#### Funcionalidades:
- Cadastro de investimentos
- Controle de valor aplicado
- Acompanhamento de rendimento

## 3. Regras de Negócio Gerais

### 3.1 Controle de Acesso
- Sistema multiusuário com isolamento de dados
- Controle granular de permissões por módulo
- Logs de auditoria para todas as ações

### 3.2 Validações
- Formatação brasileira para datas (DD/MM/YYYY)
- Formatação brasileira para valores monetários
- Validação de CPF/CNPJ
- Controle de unicidade de emails

### 3.3 Soft Delete
- Exclusão lógica em todas as entidades principais
- Campo `status` controla ativo/inativo
- Dados preservados para auditoria

### 3.4 Controle de Estoque
- Atualização automática em vendas e OS
- Alerta de estoque mínimo
- Controle de entrada/saída

### 3.5 Vinculação Financeira
- OS e vendas podem gerar lançamentos financeiros
- Faturas de cartão vinculadas automaticamente
- Controle de pagamentos pendentes

## 4. Fluxos de Processo

### 4.1 Fluxo de Venda
1. Cadastro/Seleção do cliente
2. Adição de produtos à venda
3. Aplicação de desconto (opcional)
4. Faturamento → geração de lançamento financeiro
5. Retirada automática do estoque

### 4.2 Fluxo de OS
1. Abertura de OS com cliente e responsável
2. Adição de produtos (retira do estoque)
3. Adição de serviços
4. Anexar arquivos (opcional)
5. Atualização de status
6. Faturamento → geração de lançamento financeiro

### 4.3 Fluxo Financeiro - Cartão de Crédito
1. Cadastro do cartão com dia de vencimento
2. Lançamento de compras na fatura
3. Fechamento automático da fatura no dia de vencimento
4. Pagamento da fatura → geração de lançamento financeiro

### 4.4 Fluxo de Chamados
1. Usuário abre chamado
2. Notificação para administrador
3. Administrador responde
4. Notificação para usuário
5. Usuário finaliza chamado

## 5. Estrutura do Banco de Dados

### 5.1 Tabelas Principais
- `usuarios` - Dados dos usuários
- `clientes` - Cadastro de clientes
- `produtos` - Catálogo de produtos
- `servicos` - Catálogo de serviços
- `os` - Ordens de serviço
- `produtos_os` - Produtos vinculados às OS
- `servicos_os` - Serviços vinculados às OS
- `vendas` - Registro de vendas
- `itens_de_vendas` - Produtos das vendas
- `lancamentos` - Lançamentos financeiros
- `cartoes` - Cartões de crédito
- `faturas` - Faturas dos cartões
- `lancamentos_faturas` - Lançamentos das faturas
- `categorias` - Categorias financeiras
- `contas` - Contas bancárias
- `chamados` - Sistema de chamados
- `anexos` - Arquivos anexados
- `documentos` - Arquivos do sistema
- `permissoes` - Controle de permissões
- `logs` - Logs de auditoria

### 5.2 Relacionamentos
- Usuário possui múltiplos clientes, produtos, OS, vendas, lançamentos
- Cliente possui múltiplas OS, vendas, pendências
- OS possui múltiplos produtos e serviços
- Venda possui múltiplos produtos
- Cartão possui múltiplas faturas
- Fatura possui múltiplos lançamentos

## 6. Interfaces e Usuário

### 6.1 Dashboard Principal
- Resumo financeiro (saldo, pendências)
- Widgets configuráveis por usuário
- Anúncios direcionados
- Menu lateral com módulos

### 6.2 Temas e Layout
- Tema responsivo com Bootstrap
- Menu lateral colapsível
- Tabelas com paginação
- Modais para ações rápidas
- Sistema de notificações

### 6.3 Funcionalidades Comuns
- Busca global
- Filtros avançados
- Exportação/Impressão
- Auto-complete em campos
- Validação client-side e server-side

## 7. Segurança

### 7.1 Autenticação
- Sessões armazenadas no banco
- Timeout automático
- Proteção CSRF desabilitada (configuração)
- Validação de senha forte

### 7.2 Autorização
- Controle de permissões por módulo
- Verificação em cada ação
- Isolamento de dados por usuário

### 7.3 Dados Sensíveis
- Senhas criptografadas
- Números de cartão criptografados
- Logs de auditoria

## 8. Configurações do Sistema

### 8.1 Configurações Gerais
- Idioma: pt-br
- Timezone: America/Sao_Paulo
- Logs desabilitados em produção
- Compressão de output desabilitada

### 8.2 Configurações de Cartão
- Dia de vencimento da fatura
- Vinculação automática de faturas
- Limite de crédito

### 8.3 Configurações de Emitente
- Dados da empresa para OS e vendas
- Logomarca
- Informações de contato

## 9. Requisitos Não Funcionais

### 9.1 Performance
- Paginação em listagens grandes
- Queries otimizadas
- Cache de sessões no banco

### 9.2 Usabilidade
- Interface intuitiva
- Auto-complete em campos
- Validações em tempo real
- Feedback visual de ações

### 9.3 Manutenibilidade
- Código organizado em MVC
- Helpers e libraries reutilizáveis
- Estrutura modular
- Documentação de código

### 9.4 Escalabilidade
- Estrutura preparada para múltiplos usuários
- Isolamento de dados por usuário
- Configurações flexíveis

Este documento representa uma análise completa de TODO o sistema, cobrindo todos os módulos identificados no código fonte, suas funcionalidades, regras de negócio e relacionamentos.