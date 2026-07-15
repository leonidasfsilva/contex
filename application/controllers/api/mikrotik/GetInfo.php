<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class GetInfo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api_auth');
        $this->api_auth->authenticate();
    }

    public function index()
    {
        if (!$this->api_auth->isAuthenticated()) {
            gravaLog(null, null, null, 'Unauthorized: Tentativa de acesso recusada de getInfo da API Mikrotik', getenv("REMOTE_ADDR"));
            return $this->response(
                ['response' => 'Error 401 Unauthorized'],
                401
            );
        }

        return $this->response([
            'appVersion:' => APP_VERSION,
            'phpServer:'  => phpversion(),
        ]);
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
}
