<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class GetInfo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('ApiAuth', null, 'api_auth');
    }

    public function index()
    {
        if (strtolower($this->input->method(true)) !== 'get') {
            return $this->response(
                ['response' => 'Error 405 Method Not Allowed'],
                405
            );
        }

        if (!$this->api_auth->authorize()) {
            $code = $this->api_auth->getAuthorizationCode();
            if ($code !== 429) {
                gravaLog(null, null, null, 'Unauthorized: Tentativa de acesso recusada de getInfo da API Mikrotik', getenv("REMOTE_ADDR"), 'api', '/api/v1/mikrotik/getinfo');
            }
            return $this->response(
                ['response' => $this->api_auth->getAuthorizationMessage()],
                $code
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
