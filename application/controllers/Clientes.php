<?php

class Clientes extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
        $this->load->model('clientes_model', '', true);
        $this->load->model('pendencia_model', '', true);
        $this->data['menuClientes'] = 'clientes';
    }

    function index()
    {
        $this->gerenciar();
    }

    function gerenciar()
    {

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar clientes.');
            redirect(base_url());
        }
        $this->load->library('table');
        $this->load->library('pagination');
        $id_usuario = $this->session->userdata('id');


        $config['base_url'] = base_url() . 'clientes/gerenciar/';
        $config['total_rows'] = $this->clientes_model->count('clientes');
        $config['per_page'] = null;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $this->data['results'] = $this->clientes_model->get('clientes', '*', '', $id_usuario, $config['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'clientes/clientes';
        $this->load->view('tema/topo', $this->data);


    }

    function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar clientes.');
            redirect(base_url());
        }
        $id_usuario = $this->session->userdata('id');
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->input->post('nome')) {
            $nome = padronizarString($this->input->post('nome'));

            $data = array(
                'nome' => $nome,
                'cpf' => $this->input->post('cpf'),
                'telefone' => $this->input->post('telefone'),
                'email' => $this->input->post('email'),
                'logradouro' => $this->input->post('logradouro'),
                'complemento' => $this->input->post('complemento'),
                'numero' => $this->input->post('numero'),
                'bairro' => $this->input->post('bairro'),
                'cidade' => $this->input->post('cidade'),
                'uf' => $this->input->post('uf'),
                'cep' => $this->input->post('cep'),
                'id_usuario' => $id_usuario,
            );

            if ($this->clientes_model->add('clientes', $data) == true) {
                $this->session->set_flashdata('sucesso', 'Cliente adicionado com sucesso!');
                redirect(base_url() . 'clientes/');
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar adicionar cliente');
                redirect(base_url() . 'clientes/');
            }
        }

        $this->data['view'] = 'clientes/adicionarCliente';
        $this->load->view('tema/topo', $this->data);

    }

    function editar($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('clientes');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar clientes.');
            redirect(base_url());
        }

        $verificacao = $this->clientes_model->verificaClienteUsuario($id, getUserId());

        if ($verificacao == 0) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado para este usuário');
            redirect(base_url() . 'clientes/');
        }

        $nome = padronizarString($this->input->post('nome'));
        if ($this->input->post('nome')) {
            $data = array(
                'nome' => $nome,
                'cpf' => $this->input->post('cpf'),
                'telefone' => $this->input->post('telefone'),
                'email' => $this->input->post('email'),
                'logradouro' => $this->input->post('logradouro'),
                'complemento' => $this->input->post('complemento'),
                'numero' => $this->input->post('numero'),
                'bairro' => $this->input->post('bairro'),
                'cidade' => $this->input->post('cidade'),
                'uf' => $this->input->post('uf'),
                'cep' => $this->input->post('cep')
            );

            if ($this->clientes_model->edit('clientes', $data, 'id_clientes', $this->input->post('id_clientes')) == true) {
                $this->session->set_flashdata('sucesso', 'Cliente editado com sucesso!');
                redirect(base_url() . 'clientes/editar/' . $this->input->post('id_clientes'));
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar editar dados do cliente');
                redirect(base_url() . 'clientes/');
            }

        }

        $this->data['result'] = $this->clientes_model->getById($id);
        $this->data['view'] = 'clientes/editarCliente';
        $this->load->view('tema/topo', $this->data);

    }

    public function visualizar($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('clientes');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar clientes.');
            redirect(base_url());
        }

        $verificacao = $this->clientes_model->verificaClienteUsuario($id, getUserId());

        if ($verificacao == 0) {
            $this->session->set_flashdata('erro', 'Cliente não encontrado para este usuário');
            redirect(base_url() . 'clientes/');
        }

        $this->data['custom_error'] = '';
        $this->data['result'] = $this->clientes_model->getById($id);
        $this->data['os'] = $this->clientes_model->getOsByCliente($id);
        $this->data['total_credito'] = $this->clientes_model->getPendenciasCreditoCliente(getUserId(), $id);
        $this->data['total_debito'] = $this->clientes_model->getPendenciasDebitoCliente(getUserId(), $id);
        $this->data['pendencias'] = $this->clientes_model->getPendenciasByCliente($id);
        $this->data['view'] = 'clientes/visualizar';
        $this->load->view('tema/topo', $this->data);

    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir clientes.');
            redirect(base_url());
        }

        $data = array(
            'status' => 0
        );

        if (!$this->input->post('id')) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect(base_url() . 'clientes/gerenciar/');
        }
        $id = $this->input->post('id');


//        // excluindo OSs vinculadas ao cliente
//        $this->db->where('clientes_id', $id);
//        $os = $this->db->get('os')->result();
//
//        if ($os != null) {
//
//            foreach ($os as $o) {
//                $this->db->where('os_id', $o->idOs);
//                $this->db->delete('servicos_os');
//
//                $this->db->where('os_id', $o->idOs);
//                $this->db->delete('produtos_os');
//
//
//                $this->db->where('idOs', $o->idOs);
//                $this->db->delete('os');
//            }
//        }
//
//        // excluindo Vendas vinculadas ao cliente
//        $this->db->where('clientes_id', $id);
//        $vendas = $this->db->get('vendas')->result();
//
//        if ($vendas != null) {
//
//            foreach ($vendas as $v) {
//                $this->db->where('vendas_id', $v->idVendas);
//                $this->db->delete('itens_de_vendas');
//
//
//                $this->db->where('idVendas', $v->idVendas);
//                $this->db->delete('vendas');
//            }
//        }
//
//        //excluindo receitas vinculadas ao cliente
//        $this->db->where('clientes_id', $id);
//        $this->db->delete('lancamentos');

        if ($this->clientes_model->delete('clientes', $data, 'id_clientes', $id) == true) {
            $this->session->set_flashdata('sucesso', 'Cliente excluído com sucesso!');
            redirect(base_url() . 'clientes/gerenciar/');

        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir cliente.');
            redirect(base_url() . 'clientes/gerenciar/');
        }

    }
}
