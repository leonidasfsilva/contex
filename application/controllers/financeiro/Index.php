<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Index extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

    }

    public function index()
    {
        $data['menuFinanceiro'] = true;
        $data['view']           = 'financeiro/painel';
        $this->load->view('tema/topo', $data);
    }

}
