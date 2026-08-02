<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FrontendRequestContext
{
    /** @var CI_Controller */
    private $CI;

    /** @var array|null */
    private $jsonInput;

    public function __construct()
    {
        $this->CI = get_instance();
    }

    public function expectsJson()
    {
        return strpos(uri_string(), 'api/frontend/v1/') === 0;
    }

    public function method()
    {
        return strtoupper((string) $this->CI->input->method(true));
    }

    public function jsonInput()
    {
        if ($this->jsonInput !== null) {
            return $this->jsonInput;
        }

        $raw = trim((string) $this->CI->input->raw_input_stream);

        if ($raw === '') {
            return $this->jsonInput = array();
        }

        $decoded = json_decode($raw, true);
        $this->jsonInput = is_array($decoded) ? $decoded : array();

        return $this->jsonInput;
    }

    public function jsonIsValid()
    {
        $raw = trim((string) $this->CI->input->raw_input_stream);

        if ($raw === '') {
            return true;
        }

        json_decode($raw, true);

        return json_last_error() === JSON_ERROR_NONE;
    }

    public function applyLancamentoJsonToPost($id = null)
    {
        if (!$this->expectsJson()) {
            return;
        }

        $input = $this->jsonInput();
        $transactionDate = $this->toLegacyDate($input['transactionDate'] ?? null);
        $paymentDate = $this->toLegacyDate($input['paymentDate'] ?? null);

        $_POST = array(
            'id'          => $id ?? ($input['id'] ?? null),
            'descricao'   => $input['description'] ?? null,
            'valor'       => $input['amount'] ?? null,
            'vencimento'  => $transactionDate,
            'pagamento'   => $paymentDate,
            'recebimento' => $paymentDate,
            'pago'        => !empty($input['paid']) ? 1 : 0,
            'fornecedor'  => $input['provider'] ?? null,
            'formaPgto'   => $input['paymentMethodId'] ?? null,
            'tipo'        => $input['type'] ?? null,
            'observacoes' => $input['notes'] ?? null,
            'oculto'      => !empty($input['hidden']) ? 1 : null,
        );
    }

    public function applyIdsToPost($id = null)
    {
        if (!$this->expectsJson()) {
            return;
        }

        $input = $this->jsonInput();
        $_POST['id'] = $input['ids'] ?? $input['id'] ?? $id;
    }

    public function normalizeCollectionQuery()
    {
        if (!$this->expectsJson()) {
            return;
        }

        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
        $perPage = filter_var($_GET['perPage'] ?? 30, FILTER_VALIDATE_INT);
        $page = $page && $page > 0 ? (int) $page : 1;
        $perPage = $perPage && $perPage > 0 && $perPage <= 100 ? (int) $perPage : 30;

        $_GET['per_page'] = ($page - 1) * $perPage;
        $_GET['api_per_page'] = $perPage;
    }

    private function toLegacyDate($date)
    {
        if (!$date || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string) $date, $matches)) {
            return $date;
        }

        return sprintf('%s/%s/%s', $matches[3], $matches[2], $matches[1]);
    }
}
