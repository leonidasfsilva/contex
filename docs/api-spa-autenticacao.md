# Contrato de autenticação do Contex SPA

## Escopo

O Contex SPA utiliza autenticação por sessão própria, independente da sessão do frontend MVC. O backend continua sendo a autoridade sobre sessão, usuário e permissões.

Este contrato se aplica às rotas `/api/v1/auth/*`. As APIs existentes autenticadas por Bearer Token permanecem independentes.

## Variáveis do frontend

O bundle Vue pode receber apenas configurações públicas:

| Variável | Exemplo DEV | Finalidade |
| --- | --- | --- |
| `VITE_APP_URL` | `https://contex-spa.local` | Origem pública do SPA. |
| `VITE_API_BASE_URL` | `https://contex.local/api/v1` | Base pública dos endpoints consumidos pelo SPA. |

Variáveis iniciadas por `VITE_` são incorporadas ao bundle e podem ser lidas no navegador. Nunca armazenar nelas senhas, tokens administrativos, chaves privadas, credenciais de banco de dados ou segredos de integração.

## Sessão e credenciais

- O cookie `api_session` identifica exclusivamente a sessão da API fake consumida pelo Contex SPA.
- O MVC continua usando `app_session`; um login MVC não autentica o SPA e um login SPA não autentica o MVC.
- O navegador armazena e envia esse cookie automaticamente nas requisições com credenciais.
- O cliente HTTP do SPA deve usar `credentials: include` ou `withCredentials: true`.
- O Vue não usa dados locais como prova de autenticação; a confirmação vem de `GET /api/v1/auth/session`.

## Token CSRF

- O token CSRF é uma string hexadecimal aleatória de 64 caracteres associada à sessão.
- O backend o entrega no campo JSON `csrfToken` após login ou consulta de sessão.
- O SPA mantém o token em memória e o envia no header `X-CSRF-TOKEN` nas operações protegidas.
- O token permanece estável durante a mesma sessão e deixa de ser válido quando a sessão é encerrada.
- O token CSRF não autentica o usuário e não substitui o cookie `api_session`.
- O CSRF global do CodeIgniter permanece desativado para preservar os formulários e AJAX legados do MVC.

## `POST /api/v1/auth/login`

Corpo JSON:

```json
{
  "email": "usuario@example.com",
  "password": "senha"
}
```

Resposta de sucesso: `200 OK`.

```json
{
  "authenticated": true,
  "user": {
    "id": "2",
    "name": "Usuário",
    "email": "usuario@example.com",
    "avatar": "avatar.png",
    "permissionId": "1"
  },
  "permissions": {},
  "csrfToken": "string-hexadecimal-com-64-caracteres"
}
```

Erros esperados:

- `401 Unauthorized`: credenciais inválidas.
- `403 Forbidden`: conta desativada.
- `422 Unprocessable Entity`: dados ausentes ou inválidos.
- `503 Service Unavailable`: manutenção impede o acesso do usuário.

O login não exige CSRF porque ainda não existe uma sessão autenticada a proteger.

## `GET /api/v1/auth/session`

O navegador envia `api_session`. Quando a sessão for válida, a resposta será `200 OK` com o mesmo contrato do login, incluindo `csrfToken`.

Sem sessão válida, retorna `401 Unauthorized`:

```json
{
  "authenticated": false,
  "message": "Sessão inválida ou expirada."
}
```

O SPA deve consultar esta rota ao iniciar e ao retomar o aplicativo depois de tempo relevante em segundo plano.

## `POST /api/v1/auth/logout`

Quando existir sessão autenticada, enviar:

```http
X-CSRF-TOKEN: token-retornado-pelo-login-ou-session
```

Respostas:

- `204 No Content`: sessão encerrada, ou logout chamado sem sessão.
- `403 Forbidden`: sessão autenticada com token ausente ou inválido.

```json
{
  "code": "CSRF_TOKEN_INVALID",
  "message": "Token CSRF ausente ou inválido."
}
```

Uma consulta autenticada depois do logout deve retornar `401 Unauthorized`.

## Futuras operações de escrita

Rotas SPA autenticadas por `api_session` que alterem dados por `POST`, `PUT`, `PATCH` ou `DELETE` devem reutilizar a validação do header `X-CSRF-TOKEN` antes de executar a regra de negócio.

O contrato esperado é:

- `401 Unauthorized`: sessão ausente ou expirada.
- `403 Forbidden` com `CSRF_TOKEN_INVALID`: token CSRF ausente ou inválido.
- `403 Forbidden`: usuário autenticado sem a permissão funcional necessária.

## CORS

- DEV permite explicitamente `https://contex-spa.local`.
- PRD permite explicitamente `https://contex-spa.devply.net`.
- Credenciais são habilitadas.
- `X-CSRF-TOKEN` é permitido no preflight.
- Cookies nunca são combinados com `Access-Control-Allow-Origin: *`.
