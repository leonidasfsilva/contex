<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Api_auth
{
    protected ?string $token         = null;
    protected ?string $username      = null;
    protected ?array  $request       = null;
    protected ?object $apiClient     = null;
    protected ?string $userAgent     = null;
    protected bool    $authenticated = false;
    protected bool    $bearerToken   = false;

    protected $CI;

    public function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->model('mxcode_model');
    }

    public function authenticate(): bool
    {
        $this->userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
        $this->userAgent = strstr($this->userAgent, '/', true) ?: $this->userAgent;

        $this->setCredentialsFromInput();
        $this->setCredentialsFromJson();
        $this->setBearerToken();

        if (!$this->token) {
            return false;
        }

        $apiClient = $this->checkToken($this->username, $this->token, $this->bearerToken);

        if (!$apiClient) {
            return false;
        }

        $this->apiClient     = $apiClient;
        $this->authenticated = true;

        return true;
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    public function hasScope($scope): bool
    {
        if (!$this->apiClient || empty($this->apiClient->scopes)) {
            return false;
        }

        $scopes = array_filter(array_map('trim', explode(',', $this->apiClient->scopes)));

        if (in_array('*', $scopes)) {
            return true;
        }

        if (is_array($scope)) {
            foreach ($scope as $item) {
                if (!in_array($item, $scopes)) {
                    return false;
                }
            }

            return true;
        }

        return in_array($scope, $scopes);
    }

    public function getRequest(): ?array
    {
        return $this->request;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getApiClient(): ?object
    {
        return $this->apiClient;
    }

    public function isBearerToken(): bool
    {
        return $this->bearerToken;
    }

    protected function setCredentialsFromInput(): void
    {
        if ($_GET) {
            if ($this->CI->input->get('token')) {
                $this->token = $this->CI->input->get('token');
            }
            if ($this->CI->input->get('username')) {
                $this->username = $this->CI->input->get('username');
            }
        }

        if ($_POST) {
            if ($this->CI->input->post('token')) {
                $this->token = $this->CI->input->post('token');
            }
            if ($this->CI->input->post('username')) {
                $this->username = $this->CI->input->post('username');
            }
        }
    }

    protected function setCredentialsFromJson(): void
    {
        $this->request = json_decode(file_get_contents('php://input'), true);

        if (!$this->request || !is_array($this->request)) {
            return;
        }

        if (isset($this->request['token'])) {
            $this->token = $this->request['token'];
        }
        if (isset($this->request['username'])) {
            $this->username = $this->request['username'];
        }
    }

    protected function setBearerToken(): void
    {
        $bearerToken = $this->getBearerToken();

        if (!$bearerToken) {
            return;
        }

        $this->token       = $bearerToken;
        $this->bearerToken = true;
    }

    protected function checkToken($username, $token, $tokenFromBearer = false)
    {
        if ($tokenFromBearer) {
            return $this->CI->mxcode_model->getApiClientByToken($token)->row();
        }

        return $this->CI->mxcode_model->getTokenByToken($username, $token)->row();
    }

    protected function getBearerToken(): ?string
    {
        $authorization = $this->getAuthorizationHeader();

        if (!$authorization) {
            return null;
        }

        if (preg_match('/Bearer\s+(.+)/i', $authorization, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    protected function getAuthorizationHeader()
    {
        $authorization = $this->CI->input->server('HTTP_AUTHORIZATION');

        if (!$authorization) {
            $authorization = $this->CI->input->server('REDIRECT_HTTP_AUTHORIZATION');
        }

        if (!$authorization && function_exists('apache_request_headers')) {
            $authorization = $this->findAuthorizationHeader(apache_request_headers());
        }

        if (!$authorization && function_exists('getallheaders')) {
            $authorization = $this->findAuthorizationHeader(getallheaders());
        }

        return $authorization ?: null;
    }

    protected function findAuthorizationHeader($headers)
    {
        if (!is_array($headers)) {
            return null;
        }

        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'authorization') {
                return $value;
            }
        }

        return null;
    }
}
