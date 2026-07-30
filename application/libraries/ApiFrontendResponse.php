<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ApiFrontendResponse
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = get_instance();
    }

    public function success($data = array(), $meta = null, $status = 200)
    {
        return $this->send(self::successPayload($data, $meta), $status);
    }

    public function collection($results, $meta = array())
    {
        return $this->success(
            array('results' => array_values((array) $results)),
            $meta
        );
    }

    public function created($data)
    {
        return $this->success($data, null, 201);
    }

    public function noContent()
    {
        return $this->send(null, 204);
    }

    public function error($code, $message, $status = 400, $details = null)
    {
        return $this->send(
            self::errorPayload($code, $message, $details),
            $status
        );
    }

    public static function successPayload($data = array(), $meta = null)
    {
        $payload = array(
            'success' => true,
            'data'    => $data,
        );

        if ($meta !== null) {
            $payload['meta'] = $meta;
        }

        return $payload;
    }

    public static function errorPayload($code, $message, $details = null)
    {
        $payload = array(
            'success' => false,
            'error'   => array(
                'code'    => (string) $code,
                'message' => (string) $message,
            ),
        );

        if ($details !== null) {
            $payload['error']['details'] = $details;
        }

        return $payload;
    }

    private function send($payload, $status)
    {
        $this->CI->output
            ->set_status_header((int) $status)
            ->set_content_type('application/json', 'utf-8');

        if ((int) $status === 204) {
            return $this->CI->output->set_output('');
        }

        $json = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($json === false) {
            $this->CI->output->set_status_header(500);
            $json = '{"success":false,"error":{"code":"JSON_ENCODING_ERROR","message":"Não foi possível gerar a resposta da API."}}';
        }

        return $this->CI->output->set_output($json);
    }
}
