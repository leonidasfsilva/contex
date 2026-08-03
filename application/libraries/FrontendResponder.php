<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FrontendResponder
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->library('FrontendRequestContext');
        $this->CI->load->library('ApiFrontendResponse');
    }

    public function view($view, array $viewData, array $jsonData)
    {
        if ($this->CI->frontendrequestcontext->expectsJson()) {
            return $this->CI->apifrontendresponse->success(
                $jsonData['data'],
                $jsonData['meta'] ?? null
            );
        }

        $viewData['view'] = $view;

        return $this->CI->load->view('tema/topo', $viewData);
    }

    public function success($message, $redirectUrl, $data = array(), $status = 200)
    {
        if ($this->CI->frontendrequestcontext->expectsJson()) {
            return $this->CI->apifrontendresponse->success(
                array_merge(array('message' => $message), (array) $data),
                null,
                $status
            );
        }

        $this->CI->session->set_flashdata('sucesso', $message);

        return redirect($redirectUrl);
    }

    public function noContent($message, $redirectUrl)
    {
        if ($this->CI->frontendrequestcontext->expectsJson()) {
            return $this->CI->apifrontendresponse->noContent();
        }

        $this->CI->session->set_flashdata('sucesso', $message);

        return redirect($redirectUrl);
    }

    public function error($code, $message, $status, $redirectUrl, $details = null)
    {
        if ($this->CI->frontendrequestcontext->expectsJson()) {
            return $this->CI->apifrontendresponse->error($code, $message, $status, $details);
        }

        $this->CI->session->set_flashdata('erro', $message);

        return redirect($redirectUrl);
    }
}
