# Sistema CONTEX - Gestão Financeira Pessoal

Sistema web desenvolvido em PHP/CodeIgniter para gestão financeira pessoal completa.

## 🚀 Funcionalidades

- **Financeiro Completo**: Lançamentos, faturas de cartão, investimentos, despesas, pendências
- **Gestão de Clientes**: Cadastro completo com histórico financeiro
- **Produtos e Serviços**: Controle de estoque e serviços oferecidos
- **Ordens de Serviço**: Gestão técnica com anexos e faturamento
- **Vendas**: Controle de vendas com atualização automática de estoque
- **Relatórios**: Análises financeiras e estatísticas
- **Usuários e Permissões**: Controle granular de acessos

## 📋 Requisitos do Sistema

- PHP 7.4+
- MySQL/MariaDB 5.7+
- CodeIgniter 3.x
- Composer

## 🛠️ Instalação

1. Clone o repositório
2. Execute `composer install`
3. Configure o banco de dados em `application/config/database.php`
4. Execute o script SQL em `docs/script.sql`
5. Configure os hooks do Git: `./scripts/setup-hooks.sh`

## 🔧 Configuração dos Git Hooks

Os Git Hooks automatizam tarefas importantes do desenvolvimento:

### Versionamento Automático
- **Arquivo**: `.git/hooks/pre-commit`
- **Função**: Incrementa automaticamente a versão em `constants.php`
- **Quando**: Antes de cada commit

### Como Configurar
```bash
# Executar uma vez após clonar/reformatar PC
./scripts/setup-hooks.sh
```

### Como Funciona
1. Você faz um commit normal
2. Hook executa automaticamente
3. Versão é incrementada (+1)
4. Arquivo é adicionado ao commit
5. Commit prossegue normalmente

### Exemplo
```bash
git add .
git commit -m "Add new feature"
# Hook mostra: Versão atual: 155 → Nova versão: 156
```

## 📚 Documentação

- **Documento de Funcionalidades**: `documento_funcionalidades.docx`
- **Documento de Requisitos**: `doc_requisitos.docx`

## 📝 Regras de Desenvolvimento

### Commits
- Mensagens curtas em inglês
- Solicitar permissão antes de push para origin

### Versionamento
- Formato: YYYY.Q.R (Ano.Trimestre.Release)
- Incremento automático via Git Hook

## 🔐 Segurança

- Dados de cartão criptografados
- Sessões database-driven
- Controle granular de permissões
- Senhas criptografadas

## 📊 Estrutura do Banco

- **Clientes**: Gestão completa de clientes
- **Produtos**: Controle de estoque
- **Serviços**: Catálogo de serviços
- **Lançamentos**: Movimentações financeiras
- **Faturas**: Controle de cartão de crédito
- **OS**: Ordens de serviço
- **Vendas**: Histórico de vendas
- **Usuários**: Controle de acessos

## 🎯 Casos de Uso Principais

1. **Login no Sistema**
2. **Cadastro de Cliente**
3. **Registro de Compra no Cartão**
4. **Criação de Ordem de Serviço**
5. **Faturamento de Vendas**

## 📞 Suporte

Para dúvidas ou problemas, consulte a documentação ou entre em contato com o desenvolvedor.