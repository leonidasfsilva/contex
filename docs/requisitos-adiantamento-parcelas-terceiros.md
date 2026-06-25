# Requisitos - Adiantamento de Parcelas de Terceiros

## Contexto

O relatorio de compras de terceiros em faturas de cartao fica na view:

- `application/views/faturas/lancamentos_terceiros.php`

Atualmente o sistema lista compras realizadas por terceiros em cartoes do usuario, agrupadas por fatura/cartao e filtradas por mes, ano, cartao e nome do terceiro.

A view soma os valores de `valor_parcela` para calcular:

- saldo devedor por fatura;
- saldo devedor total do periodo.

Hoje nao existe controle para identificar quais parcelas ja foram adiantadas/pagas pelo terceiro.

## Objetivo

Permitir marcar uma parcela especifica de compra de terceiro como paga/adiantada pelo terceiro.

Essa marcacao deve ajudar o usuario a controlar o que ainda esta pendente de recebimento, sem confundir isso com o pagamento da fatura do cartao.

## Regra de Negocio

- A compra fica registrada em `lancamentos_faturas`.
- Cada parcela fica registrada em `lancamentos_faturas_assoc`.
- A marcacao de pagamento antecipado deve ser feita por parcela, portanto deve ficar em `lancamentos_faturas_assoc`.
- A identificacao da parcela deve usar `id_assoc`.
- `id_lancamento` identifica a compra/serie e nao deve ser usado sozinho para marcar uma parcela especifica.
- Uma compra parcelada pode ter algumas parcelas pagas pelo terceiro e outras ainda em aberto.
- Parcelas marcadas como pagas pelo terceiro nao devem compor o saldo devedor do terceiro no mes/fatura.
- Deve existir acao reversa para desmarcar uma parcela como paga.
- O botao de acao nao deve ser desabilitado; deve alternar estado/icone conforme a parcela esteja paga ou em aberto.

## Fora do Escopo Inicial

- Marcar uma compra inteira como paga/adiantada.
- Pagamentos parciais de uma mesma parcela.
- Comprovantes de pagamento.
- Conciliacao bancaria.
- Motivo obrigatorio para reversao.

Esses itens podem virar features separadas.

## Alteracoes de Banco Previstas

Tabela: `lancamentos_faturas_assoc`

Campos sugeridos:

```sql
pagamento_terceiro TINYINT DEFAULT 0
data_pagamento_terceiro DATE NULL
```

Campos adicionais podem ser avaliados depois, conforme necessidade de auditoria direta na propria tabela.

## Auditoria

A feature deve registrar log ao marcar e desmarcar uma parcela como paga.

A preferencia e integrar com a tabela `logs` existente, desde que ela passe a suportar contexto estruturado.

Campos sugeridos para evolucao da tabela `logs`:

```sql
modulo VARCHAR(50) NULL
referencia_tabela VARCHAR(80) NULL
referencia_id INT NULL
acao VARCHAR(80) NULL
metadata JSON NULL
```

Exemplo de log ao marcar uma parcela:

```text
modulo: faturas
referencia_tabela: lancamentos_faturas_assoc
referencia_id: 123
acao: marcar_pagamento_terceiro
descricao: Parcela de terceiro marcada como paga
```

Exemplo de `metadata`:

```json
{
  "id_lancamento": 456,
  "id_fatura": 789,
  "terceiro": "FULANO",
  "valor_parcela": "120.50",
  "parcela": "03/10",
  "mes_referencia": "06",
  "ano_referencia": "2026"
}
```

O campo `metadata` deve guardar detalhes do evento no momento da acao, para que o historico continue compreensivel mesmo se dados da compra forem alterados depois.

## Historico da Parcela

Em etapa posterior, a view pode exibir um botao dedicado para abrir modal com o historico da parcela.

Esse modal deve consultar os logs por:

```sql
modulo = 'faturas'
referencia_tabela = 'lancamentos_faturas_assoc'
referencia_id = id_assoc
```

Eventos esperados:

- parcela marcada como paga;
- pagamento da parcela desmarcado;
- futuras alteracoes relacionadas ao pagamento antecipado.

## Impacto na View

Na tabela de `lancamentos_terceiros.php`, cada linha deve passar a considerar o status de pagamento do terceiro.

Comportamentos esperados:

- exibir visualmente se a parcela esta paga ou em aberto;
- permitir marcar como paga;
- permitir desmarcar como paga;
- alternar icone/estilo da acao conforme o estado;
- recalcular os totais ignorando parcelas pagas pelo terceiro.

## Impacto no Controller e Model

Controller provavel:

- `application/controllers/financeiro/Faturas.php`

Model provavel:

- `application/models/Fatura_model.php`

Novos comportamentos esperados:

- validar permissao antes de marcar/desmarcar;
- validar se o `id_assoc` pertence ao usuario logado;
- atualizar a flag de pagamento do terceiro;
- registrar log estruturado;
- redirecionar para a URL atual mantendo os filtros da view.

## Observacoes

Esta feature controla o pagamento feito pelo terceiro ao usuario.

Ela nao deve alterar automaticamente:

- pagamento da fatura do cartao;
- status financeiro da fatura;
- baixa de lancamentos financeiros principais.
