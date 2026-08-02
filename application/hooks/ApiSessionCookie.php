<?php

defined('BASEPATH') or exit('No direct script access allowed');

function selectApiSessionCookie()
{
    $requestUri = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $basePath   = rtrim(str_replace('index.php', '', $scriptName), '/');
    $routePath  = ltrim(substr($requestUri, strlen($basePath)), '/');

    if (strpos($routePath, 'api/frontend/v1/') === 0) {
        $GLOBALS['CFG']->set_item('sess_cookie_name', 'api_session');
        $GLOBALS['CFG']->set_item('sess_expiration', 7200);

        // A consulta periódica do SPA apenas valida a sessão e, por projeto,
        // não renova seu tempo de inatividade no banco. Se o CodeIgniter
        // regenerar o ID durante essa requisição passiva, o driver também não
        // deve persistir a nova linha e o cookie passa a apontar para uma
        // sessão inexistente. Requisições funcionais da API continuam usando
        // normalmente a rotação configurada em sess_time_to_update.
        if (
            $routePath === 'api/frontend/v1/auth/session'
            && strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '') === 'GET'
        ) {
            $GLOBALS['CFG']->set_item('sess_time_to_update', 0);
        }
    }
}
