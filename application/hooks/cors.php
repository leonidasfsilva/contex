<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Permite que o frontend local consuma o backend MVC por origem cruzada.
 * A lista deve ser expandida explicitamente quando o ambiente de produção
 * estiver pronto; nunca aceitar a origem recebida sem validacao.
 */
$allowedOrigins = array();

switch (ENVIRONMENT) {
    case 'development':
        $allowedOrigins = array(
            'https://contex-spa.local',
        );
        break;

    case 'production':
        $allowedOrigins = array(
            'https://contex-spa.devply.net',
        );
        break;
}

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Accept, Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
    header('Access-Control-Max-Age: 86400');
    header('Vary: Origin');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS' && in_array($origin, $allowedOrigins, true)) {
    http_response_code(204);
    exit;
}
