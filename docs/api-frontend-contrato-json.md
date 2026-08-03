# Contrato JSON da API Frontend

## Escopo

Este contrato aplica-se às rotas funcionais em `/api/frontend/v1/*`. A API real em `/api/v1/*`, autenticada por Bearer Token, não é alterada.

As rotas de autenticação em `/api/frontend/v1/auth/*` preservam o contrato específico já homologado. Os módulos funcionais usam os envelopes descritos abaixo.

## Tipos e nomenclatura

- Campos públicos usam `camelCase`.
- IDs e contadores são números inteiros.
- Valores monetários são strings decimais, por exemplo, `"1250.90"`.
- Datas usam `YYYY-MM-DD`, sem horário.
- Datetimes usam ISO 8601 completo, por exemplo, `2026-07-30T14:35:00-03:00`.
- Datetime representa data, hora, minuto, segundo e fuso horário. Segundos não devem ser exibidos obrigatoriamente na interface.
- Booleanos são valores JSON `true` ou `false`, sem aspas.
- Dados ausentes são representados por `null`, sem aspas.
- Objetos do banco não são publicados integralmente; cada endpoint mapeia explicitamente seus campos públicos.

## Sucesso

### Listagem

```json
{
  "success": true,
  "data": {
    "results": []
  },
  "meta": {
    "pagination": {
      "page": 1,
      "perPage": 30,
      "total": 0,
      "totalPages": 0
    },
    "filters": {},
    "sort": []
  }
}
```

Uma listagem vazia é uma resposta de sucesso e usa `results: []`.

### Item único ou ação

```json
{
  "success": true,
  "data": {}
}
```

### Criação

Uma criação bem-sucedida retorna `201 Created` com o recurso criado em `data`.

### Operação sem corpo

Uma exclusão ou ação concluída sem representação retorna `204 No Content`.

## Erro

```json
{
  "success": false,
  "error": {
    "code": "INVALID_REQUEST",
    "message": "A requisição enviada não corresponde ao contrato deste recurso.",
    "details": {}
  }
}
```

- `code` é estável e destinado ao tratamento do cliente.
- `message` é legível para o usuário.
- `details` é opcional e pode conter campos inválidos ou contexto seguro.

## Status HTTP

- `200`: leitura ou atualização concluída.
- `201`: criação concluída.
- `204`: operação concluída sem corpo.
- `400`: request incompatível com o contrato, JSON malformado, parâmetro inválido ou verbo incorreto.
- `401`: sessão ausente, expirada ou revogada.
- `403`: permissão insuficiente ou CSRF inválido.
- `404`: recurso inexistente.
- `409`: duplicidade ou estado atual incompatível com a operação solicitada, quando aplicável ao módulo.
- `422`: dados sintaticamente válidos, mas rejeitados por validação funcional.
- `500`: erro interno inesperado.
- `503`: API Frontend indisponível.

O projeto optou por responder `400 Bad Request`, e não `405 Method Not Allowed`, quando uma rota funcional recebe um verbo diferente daquele definido em seu contrato.

## Endpoint piloto

### `GET /api/frontend/v1/financeiro/lancamentos`

Parâmetros:

| Campo | Tipo | Padrão | Regra |
| --- | --- | --- | --- |
| `page` | inteiro | `1` | Maior que zero. |
| `perPage` | inteiro | `30` | Entre `1` e `100`. |
| `search` | string | `null` | Pesquisa descrição, contraparte e observações. |
| `sortDirection` | string | `desc` | Aceita `asc` ou `desc`. |

Exemplo:

```json
{
  "success": true,
  "data": {
    "results": [
      {
        "id": 123,
        "description": "Compra no mercado",
        "amount": "-125.90",
        "transactionDate": "2026-07-30",
        "paymentDate": "2026-07-30",
        "paid": true,
        "provider": "Mercado Exemplo",
        "paymentMethodId": 2,
        "type": 2,
        "notes": null,
        "hidden": false
      }
    ]
  },
  "meta": {
    "pagination": {
      "page": 1,
      "perPage": 30,
      "total": 1,
      "totalPages": 1
    },
    "filters": {
      "search": null
    },
    "sort": [
      {
        "field": "transactionDate",
        "direction": "desc"
      },
      {
        "field": "id",
        "direction": "desc"
      }
    ]
  }
}
```

## CRUD de lançamentos

### Leitura individual

```http
GET /api/frontend/v1/financeiro/lancamentos/{id}
```

Retorna `200` com o lançamento em `data` ou `404` com `TRANSACTION_NOT_FOUND`. Um recurso inexistente e um recurso pertencente a outro usuário produzem a mesma resposta.

### Criação

```http
POST /api/frontend/v1/financeiro/lancamentos
X-CSRF-TOKEN: token-da-sessao
Content-Type: application/json
```

```json
{
  "description": "Compra no mercado",
  "amount": "125.90",
  "transactionDate": "2026-07-30",
  "paymentDate": "2026-07-30",
  "paid": true,
  "provider": "Mercado Exemplo",
  "paymentMethodId": 2,
  "type": 2,
  "notes": null,
  "hidden": false
}
```

O valor de `amount` é sempre positivo no request. O campo `type` determina se o backend persiste entrada (`1`) ou saída (`2`). A resposta usa `201 Created`.

### Atualização completa

```http
PUT /api/frontend/v1/financeiro/lancamentos/{id}
X-CSRF-TOKEN: token-da-sessao
Content-Type: application/json
```

O corpo utiliza o mesmo contrato da criação. A resposta usa `200` e devolve o estado atualizado em `data`.

### Exclusão lógica

```http
DELETE /api/frontend/v1/financeiro/lancamentos/{id}
X-CSRF-TOKEN: token-da-sessao
```

A exclusão preserva o padrão atual do Contex, alterando o estado lógico do registro. A resposta usa `204 No Content`.

### Validação

- JSON malformado ou request incompatível: `400`.
- CSRF ausente ou inválido: `403`.
- Recurso inexistente ou pertencente a outro usuário: `404`.
- Campos inválidos: `422` com `VALIDATION_ERROR` e `error.details.fields`.
