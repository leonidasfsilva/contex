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
    }
}
