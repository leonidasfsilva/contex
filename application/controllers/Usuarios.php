<?php

class Usuarios extends CI_Controller
{

    function __construct()
    {

        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar os usuários.');
            redirect(base_url());
        }

        $this->load->helper(array('form', 'codegen_helper'));
        $this->load->model('usuarios_model', '', true);
        $this->data['menuUsuarios'] = 'Usuários';
        $this->data['menuConfiguracoes'] = 'Configurações';

    }

    function index()
    {
        $this->gerenciar();
    }

    function gerenciar()
    {

        $this->load->library('pagination');

        $config['base_url'] = base_url() . 'usuarios/gerenciar/';
        $config['total_rows'] = $this->usuarios_model->count('usuarios');
        $config['per_page'] = 10;
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

        $this->data['results'] = $this->usuarios_model->get($config['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'usuarios/usuarios';
        $this->load->view('tema/topo', $this->data);


    }

    function adicionar()
    {

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('usuarios') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);

        } else {

            $data = array(
                'nome' => set_value('nome'),
                'rg' => set_value('rg'),
                'cpf' => set_value('cpf'),
                'logradouro' => set_value('rua'),
                'numero' => set_value('numero'),
                'bairro' => set_value('bairro'),
                'cidade' => set_value('cidade'),
                'uf' => set_value('estado'),
                'email' => set_value('email'),
                'senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                'telefone_2' => set_value('telefone'),
                'telefone' => set_value('celular'),
                'status' => set_value('situacao'),
                'permissoes_id' => $this->input->post('permissoes_id'),
            );

            if ($this->usuarios_model->add('usuarios', $data) == true) {
                $this->session->set_flashdata('success', 'Usuário cadastrado com sucesso!');
                redirect(base_url() . 'index.php/usuarios/adicionar/');
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';

            }
        }

        $this->load->model('permissoes_model');
        $this->data['permissoes'] = $this->permissoes_model->getActive('permissoes', 'permissoes.idPermissao,permissoes.nome');
        $this->data['view'] = 'usuarios/adicionarUsuario';
        $this->load->view('tema/topo', $this->data);


    }

    function editar()
    {

        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('erro', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('usuarios');
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        $this->form_validation->set_rules('rg', 'RG', 'trim|required');
        $this->form_validation->set_rules('cpf', 'CPF', 'trim|required');
        $this->form_validation->set_rules('logradouro', 'Logradouro', 'trim|required');
        $this->form_validation->set_rules('numero', 'Número', 'trim|required');
        $this->form_validation->set_rules('bairro', 'Bairro', 'trim|required');
        $this->form_validation->set_rules('cidade', 'Cidade', 'trim|required');
        $this->form_validation->set_rules('uf', 'UF', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('telefone', 'Telefone', 'trim|required');
        $this->form_validation->set_rules('situacao', 'Situação', 'trim|required');
        $this->form_validation->set_rules('permissoes_id', 'Permissão', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);

        } else {

            if ($this->input->post('id_usuarios') == 1 && $this->input->post('situacao') == 0) {
                $this->session->set_flashdata('erro', 'O administrador padrão não pode ser desativado!');
                redirect(base_url() . 'usuarios/editar/' . $this->input->post('id_usuarios'));
            }

            $senha = $this->input->post('senha');
            if ($senha != null) {

                $senha = password_hash($senha, PASSWORD_DEFAULT);
                $data = array(
                    'nome' => $this->input->post('nome'),
                    'rg' => $this->input->post('rg'),
                    'cpf' => $this->input->post('cpf'),
                    'logradouro' => $this->input->post('logradouro'),
                    'numero' => $this->input->post('numero'),
                    'bairro' => $this->input->post('bairro'),
                    'cidade' => $this->input->post('cidade'),
                    'uf' => $this->input->post('uf'),
                    'email' => $this->input->post('email'),
                    'senha' => $senha,
                    'telefone_2' => $this->input->post('telefone'),
                    'telefone' => $this->input->post('celular'),
                    'status' => $this->input->post('situacao'),
                    'permissoes_id' => $this->input->post('permissoes_id')
                );
            } else {

                $data = array(
                    'nome' => $this->input->post('nome'),
                    'rg' => $this->input->post('rg'),
                    'cpf' => $this->input->post('cpf'),
                    'logradouro' => $this->input->post('logradouro'),
                    'numero' => $this->input->post('numero'),
                    'bairro' => $this->input->post('bairro'),
                    'cidade' => $this->input->post('cidade'),
                    'uf' => $this->input->post('uf'),
                    'email' => $this->input->post('email'),
                    'telefone_2' => $this->input->post('telefone'),
                    'telefone' => $this->input->post('celular'),
                    'status' => $this->input->post('situacao'),
                    'permissoes_id' => $this->input->post('permissoes_id')
                );

            }


            if ($this->usuarios_model->edit('usuarios', $data, 'id_usuarios', $this->input->post('id_usuarios')) == true) {
                $this->session->set_flashdata('sucesso', 'Usuário editado com sucesso!');
                redirect(base_url() . 'usuarios/editar/' . $this->input->post('id_usuarios'));
            } else {
                $this->session->set_flashdata('sucesso', 'Ocorreu um erro ao tentar editar usuário.');
                redirect('Mxcode');
            }
        }

        $this->data['result'] = $this->usuarios_model->getById($this->uri->segment(3));
        $this->load->model('permissoes_model');
        $this->data['permissoes'] = $this->permissoes_model->getActive('permissoes', 'permissoes.idPermissao,permissoes.nome');

        $this->data['view'] = 'usuarios/editarUsuario';
        $this->load->view('tema/topo', $this->data);


    }
    

    public function desativar()
    {
        $id = $this->input->post('id');

        if($id == 1) {
            $this->session->set_flashdata('erro', 'O administrador do sistema não pode ser desativado.');
            redirect(base_url() . 'usuarios/gerenciar/');
        }

        $data = array(
            'status' => 0
        );

        if($this->usuarios_model->delete('usuarios', $data, 'id_usuarios', $id) == true){
            $this->session->set_flashdata('sucesso', 'Usuário desativado com sucesso!');
            redirect(base_url() . 'usuarios/gerenciar/');

        } else{
            $this->session->set_flashdata('erro', 'Erro ao tentar desativar usuário.');
            redirect(base_url() . 'usuarios/gerenciar/');
        }
    }

    public function ativar()
    {
        $id = $this->input->post('id');

        $data = array(
            'status' => 1
        );

        if($this->usuarios_model->delete('usuarios', $data, 'id_usuarios', $id) == true){
            $this->session->set_flashdata('sucesso', 'Usuário ativado com sucesso!');
            redirect(base_url() . 'usuarios/gerenciar/');

        } else{
            $this->session->set_flashdata('erro', 'Erro ao tentar ativar usuário.');
            redirect(base_url() . 'usuarios/gerenciar/');
        }
    }
}
