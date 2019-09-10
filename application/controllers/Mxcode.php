<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mxcode extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mxcode_model', '', true);
        $this->load->model('financeiro_model', '', true);
        $this->load->model('poupanca_model', '', true);
        $this->load->model('fatura_model', '', true);
        $this->load->helper('file');
        $this->load->library('upload');
        $this->load->library('image_lib');
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
        $data['menuPainel'] = 'Index';
        $data['usuario'] = $this->mxcode_model->getById(id_usuario());
        $data['contaCorrente'] = $this->financeiro_model->getTotal(id_usuario());
        $data['contaPoupanca'] = $this->poupanca_model->getTotal(id_usuario());
        $data['fatura'] = $this->fatura_model->getValorTotalFaturaAtual(id_usuario());
        $data['view'] = 'mxcode/painel';

        $this->load->view('tema/topo', $data);

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
        if ((session_id()) && ($this->session->userdata('logado'))) {
            gravaLog(id_usuario(), 'Logoff no sistema', getenv("REMOTE_ADDR"));
            $this->session->sess_destroy();
            redirect('mxcode/login');
        } else {
            $this->session->sess_destroy();
            redirect('mxcode/login');
        }
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
                    if ($usuario->status == 0) {
                        $this->session->set_flashdata('erro', 'Conta desativada.<br>Por favor, contate o administrador do sitema.');
                        redirect('mxcode/login');
                    }

                    $session_data = array(
                        'nome' => $usuario->nome,
                        'avatar' => $usuario->avatar,
                        'email' => $usuario->email,
                        'id' => $usuario->id_usuarios,
                        'permissao' => $usuario->permissoes_id,
                        'logado' => true
                    );
                    $this->session->set_userdata($session_data);
                    gravaLog($usuario->id_usuarios, 'Login no sistema', getenv("REMOTE_ADDR"));
                    redirect('/');
                } else {
                    $this->session->set_flashdata('erro', '1Dados de acesso inválidos, por favor tente novamente.');
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

//        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
//            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
//            redirect(base_url());
//        }

        $image_upload_folder = $dir;

        if (!file_exists($image_upload_folder)) {
            mkdir($image_upload_folder, DIR_WRITE_MODE, true);
        }

        $upload_config = array(
            'upload_path' => $image_upload_folder,
            'allowed_types' => 'png|jpg|jpeg|bmp|gif',
            'max_size' => 5000,
            'max_widht' => 5000,
            'max_height' => 5000,
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

        if ($_FILES['userfile']['size'] > 0) {

            $dir = 'assets/uploads/avatars';
            $avatar_atual = $this->mxcode_model->getAvatarUsuario(id_usuario());

            if ($avatar_atual->avatar) {
                unlink($dir . '/' . $avatar_atual->avatar);
            }

            if ($image = $this->do_upload($_FILES['userfile'], base_url() . 'mxcode/minhaConta', $dir)) {
                $data = array('upload_data' => $this->upload->data());

                $config['image_library'] = 'gd2';
                $config['source_image'] = ($dir) . '/' . $data['upload_data']['file_name'];
                $config['create_thumb'] = false;
                $config['maintain_ratio'] = true;
                $config['width'] = 200;
                $config['height'] = 200;
                $config['new_image'] = ($dir) . '/' . $data['upload_data']['file_name'];
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    $this->session->set_flashdata('erro', $this->image_lib->display_errors());
                }
            }

            $retorno = $this->mxcode_model->editAvatarUsuario(id_usuario(), $image);
            if ($retorno) {
                $usuario = $this->mxcode_model->getUsuario(id_usuario());
                $session_data = array(
                    'nome' => $usuario->nome,
                    'avatar' => $usuario->avatar,
                    'email' => $usuario->email,
                    'id' => $usuario->id_usuarios,
                    'permissao' => $usuario->permissoes_id,
                    'logado' => true
                );
                $this->session->set_userdata($session_data);

                $this->session->set_flashdata('sucesso', 'Foto de perfil alterada com sucesso!');
//                redirect(base_url() . 'mxcode/minhaConta');
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a foto de perfil.');
//                redirect(base_url() . 'mxcode/minhaConta');
            }

        } else {
            $this->session->set_flashdata('erro', 'Nenhum arquivo enviado.');
            redirect(base_url() . 'mxcode/minhaConta');
        }

    }

    public function excluirFotoUsuario()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $dir = 'assets/uploads/avatars';
        $avatar_atual = $this->mxcode_model->getAvatarUsuario(id_usuario());

        if ($avatar_atual->avatar) {
            unlink($dir . '/' . $avatar_atual->avatar);
        }

        $retorno = $this->mxcode_model->excluirAvatarUsuario(id_usuario());
        if ($retorno) {
            $usuario = $this->mxcode_model->getUsuario(id_usuario());
            $session_data = array(
                'nome' => $usuario->nome,
                'avatar' => $usuario->avatar,
                'email' => $usuario->email,
                'id' => $usuario->id_usuarios,
                'permissao' => $usuario->permissoes_id,
                'logado' => true
            );
            $this->session->set_userdata($session_data);

            $this->session->set_flashdata('sucesso', 'Foto de perfil removida com sucesso!');
            redirect(base_url() . 'mxcode/minhaConta');
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar excluir a foto de perfil.');
            redirect(base_url() . 'mxcode/minhaConta');
        }

    }

    public function atualizarPerfil()
    {
        $data = array(
            'nome' => $this->input->post('nome'),
            'rg' => $this->input->post('rg'),
            'cpf' => $this->input->post('cpf'),
            'cep' => $this->input->post('cep'),
            'logradouro' => $this->input->post('logradouro'),
            'numero' => $this->input->post('numero'),
            's_n' => $this->input->post('s_n') == 1 ? 1 : 0,
            'bairro' => $this->input->post('bairro'),
            'cidade' => $this->input->post('cidade'),
            'uf' => $this->input->post('uf'),
//            'email' => $this->input->post('email'),
            'telefone' => $this->input->post('telefone'),
        );

        if ($this->mxcode_model->edit('usuarios', $data, 'id_usuarios', $this->input->post('id_usuarios')) == true) {
            $this->session->set_flashdata('sucesso', 'Conta de usuário atualizada com sucesso!');
            redirect(base_url() . 'mxcode/minhaConta/');
        } else {
            $this->session->set_flashdata('sucesso', 'Ocorreu um erro ao atualizar conta de usuário.');
            redirect(base_url() . 'mxcode/minhaConta/');
        }

    }

}
