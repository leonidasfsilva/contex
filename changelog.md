# Changelog

### 06/12/2023 - [2023.2.51]
### Release:
- adicionado novos títulos no cabeçalho da tabela de listagem de lançamentos em ```financeiro/lancamentos```
---
### 24/11/2023 - [2023.2.50]
### Bugfix:
- correções nos métodos de **Vincular fatura** e **Desvincular fatura**:
---
### 28/10/2023 - [2023.2.49]
### Feature:
- ajuste em ```mxcode/backup``` para não exibir *loading fade* ao clicar no link para baixar Backup
---
### 15/10/2023 - [2023.2.48]
### Feature:
- pequeno ajuste em ```financeiro/faturas/terceiros```
---
### 15/10/2023 - [2023.2.47]
### Feature:
- adicionado modal para copiar múltiplos lançamentos de uma uníca vez em ```financeiro/lancamentos```
---
### 15/10/2023 - [2023.2.46]
### Bugfix:
- correções nos formularios de **Copiar lançamento** e **Editar lançamento**:
    - corrigido comportamento: ao desmarcar a opção Compra de Terceiros o nome do terceiro era cadastrado no DB equivocadamente
---
### 13/10/2023 - [2023.2.45]
### Release:
- adicionado modal **Detalhes do Lançamento** na tela de relatório de compras de terceiros
- outras correções
---
### 12/10/2023 - [2023.2.44]
### Release:
- adicionado campo **Leitura anterior** nos modais de cadastro e edição de consumo de energia
- outras melhorias
---
### 01/10/2023 - [2023.2.43]
### Bugfix:
- corrigido bug do autofocus no campo Descrição nos modais de Novo Lançamento
---
### 29/09/2023 - [2023.2.42]
### Bugfix:
- corrigido titulo do campo **Nome do terceiro** nos modais de Copiar e Detalhes em ```financeiro/faturas```
- alterada classe **disabled** para **active** para o mes selecionado nos modais de seleção de mes
- outros ajustes
---
### 08/09/2023 - [2023.2.41]
### Bugfix:
- corrigido bug do campo Observações nos modais de Copiar
---
### 26/08/2023 - [2023.2.40]
### Release:
- alteradas as tags no cadastro de Cartões: de Labels para Badges em ```financeiro/cartoes```
- implementado botão/dropdown para selecionar o ano no modal de seleção de meses
---
### 14/08/2023 - [2023.2.39]
### Bugfix:
- correção de bug no filtro da página ```financeiro/cartoes```
---
### 13/08/2023 - [2023.2.38]
### Feature:
- implementado filtro de seleção de terceiros via dropdown em ```financeiro/faturas/terceiros```
---
### 07/08/2023 - [2023.2.37]
### Feature:
- implementado método para copiar saldo devedor ao clicar no valor em ```financeiro/faturas/terceiros```
---
### 07/08/2023 - [2023.2.36]
### Bugfix:
- correções de bugs em ```financeiro/cartoes```
---
### 29/07/2023 - [2023.1.35]
### Bugfix:
- correções de bugs
---
### 29/06/2023 - [2023.1.34]
### Release:
- ajuste do tempo de exibição do modal de Sucesso: de 3s para 1.2s
---
### 25/06/2023 - [2023.1.33]
### Bugfix:
- correções de bugs em ```financeiro/faturas/terceiros```
---
### 25/06/2023 - [2023.1.32]
### Release:
- início do changelog 
- exibição da data de vencimento da fatura em ```financeiro/faturas/terceiros```
- implementação de modal para seleção de meses em ```financeiro/faturas/terceiros```
- implementação de ícones de notificações nos botões de seleção de meses em ```financeiro/faturas/terceiros```
- ajustes nos links dos seletores de meses
- outros ajustes em métodos