# Principais Conflitos e Incompatibilidades CI3 → CI4

## 1. **Estrutura de Diretórios e Arquivos**
- **CI3**: Estrutura tradicional com `application/`, `system/`, `index.php` na raiz
- **CI4**: Nova estrutura com `app/`, `public/`, `writable/`, etc. O arquivo `index.php` deve estar em `public/`
- **Impacto**: Reorganização completa dos diretórios, especialmente assets, views e controllers

## 2. **Sintaxe de Controllers**
- **CI3**: `extends CI_Controller`
- **CI4**: `extends BaseController` ou `extends Controller`
- **Impacto**: Todos os controllers precisam ser refatorados

## 3. **Sintaxe de Models**
- **CI3**: `extends CI_Model`
- **CI4**: `extends Model` (com traits específicos)
- **Impacto**: Models precisam ser reescritos seguindo o padrão Active Record do CI4

## 4. **Loading de Libraries e Helpers**
- **CI3**: `$this->load->library()`, `$this->load->helper()`
- **CI4**: Services ou injeção de dependência
- **Impacto**: Sistema de carregamento completamente diferente

## 5. **Database Configuration**
- **CI3**: Array de configuração em `application/config/database.php`
- **CI4**: Arquivo `.env` para configurações sensíveis
- **Impacto**: Migração das configurações de banco para variáveis de ambiente

## 6. **Session Management**
- **CI3**: `$this->session->userdata()`, `session_start()`
- **CI4**: Sistema de sessão completamente redesenhado
- **Impacto**: Refatoração completa do gerenciamento de sessões

## 7. **Input Class**
- **CI3**: `$this->input->post()`, `$this->input->get()`
- **CI4**: `request()->getPost()`, `request()->getGet()`
- **Impacto**: Todas as chamadas de input precisam ser atualizadas

## 8. **URL Helper e Base URL**
- **CI3**: `base_url()`, configuração em config.php
- **CI4**: Sistema diferente, configuração via `.env`
- **Impacto**: Reconfiguração das URLs base

## 9. **Form Validation**
- **CI3**: `$this->form_validation->set_rules()`
- **CI4**: Sistema de validação redesenhado
- **Impacto**: Reescrita das regras de validação

## 10. **File Upload**
- **CI3**: `$this->upload->do_upload()`
- **CI4**: Sistema de upload redesenhado
- **Impacto**: Refatoração do upload de arquivos

## 11. **Pagination**
- **CI3**: `$this->pagination->initialize()`
- **CI4**: Sistema de paginação diferente
- **Impacto**: Reimplementação da paginação

## 12. **Security Features**
- **CI3**: CSRF protection básica
- **CI4**: Sistema de segurança aprimorado
- **Impacto**: Reconfiguração da proteção CSRF

## 13. **Autoloading**
- **CI3**: `application/config/autoload.php`
- **CI4**: Composer autoloading e `app/Config/Autoload.php`
- **Impacto**: Migração do sistema de autoloading

## 14. **Namespaces**
- **CI3**: Sem namespaces obrigatórios
- **CI4**: Uso obrigatório de namespaces
- **Impacto**: Adição de namespaces em todas as classes

## 15. **Routes**
- **CI3**: `application/config/routes.php`
- **CI4**: `app/Config/Routes.php` com sintaxe diferente
- **Impacto**: Reescrita das rotas

## 16. **Views Loading**
- **CI3**: `$this->load->view()`
- **CI4**: `echo view()` ou métodos diferentes
- **Impacto**: Mudança na forma de carregar views

## 17. **Hooks**
- **CI3**: Sistema de hooks em config/hooks.php
- **CI4**: Sistema de eventos
- **Impacto**: Reimplementação dos hooks como eventos

## 18. **Encryption**
- **CI3**: `$this->encrypt->encode()`
- **CI4**: Sistema de criptografia diferente
- **Impacto**: Refatoração da criptografia de dados

## 19. **Query Builder**
- **CI3**: `$this->db->query()`
- **CI4**: Mantém similar, mas com melhorias
- **Impacto**: Possíveis ajustes nas queries

## 20. **Third Party Libraries**
- **CI3**: Integração direta
- **CI4**: Via Composer preferencialmente
- **Impacto**: Migração para Composer

## 21. **Error Handling**
- **CI3**: Sistema básico
- **CI4**: Sistema de logging aprimorado
- **Impacto**: Reconfiguração do tratamento de erros

## 22. **Caching**
- **CI3**: Sistema de cache
- **CI4**: Sistema de cache redesenhado
- **Impacto**: Reconfiguração do cache

## 23. **Email**
- **CI3**: `$this->email->send()`
- **CI4**: Sistema de email redesenhado
- **Impacto**: Refatoração do envio de emails

## 24. **File Handling**
- **CI3**: Funções básicas
- **CI4**: Sistema de arquivos aprimorado
- **Impacto**: Ajustes no manuseio de arquivos

## 25. **Benchmarking**
- **CI3**: `$this->benchmark->elapsed_time()`
- **CI4**: Sistema diferente
- **Impacto**: Mudança no benchmarking

---

> **Nota**: Estes são os principais pontos de conflito identificados na análise do projeto. A migração requer uma abordagem cuidadosa, especialmente considerando que o projeto tem funcionalidades complexas como gestão de faturas, cartões de crédito, lançamentos financeiros e integração com módulos de terceiros.