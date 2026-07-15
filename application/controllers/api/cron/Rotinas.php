<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Rotinas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api_auth');
        $this->api_auth->authenticate();
    }

    public function financeiro()
    {
        if (strtolower($this->input->method(true)) != 'post') {
            return $this->response(
                ['response' => 'Error 405 Method Not Allowed'],
                405
            );
        }

        if (!$this->api_auth->isAuthenticated() || !$this->api_auth->hasScope(['cron', 'financeiro'])) {
            gravaLog(null, null, null, 'Unauthorized: Tentativa recusada de execução da consolidação financeira via cron', getenv("REMOTE_ADDR"));
            return $this->response(
                ['response' => 'Error 401 Unauthorized'],
                401
            );
        }

        $resultado = reconciliarFinanceiro(null, 'cron');
        $code      = ($resultado['erro'] ?? 0) ? 500 : 200;

        gravaLog(null, null, null, $this->getMensagemLogFinanceiro($resultado, $code), getenv("REMOTE_ADDR"));

        return $this->response(
            [
                'response'  => $code == 200 ? '200 OK' : 'Error 500 Internal Server Error',
                'rotina'    => 'financeiro',
                'origem'    => 'cron',
                'resultado' => $resultado,
            ],
            $code
        );
    }

    private function response($response = [], $code = 200)
    {
        if (!is_array($response)) {
            return false;
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($code)
            ->set_output(json_encode($response, JSON_PRETTY_PRINT));
    }

    private function getMensagemLogFinanceiro($resultado, $code)
    {
        $status  = $code == 200 ? 'sucesso' : 'erro';
        $total   = $resultado['total'] ?? 0;
        $sucesso = $resultado['sucesso'] ?? 0;
        $erro    = $resultado['erro'] ?? 0;

        return sprintf(
            'Consolidação financeira via cron executada com %s. Total: %s, sucesso: %s, erro: %s',
            $status,
            $total,
            $sucesso,
            $erro
        );
    }
}
