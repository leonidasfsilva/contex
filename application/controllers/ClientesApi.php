<?php

class ClientesApi extends CI_Controller
{
    private $availableScopes = [
        'mikrotik' => 'Mikrotik',
        'cron' => 'Cron',
        'financeiro' => 'Financeiro',
        '*' => 'Todos',
    ];

    public function __construct()
    {
        parent::__construct();

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('error', 'Voce nao tem permissao para configurar clientes da API.');
            redirect(base_url());
        }

        $this->load->model('ClientesApi_model', 'clientesapi_model');
        $this->load->helper(array('form', 'codegen_helper'));

        $this->data['menuConfiguracoes'] = 'Clientes API';
        $this->data['availableScopes'] = $this->availableScopes;
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->data['results'] = $this->clientesapi_model->get();
        $this->data['view'] = 'clientes_api/clientes_api';
        $this->load->view('tema/topo', $this->data);
    }

    public function adicionar()
    {
        if ($_POST) {
            $username = trim($this->input->post('username'));

            if (!$username) {
                $this->session->set_flashdata('error', 'Informe o username do cliente API.');
                redirect(base_url('clientesapi'));
            }

            if ($this->clientesapi_model->usernameExists($username)) {
                $this->session->set_flashdata('error', 'Username ja cadastrado para cliente API.');
                redirect(base_url('clientesapi'));
            }

            $token = $this->generateToken();
            $data = [
                'username' => $username,
                'description' => trim($this->input->post('description')),
                'token' => $token,
                'scopes' => $this->formatScopes($this->input->post('scopes')),
                'active' => $this->input->post('active') ? 1 : 0,
                'status' => 1,
            ];

            if ($this->clientesapi_model->add($data)) {
                $this->session->set_flashdata('sucesso', 'Cliente API cadastrado com sucesso.');
                $this->session->set_flashdata('api_token_message', 'Cliente API cadastrado com sucesso. Token gerado:');
                $this->session->set_flashdata('api_token', $token);
                redirect(base_url('clientesapi'));
            }

            $this->session->set_flashdata('error', 'Erro ao cadastrar cliente API.');
            redirect(base_url('clientesapi'));
        }

        redirect(base_url('clientesapi'));
    }

    public function editar($id = null)
    {
        if ($_POST && !$id) {
            $id = $this->input->post('id');
        }

        $cliente = $this->clientesapi_model->getById($id);

        if (!$cliente) {
            $this->session->set_flashdata('error', 'Cliente API nao encontrado.');
            redirect(base_url('clientesapi'));
        }

        if ($_POST) {
            $username = trim($this->input->post('username'));

            if (!$username) {
                $this->session->set_flashdata('error', 'Informe o username do cliente API.');
                redirect(base_url('clientesapi'));
            }

            if ($this->clientesapi_model->usernameExists($username, $id)) {
                $this->session->set_flashdata('error', 'Username ja cadastrado para outro cliente API.');
                redirect(base_url('clientesapi'));
            }

            $data = [
                'username' => $username,
                'description' => trim($this->input->post('description')),
                'scopes' => $this->formatScopes($this->input->post('scopes')),
                'active' => $this->input->post('active') ? 1 : 0,
            ];

            if ($this->clientesapi_model->edit($id, $data)) {
                $this->session->set_flashdata('sucesso', 'Cliente API alterado com sucesso.');
                redirect(base_url('clientesapi'));
            }

            $this->session->set_flashdata('error', 'Erro ao alterar cliente API.');
            redirect(base_url('clientesapi'));
        }

        redirect(base_url('clientesapi'));
    }

    public function regenerar($id = null)
    {
        if (!$id) {
            $id = $this->input->post('id');
        }

        $cliente = $this->clientesapi_model->getById($id);

        if (!$cliente) {
            $this->session->set_flashdata('error', 'Cliente API nao encontrado.');
            redirect(base_url('clientesapi'));
        }

        $token = $this->generateToken();

        if ($this->clientesapi_model->edit($id, ['token' => $token])) {
            $this->session->set_flashdata('sucesso', 'Token regenerado com sucesso.');
            $this->session->set_flashdata('api_token_message', 'Token regenerado com sucesso para ' . $cliente->username . ':');
            $this->session->set_flashdata('api_token', $token);
        } else {
            $this->session->set_flashdata('error', 'Erro ao regenerar token.');
        }

        redirect(base_url('clientesapi'));
    }

    public function ativar()
    {
        $this->alterarStatusAtivo(1, 'Cliente API ativado com sucesso.', 'Erro ao ativar cliente API.');
    }

    public function desativar()
    {
        $this->alterarStatusAtivo(0, 'Cliente API desativado com sucesso.', 'Erro ao desativar cliente API.');
    }

    public function excluir()
    {
        $id = $this->input->post('id');

        if (!$id || !$this->clientesapi_model->getById($id)) {
            $this->session->set_flashdata('error', 'Cliente API nao encontrado.');
            redirect(base_url('clientesapi'));
        }

        if ($this->clientesapi_model->edit($id, ['status' => 0])) {
            $this->session->set_flashdata('sucesso', 'Cliente API excluido com sucesso.');
            redirect(base_url('clientesapi'));
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir cliente API.');
        }

        redirect(base_url('clientesapi'));
    }

    private function alterarStatusAtivo($active, $successMessage, $errorMessage)
    {
        $id = $this->input->post('id');

        if (!$id || !$this->clientesapi_model->getById($id)) {
            $this->session->set_flashdata('error', 'Cliente API nao encontrado.');
            redirect(base_url('clientesapi'));
        }

        if ($this->clientesapi_model->edit($id, ['active' => $active])) {
            $this->session->set_flashdata('sucesso', $successMessage);
            redirect(base_url('clientesapi'));
        } else {
            $this->session->set_flashdata('error', $errorMessage);
        }

        redirect(base_url('clientesapi'));
    }

    private function formatScopes($scopes)
    {
        if (!$scopes || !is_array($scopes)) {
            return null;
        }

        $validScopes = array_keys($this->availableScopes);
        $scopes = array_intersect($validScopes, $scopes);

        return $scopes ? implode(',', $scopes) : null;
    }

    private function parseScopes($scopes)
    {
        if (!$scopes) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $scopes)));
    }

    private function generateToken()
    {
        return bin2hex(random_bytes(48));
    }
}
