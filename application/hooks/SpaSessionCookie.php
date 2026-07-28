<?php

defined('BASEPATH') or exit('No direct script access allowed');

function selectSpaSessionCookie()
{
    $requestUri = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $basePath   = rtrim(str_replace('index.php', '', $scriptName), '/');
    $routePath  = ltrim(substr($requestUri, strlen($basePath)), '/');

    if (strpos($routePath, 'api/v1/') === 0) {
        removeLegacySpaSessionCookie();
        $GLOBALS['CFG']->set_item('sess_cookie_name', 'api_session');
        $GLOBALS['CFG']->set_item('sess_expiration', 60);
    }
}

function removeLegacySpaSessionCookie()
{
    if (!isset($_COOKIE['spa_session'])) {
        return;
    }

    $cookieOptions = array(
        'expires'  => 1,
        'path'     => config_item('cookie_path') ?: '/',
        'domain'   => config_item('cookie_domain') ?: '',
        'secure'   => (bool)config_item('cookie_secure'),
        'httponly' => true,
        'samesite' => config_item('sess_samesite') ?: 'Lax',
    );

    setcookie('spa_session', '', $cookieOptions);
    unset($_COOKIE['spa_session']);
}
