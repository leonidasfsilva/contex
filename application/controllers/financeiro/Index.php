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

        $this->load->model('Poupanca_model', '', true);
        $this->load->model('pendencia_model', '', true);
        $this->load->model('fatura_model', '', true);
        $this->load->model('clientes_model', '', true);
        $this->data['menuFinanceiro'] = 'Poupanca';
        $this->load->helper(array('codegen_helper'));
        $this->id_usuario = $this->session->userdata('id');

    }

    public function index()
    {
        $this->data['view'] = 'financeiro/painel';
        $this->load->view('tema/topo', $this->data);
    }

}
