<?php

class Permissoes extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

//        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
//            $this->session->set_flashdata('error', 'Você não tem permissão para configurar as permissões no sistema.');
//            redirect(base_url());
//        }

        $this->load->helper(array('form', 'codegen_helper'));
        $this->data['menuConfiguracoes'] = 'Permissões';
    }

    function index()
    {
        $this->gerenciar();
    }

    function gerenciar()
    {

        $this->load->library('pagination');


        $config['base_url'] = base_url() . 'permissoes/gerenciar/';
        $config['total_rows'] = $this->permissoes_model->count('permissoes');
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

        $this->data['results'] = $this->permissoes_model->get('permissoes', '*', '', $config['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'permissoes/permissoes';
        $this->load->view('tema/topo', $this->data);


    }

    function adicionar()
    {
        if ($_POST) {

            $data = array(
                'nome' => padronizarString($_POST['nome']),
                'ativo' => $_POST['status']
            );
            $this->permissoes_model->add('permissoes', $data);
            $last_id = $this->db->insert_id('permissoes');


            if ($_POST['atividades']) {
                foreach ($_POST['atividades'] as $atividade) {
                    $data = array(
                        'id_permissao' => $last_id,
                        'atividade' => $atividade
                    );
                    $this->permissoes_model->add('permissoes_assoc', $data);
                }
            }
            $this->session->set_flashdata('sucesso', 'Permissão cadastrada com sucesso!');
            redirect(base_url('permissoes'));
        }

        $this->data['view'] = 'permissoes/adicionarPermissao';
        $this->load->view('tema/topo', $this->data);
    }

    function editar($id = null)
    {
        if ($_POST) {
            $this->permissoes_model->delete_real('permissoes_assoc', 'id_permissao', $id);

            if ($_POST['nome']) {
                $data = array(
                    'nome' => padronizarString($_POST['nome'])
                );
                $this->permissoes_model->edit('permissoes', $data, 'id_permissao', $id);
            }

            if ($_POST['atividades']) {
                foreach ($_POST['atividades'] as $atividade) {
                    $data = array(
                        'id_permissao' => $id,
                        'atividade' => $atividade
                    );
                    $this->permissoes_model->add('permissoes_assoc', $data);
                }
            }
            $this->session->set_flashdata('sucesso', 'Permissão alterada com sucesso!');
            redirect(base_url('permissoes/editar/') . $id);
        }

        if (!$id) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect(base_url('permissoes'));
        }

        $this->data['permissao'] = $this->permissoes_model->getById($id);
        $atividades = $this->permissoes_model->getAtividades($id);

        if ($atividades != null) {
            foreach ($atividades as $ativs) {
                $this->data['atividades'][] = $ativs->atividade;
            }
        } else {
            $this->data['atividades'][] = null;
        }
        $this->data['view'] = 'permissoes/editarPermissao';
        $this->load->view('tema/topo', $this->data);
    }

    function desativar()
    {

        $id = $_POST['id'];
        if ($id == null) {
            $this->session->set_flashdata('erro', 'Erro ao tentar desativar permissão');
            redirect(base_url('permissoes'));
        }

        $data = array(
            'ativo' => 0
        );

        if ($this->permissoes_model->edit('permissoes', $data, 'id_permissao', $id)) {
            $this->session->set_flashdata('sucesso', 'Permissão desativada com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar desativar permissão');
        }
        redirect(base_url('permissoes'));
    }

    function ativar()
    {

        $id = $_POST['id'];
        if ($id == null) {
            $this->session->set_flashdata('erro', 'Erro ao tentar ativar permissão');
            redirect(base_url('permissoes'));
        }

        $data = array(
            'ativo' => 1
        );

        if ($this->permissoes_model->edit('permissoes', $data, 'id_permissao', $id)) {
            $this->session->set_flashdata('sucesso', 'Permissão ativada com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar desativar permissão');
        }
        redirect(base_url('permissoes'));
    }

    function excluir()
    {

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('erro', 'Erro ao tentar desativar permissão.');
            redirect(base_url('permissoes'));
        }
        $data = array(
            'status' => 0
        );
        if ($this->permissoes_model->edit('permissoes', $data, 'id_permissao', $id)) {
            $this->session->set_flashdata('sucesso', 'Permissão excluída com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir permissão!');
        }
        redirect(base_url('permissoes'));
    }

}


/* End of file permissoes.php */
/* Location: ./application/controllers/permissoes.php */
