<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ApiFrontendAvailability
{
    public function check()
    {
        $uri = uri_string();

        if (strpos($uri, 'api/frontend/v1/auth/') !== 0) {
            return;
        }

        $CI = get_instance();
        $CI->load->model('configs_model');

        if (!$CI->configs_model->isApiDisabled()) {
            return;
        }

        $CI->output
            ->set_status_header(503)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(
                json_encode(
                    array(
                        'code'    => 'API_UNAVAILABLE',
                        'message' => 'API indisponível no momento. Tente novamente mais tarde.',
                    ),
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                )
            )
            ->_display();
        exit;
    }

}
