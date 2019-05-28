<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mxcode extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mxcode_model', '', true);
        $this->load->helper('file');
        $this->load->library('upload');
    }

    public function index()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

//        $this->data['ordens'] = $this->mxcode_model->getOsAbertas();
//        $this->data['produtos'] = $this->mxcode_model->getProdutosMinimo();
//        $this->data['os'] = $this->mxcode_model->getOsEstatisticas();
//        $this->data['estatisticas_financeiro'] = $this->mxcode_model->getEstatisticasFinanceiro();
        $this->data['menuPainel'] = 'Index';
        $this->data['view'] = 'mxcode/painel';
        $this->load->view('tema/topo', $this->data);

    }

    public function minhaConta()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $data['dados'] = $this->mxcode_model->getUsuario(id_usuario());
        $data['usuario'] = $this->mxcode_model->getById(id_usuario());
        $data['view'] = 'mxcode/minhaConta';
        $data['minhaConta'] = 'Conta';
        $this->load->view('tema/topo', $data);

    }

    public function alterarSenha()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
        $oldSenha = $this->input->post('oldSenha');
        $senha = $this->input->post('novaSenha');

        $usuario = $this->mxcode_model->getUsuario($this->session->userdata('id'));


        if (password_verify($oldSenha, $usuario->senha)) {
            $result = $this->mxcode_model->alterarSenha($usuario->id_usuarios, $senha);
            if ($result == true) {
                $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso!');
                redirect(base_url() . 'mxcode/minhaConta');
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a senha!');
                redirect(base_url() . 'mxcode/minhaConta');
            }
        } else {
            $this->session->set_flashdata('erro', 'Senha atual incorreta!');
            redirect(base_url() . 'mxcode/minhaConta');

        }

    }

    public function pesquisar()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $termo = $this->input->get('termo');

        $data['results'] = $this->mxcode_model->pesquisar($termo);
        $this->data['produtos'] = $data['results']['produtos'];
        $this->data['servicos'] = $data['results']['servicos'];
        $this->data['os'] = $data['results']['os'];
        $this->data['clientes'] = $data['results']['clientes'];
        $this->data['view'] = 'mxcode/pesquisa';
        $this->load->view('tema/topo', $this->data);

    }

    public function login()
    {

        $this->load->view('mxcode/login');

    }

    public function sair()
    {
        gravaLog(id_usuario(), 'Logoff no sistema', getenv("REMOTE_ADDR"));
        $this->session->sess_destroy();
        redirect('mxcode/login');
    }

    public function verificarLogin()
    {

        header('Access-Control-Allow-Origin: ' . base_url());
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'E-mail', 'valid_email|required|trim');
        $this->form_validation->set_rules('senha', 'Senha', 'required|trim');

        if ($this->form_validation->run() == false) {
//            print_array(validation_errors());
            $msgValidation = validation_errors();
            $this->session->set_flashdata('erro', 'Formato de e-mail inválido.');
            redirect('mxcode/login');
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('senha');
            $usuario = $this->mxcode_model->check_credentials($email);

            if ($usuario) {
                if (password_verify($password, $usuario->senha)) {
                    $session_data = array(
                        'nome' => $usuario->nome,
                        'email' => $usuario->email,
                        'id' => $usuario->id_usuarios,
                        'permissao' => $usuario->permissoes_id,
                        'logado' => true
                    );
                    $this->session->set_userdata($session_data);
                    gravaLog($usuario->id_usuarios, 'Login no sistema', getenv("REMOTE_ADDR"));
                    redirect('/');
                } else {
                    $this->session->set_flashdata('erro', 'Dados de acesso inválidos, por favor tente novamente.');
                    redirect('mxcode/login');
                }
            } else {
                $this->session->set_flashdata('erro', 'Dados de acesso inválidos, por favor tente novamente.');
                redirect('mxcode/login');
            }
        }
        die();
    }

    public function backup()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para efetuar backup.');
            redirect(base_url());
        }

        $this->load->dbutil();
        $prefs = array(
            'format' => 'zip',
            'foreign_key_checks' => false,
            'filename' => 'backup' . date('d-m-Y') . '.sql',
        );

        $backup = $this->dbutil->backup($prefs);

        $this->load->helper('file');
        write_file(base_url() . 'backup/backup.zip', $backup);

        $this->load->helper('download');
        force_download('backup' . date('d-m-Y H:m:s') . '.zip', $backup);
    }

    public function emitente()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $data['menuConfiguracoes'] = 'Configuracoes';
        $data['dados'] = $this->mxcode_model->getEmitente(id_usuario());
        $data['view'] = 'mxcode/emitente';
        $this->load->view('tema/topo', $data);
    }

    public function cadastrarEmitente()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        if ($_FILES['userfile']['size'] > 0) {
            $dir = 'assets/uploads/logomarcas';
            $image = $this->do_upload($_FILES['userfile'], base_url() . 'mxcode/emitente', $dir);
        } else {
            $image = null;
        }

        $data = array(
            'logomarca' => $image,
            'emitente' => padronizarString($this->input->post('emitente')),
            'cnpj' => $this->input->post('cnpj'),
            'ie' => $this->input->post('ie'),
            's_n' => $this->input->post('s_n'),
            'logradouro' => padronizarString($this->input->post('logradouro')),
            'numero' => $this->input->post('numero'),
            'bairro' => padronizarString($this->input->post('bairro')),
            'cidade' => padronizarString($this->input->post('cidade')),
            'uf' => padronizarString($this->input->post('uf')),
            'cep' => $this->input->post('cep'),
            'telefone' => $this->input->post('telefone'),
            'email' => $this->input->post('email'),
            'id_usuario' => id_usuario(),
        );

        $retorno = $this->mxcode_model->add('emitente', $data);
        if ($retorno == true) {
            $this->session->set_flashdata('sucesso', 'Informações cadastradas com sucesso!');
            redirect(base_url() . 'mxcode/emitente');
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar cadastrar as informações.');
            redirect(base_url() . 'mxcode/emitente');
        }


    }

    public function editarEmitente()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }


        $data = array(
            'emitente' => padronizarString($this->input->post('emitente')),
            'cnpj' => $this->input->post('cnpj'),
            'ie' => $this->input->post('ie'),
            's_n' => $this->input->post('s_n'),
            'logradouro' => padronizarString($this->input->post('logradouro')),
            'numero' => $this->input->post('numero'),
            'bairro' => padronizarString($this->input->post('bairro')),
            'cidade' => padronizarString($this->input->post('cidade')),
            'uf' => padronizarString($this->input->post('uf')),
            'cep' => $this->input->post('cep'),
            'telefone' => $this->input->post('telefone'),
            'email' => $this->input->post('email'),
        );

        $retorno = $this->mxcode_model->edit('emitente', $data, 'id_emitente', $this->input->post('id_emitente'));
        if ($retorno == true) {
            $this->session->set_flashdata('sucesso', 'Informações alteradas com sucesso.');
            redirect(base_url() . 'mxcode/emitente');
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar as informações.');
            redirect(base_url() . 'mxcode/emitente');
        }


    }

    function do_upload($file, $url = null, $dir)
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $image_upload_folder = $dir;

        if (!file_exists($image_upload_folder)) {
            mkdir($image_upload_folder, DIR_WRITE_MODE, true);
        }

        $upload_config = array(
            'upload_path' => $image_upload_folder,
            'allowed_types' => 'png|jpg|jpeg|bmp|gif',
            'max_size' => 2048,
            'remove_space' => true,
            'encrypt_name' => true,
        );

        $this->upload->initialize($upload_config);

        $url != null ?: $url = base_url();

        if (!$this->upload->do_upload()) {
            $upload_error = $this->upload->display_errors();
            $this->session->set_flashdata('error', $upload_error);
            redirect($url);
//            print_r($upload_error);
            exit();
        } else {
            $file_info = array($this->upload->data());
            return $file_info[0]['file_name'];
        }

    }

    public function editarLogo()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $id = $this->input->post('id_emitente');
        if ($id == null || !is_numeric($id)) {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a logomarca.');
            redirect(base_url() . 'mxcode/emitente');
        }

        $logo_atual = $this->mxcode_model->getLogoEmitente(id_usuario());

        if ($logo_atual->logomarca) {
            unlink('assets/uploads/logomarcas/' . $logo_atual->logomarca);
        }
        $dir = 'assets/uploads/logomarcas';
        $image = $this->do_upload($_FILES['userfile'], base_url() . 'mxcode/emitente', $dir);
        $logo = $image;

        $retorno = $this->mxcode_model->editLogo($id, $logo);
        if ($retorno) {
            $this->session->set_flashdata('sucesso', 'Logomarca alterada com sucesso!');
            redirect(base_url() . 'mxcode/emitente');
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a logomarca.');
            redirect(base_url() . 'mxcode/emitente');
        }

    }

    public function excluirLogo()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $id = $this->input->post('id_emitente');
        if ($id == null || !is_numeric($id)) {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar excluir a logomarca.');
            redirect(base_url() . 'mxcode/emitente');
        }

        $logo_atual = $this->mxcode_model->getLogoEmitente(id_usuario());
        if ($logo_atual) {
            unlink('assets/uploads/logomarcas/' . $logo_atual->logomarca);
        }
        $nova_logo = null;
        $retorno = $this->mxcode_model->editLogo($id, $nova_logo);

        if ($retorno) {
            $this->session->set_flashdata('sucesso', 'Logomarca excluída com sucesso!');
            redirect(base_url() . 'mxcode/emitente');
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar excluir a logomarca.');
            redirect(base_url() . 'mxcode/emitente');
        }

    }

    public function editarFotoUsuario()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar alterar a logomarca.');
            redirect(base_url() . 'mxcode/emitente');
        }
        delete_files(FCPATH . 'assets/uploads/');

        $image = $this->do_upload($_FILES['userfile']);
        $logo = 'assets/uploads/' . $image;

        $retorno = $this->mxcode_model->editLogo($id, $logo);
        if ($retorno) {
            $this->session->set_flashdata('success', 'As informações foram alteradas com sucesso.');
            redirect(base_url() . 'mxcode/emitente');
        } else {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar alterar as informações.');
            redirect(base_url() . 'mxcode/emitente');
        }

    }

    public function atualizarPerfil()
    {

        $data = array(
            'nome' => $this->input->post('nome'),
            'rg' => $this->input->post('rg'),
            'cpf' => $this->input->post('cpf'),
            'logradouro' => $this->input->post('logradouro'),
            'numero' => $this->input->post('numero'),
            'bairro' => $this->input->post('bairro'),
            'cidade' => $this->input->post('cidade'),
            'uf' => $this->input->post('uf'),
//            'email' => $this->input->post('email'),
            'telefone' => $this->input->post('telefone'),
        );

        if ($this->mxcode_model->edit('usuarios', $data, 'id_usuarios', $this->input->post('id_usuarios')) == true) {
            $this->session->set_flashdata('sucesso', 'Perfil de usuário atualizado com sucesso!');
            redirect(base_url() . 'mxcode/minhaConta/');
        } else {
            $this->session->set_flashdata('sucesso', 'Ocorreu um erro ao atualizar perfil de usuário.');
            redirect(base_url() . 'mxcode/minhaConta/');
        }

    }

}
