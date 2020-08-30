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

        $this->load->library('pagination');
        $this->load->helper(array('form', 'codegen_helper'));
        $this->data['menuUsuarios'] = 'Usuários';
        $this->data['menuConfiguracoes'] = 'Configurações';

    }

    function index()
    {
        $this->gerenciar();
    }

    function gerenciar()
    {
        $config['base_url'] = base_url() . 'usuarios/gerenciar';
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
        if ($_POST) {

            $verificacao = $this->usuarios_model->verificaExisteEmail($this->input->post('email'));

            if ($verificacao == 1) {
                $this->session->set_flashdata('erro', 'O email informado já encontra-se em uso, informe um email diferente.');
                redirect(base_url() . 'usuarios/adicionar');
            }

            $data1 = array(
                'nome' => $this->input->post('nome'),
                'cpf' => $this->input->post('cpf'),
                'cep' => $this->input->post('cep'),
                'logradouro' => $this->input->post('logradouro'),
                'complemento' => $this->input->post('complemento'),
                'numero' => $this->input->post('numero'),
                's_n' => $this->input->post('s_n') == 1 ? 1 : 0,
                'bairro' => $this->input->post('bairro'),
                'cidade' => $this->input->post('cidade'),
                'uf' => $this->input->post('uf'),
                'email' => $this->input->post('email'),
                'senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                'telefone' => $this->input->post('telefone'),
                'ativo' => $this->input->post('situacao'),
                'permissoes_id' => $this->input->post('permissoes_id')
            );

            if ($this->usuarios_model->add('usuarios', $data1) == true) {
                $last_id = $this->db->insert_id('usuarios');

                $data2 = array(
                    'id_usuario' => $last_id,
                    'nome' => $this->input->post('nome'),
                );
                $this->configs_model->registraConfigsUsuario($data2);
                $this->session->set_flashdata('sucesso', 'Usuário cadastrado com sucesso!');
                redirect(base_url() . 'usuarios/');
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar cadastrar usuário.');
                redirect(base_url() . 'usuarios/');

            }
        }

        $this->load->model('permissoes_model');
        $this->data['permissoes'] = $this->permissoes_model->getActive('permissoes', 'permissoes.id_permissao,permissoes.nome');
        $this->data['view'] = 'usuarios/adicionarUsuario';
        $this->load->view('tema/topo', $this->data);

    }

    function editar($id = null)
    {

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para editar usuários');
            redirect(base_url());
        }

//        if ($this->input->post('id_usuarios') == 1 && $this->input->post('situacao') == 0) {
//            $this->session->set_flashdata('erro', 'O administrador do sistema não pode ser desativado.');
//            redirect(base_url() . 'usuarios/editar/' . $this->input->post('id_usuarios'));
//        }

        if ($_POST) {

            if (!$this->input->post('email')) {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar editar usuário: email não informado');
                redirect(base_url('usuarios/editar/') . $this->input->post('id_usuarios'));
            }

            $verificacao = $this->usuarios_model->verificaEmailUsuario($this->input->post('email'), $this->input->post('id_usuarios'));

            if ($verificacao == 0) {
                $verificacao = $this->usuarios_model->verificaExisteEmail($this->input->post('email'));

                if ($verificacao == 1) {
                    $this->session->set_flashdata('erro', 'Email informado está em uso, informe um email diferente.');
                    redirect(base_url() . 'usuarios/editar/' . $this->input->post('id_usuarios'));
                }
            }

            if (!$_POST['cpf']) {
                $cpf = null;
            } else {
                $cpf = $_POST['cpf'];
            }

            $dados_usuario = $this->mxcode_model->getUsuario($this->input->post('id_usuarios'));
            if (!$dados_usuario->cpf) {
                if ($this->mxcode_model->verificaCPF($_POST['cpf']) && $_POST['cpf']) {
                    $this->session->set_flashdata('erro', 'O CPF informado pertence a outro usuário');
                    redirect(base_url('usuarios/editar/') . $id);
                }
            } else {
                if ($dados_usuario->cpf != $_POST['cpf']) {
                    if ($this->mxcode_model->verificaCPF($_POST['cpf'])) {
                        $this->session->set_flashdata('erro', 'O CPF informado pertence a outro usuário');
                        redirect(base_url('usuarios/editar/') . $id);
                    }
                }
            }

            $senha = $this->input->post('senha');
            if ($senha) {

                $senha = password_hash($senha, PASSWORD_DEFAULT);
                $data = array(
                    'nome' => $this->input->post('nome'),
                    'cpf' => $cpf,
                    'cep' => $this->input->post('cep'),
                    'logradouro' => $this->input->post('logradouro'),
                    'complemento' => $this->input->post('complemento'),
                    'numero' => $this->input->post('numero'),
                    's_n' => $this->input->post('s_n') == 1 ? 1 : 0,
                    'bairro' => $this->input->post('bairro'),
                    'cidade' => $this->input->post('cidade'),
                    'uf' => $this->input->post('uf'),
                    'email' => $this->input->post('email'),
                    'senha' => $senha,
                    'telefone' => $this->input->post('telefone'),
                    'permissoes_id' => $this->input->post('permissoes_id')
                );
            } else {
                $data = array(
                    'nome' => $this->input->post('nome'),
                    'cpf' => $this->input->post('cpf'),
                    'cep' => $this->input->post('cep'),
                    'logradouro' => $this->input->post('logradouro'),
                    'complemento' => $this->input->post('complemento'),
                    'numero' => $this->input->post('numero'),
                    's_n' => $this->input->post('s_n') == 1 ? 1 : 0,
                    'bairro' => $this->input->post('bairro'),
                    'cidade' => $this->input->post('cidade'),
                    'uf' => $this->input->post('uf'),
                    'email' => $this->input->post('email'),
                    'telefone' => $this->input->post('telefone'),
                    'permissoes_id' => $this->input->post('permissoes_id')
                );
            }
            $this->usuarios_model->edit('usuarios', $data, 'id_usuarios', $this->input->post('id_usuarios'));
            $this->session->set_flashdata('sucesso', 'Cadastro de usuário alterado com sucesso!');
            redirect(base_url() . 'usuarios/editar/' . $this->input->post('id_usuarios'));
        }

        $this->data['result'] = $this->usuarios_model->getById($id);
        $this->data['permissoes'] = $this->permissoes_model->getActive('permissoes', 'permissoes.id_permissao,permissoes.nome');
        $this->data['view'] = 'usuarios/editarUsuario';
        $this->load->view('tema/topo', $this->data);

    }

    function desativar()
    {
        $id = $this->input->post('id');

        if ($id == 1) {
            $this->session->set_flashdata('erro', 'A conta de administrador do sistema não pode ser desativada.');
            redirect(base_url() . 'usuarios/gerenciar');
        }

        $data = array(
            'ativo' => 0
        );

        if ($this->usuarios_model->delete('usuarios', $data, 'id_usuarios', $id) == true) {
            $this->session->set_flashdata('sucesso', 'Conta de usuário desativada com sucesso!');
            redirect(base_url() . 'usuarios');

        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar desativar conta de usuário.');
            redirect(base_url() . 'usuarios');
        }
    }

    function excluir()
    {
        $id = $this->input->post('id');

        if ($id == 1) {
            $this->session->set_flashdata('erro', 'A conta de administrador do sistema não pode ser excluída.');
            redirect(base_url() . 'usuarios');
        }

        $data = array(
            'status' => 0
        );

        if ($this->usuarios_model->delete('usuarios', $data, 'id_usuarios', $id) == true) {
            $this->session->set_flashdata('sucesso', 'Conta de usuário excluída com sucesso!');
            redirect(base_url() . 'usuarios/gerenciar/');

        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir conta de usuário.');
            redirect(base_url() . 'usuarios/gerenciar/');
        }
    }

    function ativar()
    {
        $id = $this->input->post('id');

        $data = array(
            'ativo' => 1
        );

        if ($this->usuarios_model->delete('usuarios', $data, 'id_usuarios', $id) == true) {
            $this->session->set_flashdata('sucesso', 'Conta de usuário ativada com sucesso!');
            redirect(base_url() . 'usuarios');

        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar ativar conta de usuário.');
            redirect(base_url() . 'usuarios');
        }
    }

    function visualizar($id)
    {

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar usuários.');
            redirect(base_url());
        }

        $verificacao = $this->usuarios_model->verificaExisteUsuario($id);

        if ($verificacao == 0) {
            $this->session->set_flashdata('erro', 'Usuário solicitado não encontrado');
            redirect(base_url() . 'usuarios');
        }

        $this->load->library('pagination');

        $config['base_url'] = base_url() . 'usuarios/visualizar/' . $id;
        $config['suffix'] = '#logs';
        $config['first_url'] = $config['base_url'] . '#logs';
        $config['total_rows'] = $this->usuarios_model->countLogs($id);
        $config['per_page'] = 20;
        $config['page_query_string'] = true;
        $config['next_link'] = false;
        $config['prev_link'] = false;
        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="disabled"><a style="background-color:#337ab7; color: white" class="js:"><b>';
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

        $this->data['custom_error'] = '';
        $this->data['result'] = $this->usuarios_model->getById($id);
        $this->data['logs'] = $this->usuarios_model->getLogsUsuario(
            $id,
            $config['per_page'],
            $this->input->get('per_page'));
//        $this->data['total_credito'] = $this->clientes_model->getPendenciasCreditoCliente(id_usuario(), $id);
//        $this->data['total_debito'] = $this->clientes_model->getPendenciasDebitoCliente(id_usuario(), $id);
//        $this->data['pendencias'] = $this->clientes_model->getPendenciasByCliente($id);
        $this->data['view'] = 'usuarios/visualizar';
        $this->load->view('tema/topo', $this->data);
    }

}