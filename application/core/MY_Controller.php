<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
}

class API_Frontend_Controller extends MY_Controller
{
    /** @var ApiFrontendResponse */
    protected $apiResponse;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('ApiFrontendResponse');
        $this->apiResponse = $this->apifrontendresponse;

        if ($this->session->userdata('api_forced_logout') || $this->session->userdata('spa_forced_logout')) {
            $this->terminateWithError(
                'API_SESSION_REVOKED',
                'Sua sessão na API Frontend foi encerrada pelo administrador.',
                401
            );
        }

        if (!$this->session->userdata('logado')) {
            $this->terminateWithError(
                'SESSION_INVALID',
                'Sessão inválida ou expirada.',
                401
            );
        }
    }

    protected function requireMethod($method)
    {
        $expected = strtoupper((string) $method);
        $received = strtoupper((string) $this->input->method(true));

        if ($received !== $expected) {
            $this->terminateWithError(
                'INVALID_REQUEST',
                'A requisição enviada não corresponde ao contrato deste recurso.',
                400,
                array(
                    'expectedMethod' => $expected,
                    'receivedMethod' => $received,
                )
            );
        }
    }

    protected function requirePermission($permission)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), $permission)) {
            $this->terminateWithError(
                'PERMISSION_DENIED',
                'Você não possui permissão para acessar este recurso.',
                403
            );
        }
    }

    protected function requireCsrf()
    {
        $this->load->library('ApiCsrf', null, 'api_csrf');
        $token = $this->input->get_request_header('X-CSRF-TOKEN', true);

        if (!$this->api_csrf->isValid($token)) {
            $this->terminateWithError(
                'CSRF_TOKEN_INVALID',
                'Token CSRF ausente ou inválido.',
                403
            );
        }
    }

    protected function jsonInput()
    {
        $raw = trim((string) $this->input->raw_input_stream);

        if ($raw === '') {
            return array();
        }

        $data = json_decode($raw, true);

        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            $this->terminateWithError(
                'INVALID_JSON',
                'O corpo JSON enviado é inválido.',
                400
            );
        }

        return $data;
    }

    protected function positiveIntegerQuery($name, $default, $maximum = null)
    {
        $value = $this->input->get($name, true);

        if ($value === null || $value === '') {
            return (int) $default;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < 1) {
            $this->terminateWithError(
                'INVALID_QUERY_PARAMETER',
                'Um ou mais parâmetros da consulta são inválidos.',
                400,
                array('fields' => array($name => 'Informe um número inteiro maior que zero.'))
            );
        }

        $value = (int) $value;

        if ($maximum !== null && $value > $maximum) {
            $this->terminateWithError(
                'INVALID_QUERY_PARAMETER',
                'Um ou mais parâmetros da consulta são inválidos.',
                400,
                array('fields' => array($name => 'O valor máximo permitido é ' . (int) $maximum . '.'))
            );
        }

        return $value;
    }

    protected function terminateWithError($code, $message, $status, $details = null)
    {
        $this->apiResponse->error($code, $message, $status, $details);
        $this->output->_display();
        exit;
    }
}
