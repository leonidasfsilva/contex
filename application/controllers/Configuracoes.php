<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Configuracoes extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('upload');
        $this->load->library('image_lib');
    }

    public function index()
    {
        $this->sistema();
    }

    public function sistema()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para gerenciar configurações do sistema.');
            redirect(base_url());
        }

        $data['setores'] = array(
            'ARQUIVOS',
            'ANÚNCIOS',
            'CARTÕES',
            'CONFIGURAÇÕES',
            'CLIENTES',
            'EMITENTE',
            'FATURAS',
            'INVESTIMENTOS',
            'LANÇAMENTOS',
            'ORDENS DE SERVIÇO',
            'PAINEL INICIAL',
            'PENDÊNCIAS',
            'PERMISSÕES',
            'PRODUTOS',
            'RELATÓRIOS',
            'SERVIÇOS',
            'USUÁRIOS',
            'VENDAS',
        );

        $data['menuConfiguracoes'] = true;
        $data['results'] = $this->configs_model->getConfigs();
        $data['view'] = 'configuracoes/sistema';
        $this->load->view('tema/topo', $data);
    }

    public function usuario()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para gerenciar configurações de usuário.');
            redirect(base_url());
        }

        $data['menuConfiguracoes'] = true;
        $data['dados'] = $this->configs_model->getConfigsUsuario(getUserId());
        $data['view'] = 'configuracoes/usuario';
        $this->load->view('tema/topo', $data);
    }

    public function logs()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para visualizar logs do sistema');
            redirect(base_url());
        }
        $this->load->library('pagination');

        $config['base_url']             = base_url('configuracoes/logs/');
        $config['suffix']               = '#logs';
        $config['first_url']            = $config['base_url'] . '#logs';
        $config['total_rows']           = $this->usuarios_model->countLogs();
        $config['per_page']             = 40;
        $config['page_query_string']    = true;
        $config['next_link']            = false;
        $config['prev_link']            = false;
        $config['full_tag_open']        = '<ul class="pagination pagination-sm">';
        $config['full_tag_close']       = '</ul>';
        $config['num_tag_open']         = '<li>';
        $config['num_tag_close']        = '</li>';
        $config['cur_tag_open']         = '<li class="disabled"><a style="background-color:#337ab7; color: white" class="js:"><b>';
        $config['cur_tag_close']        = '</b></a></li>';
        $config['prev_tag_open']        = '<li>';
        $config['prev_tag_close']       = '</li>';
        $config['next_tag_open']        = '<li>';
        $config['next_tag_close']       = '</li>';
        $config['first_link']           = 'Primeira';
        $config['last_link']            = 'Última';
        $config['first_tag_open']       = '<li>';
        $config['first_tag_close']      = '</li>';
        $config['last_tag_open']        = '<li>';
        $config['last_tag_close']       = '</li>';

        $this->pagination->initialize($config);

        $data['logs'] = $this->configs_model->getLogsSistema(
            $config['per_page'],
            $this->input->get('per_page')
        );
        $data['view'] = 'configuracoes/logs';
        $this->load->view('tema/topo', $data);
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para editar configurações do sistema.');
            redirect(base_url());
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect(base_url('configuracoes/sistema'));
        }

        $descricao = padronizarString($_POST['descricao']);

        $data = array(
            'descricao' => $descricao,
            'setor' => $_POST['setor'],
        );

        if ($this->configs_model->add('configs_opcoes', $data)) {
            $this->session->set_flashdata('sucesso', 'Opção cadastrada com sucesso!');
            redirect(base_url() . 'configuracoes/sistema');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar cadastrar opção!');
            redirect(base_url() . 'configuracoes/sistema');
        }
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para editar configurações do sistema.');
            redirect(base_url());
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect(base_url('configuracoes/sistema'));
        }

        $descricao = padronizarString($_POST['descricao']);

        $data = array(
            'descricao' => $descricao,
            'setor' => $_POST['setor'],
        );

        if ($this->configs_model->edit('configs_opcoes', $data, 'id_opcao', $_POST['id_opcao'])) {
            $this->session->set_flashdata('sucesso', 'Opção alterada com sucesso!');
            redirect(base_url() . 'configuracoes/sistema');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar alterar opção!');
            redirect(base_url() . 'configuracoes/sistema');
        }
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para excluir configurações do sistema.');
            redirect(base_url());
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect(base_url('configuracoes/sistema'));
        }

        if ($this->configs_model->delete('configs_opcoes', 'id_opcao', $_POST['id_opcao'])) {
            $this->session->set_flashdata('sucesso', 'Opção excluída com sucesso!');
            redirect(base_url() . 'configuracoes/sistema');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir opção!');
            redirect(base_url() . 'configuracoes/sistema');
        }
    }


    public function minhaConta()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $data['dados'] = $this->mxcode_model->getUsuario(getUserId());
        $data['usuario'] = $this->mxcode_model->getById(getUserId());
        $data['view'] = 'mxcode/minhaConta';
        $data['minhaConta'] = 'Conta';
        $this->load->view('tema/topo', $data);
    }

    public function alterarSenha()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
        $antigaSenha = $this->input->post('antigaSenha');
        $senha = $this->input->post('novaSenha');

        $usuario = $this->mxcode_model->getUsuario($this->session->userdata('id'));


        if (password_verify($antigaSenha, $usuario->senha)) {
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

        $data['results'] = $this->mxcode_model->pesquisar($termo, getUserId());
        $this->data['produtos'] = $data['results']['produtos'];
        $this->data['servicos'] = $data['results']['servicos'];
        $this->data['os'] = $data['results']['os'];
        $this->data['clientes'] = $data['results']['clientes'];
        $this->data['view'] = 'mxcode/pesquisa';
        $this->load->view('tema/topo', $this->data);
    }

    public function login()
    {
        if (($this->session->userdata('logado'))) {
            redirect($this->index());
        }
        $this->load->view('mxcode/login');
    }

    public function sair()
    {
        if ((session_id()) && ($this->session->userdata('logado'))) {
            gravaLog(getUserId(), getUserName(), getUserEmail(), 'Logoff no sistema', getenv("REMOTE_ADDR"));
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
            //    print_array(validation_errors());
            $msgValidation = validation_errors();
            $this->session->set_flashdata('erro', 'Formato de e-mail inválido.');
            redirect('mxcode/login');
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('senha');
            $usuario = $this->mxcode_model->check_credentials($email);

            if ($usuario) {
                if (password_verify($password, $usuario->senha)) {
                    if ($usuario->ativo == 0) {
                        $this->session->set_flashdata('erro', 'Conta de usuário desativada.<br>Por favor, contate o administrador do sistema.');
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
                    gravaLog(getUserId(), getUserName(), getUserEmail(), 'Login no sistema', getenv("REMOTE_ADDR"));
                    redirect('/');
                } else {
                    gravaLog($usuario->id_usuarios, $usuario->nome, $usuario->email, 'Tentativa de login recusada: senha incorreta', getenv("REMOTE_ADDR"));
                    $this->session->set_flashdata('erro', 'Dados de acesso inválidos, por favor tente novamente.');
                    redirect('mxcode/login');
                }
            } else {
                gravaLog(null, null, $email, 'Tentativa de login recusada: email inexistente', getenv("REMOTE_ADDR"));
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
        $data['dados'] = $this->mxcode_model->getEmitente(getUserId());
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
            'complemento' => padronizarString($this->input->post('complemento')),
            'numero' => $this->input->post('numero'),
            'bairro' => padronizarString($this->input->post('bairro')),
            'cidade' => padronizarString($this->input->post('cidade')),
            'uf' => padronizarString($this->input->post('uf')),
            'cep' => $this->input->post('cep'),
            'telefone' => $this->input->post('telefone'),
            'email' => $this->input->post('email'),
            'id_usuario' => getUserId(),
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
            $this->session->set_flashdata('erro', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $data = array(
            'emitente' => padronizarString($this->input->post('emitente')),
            'cnpj' => $this->input->post('cnpj'),
            'ie' => $this->input->post('ie'),
            's_n' => $this->input->post('s_n'),
            'logradouro' => padronizarString($this->input->post('logradouro')),
            'complemento' => padronizarString($this->input->post('complemento')),
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

        $logo_atual = $this->mxcode_model->getLogoEmitente(getUserId());

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

        $logo_atual = $this->mxcode_model->getLogoEmitente(getUserId());
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
            $avatar_atual = $this->mxcode_model->getAvatarUsuario(getUserId());

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
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a foto de perfil.<br>ERRO: do_upload()');
                //                redirect(base_url() . 'mxcode/minhaConta');
            }

            $retorno = $this->mxcode_model->editAvatarUsuario(getUserId(), $image);
            if ($retorno) {
                $usuario = $this->mxcode_model->getUsuario(getUserId());
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
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a foto de perfil.<br>ERRO: editAvatarUsuario()');
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
        $avatar_atual = $this->mxcode_model->getAvatarUsuario(getUserId());

        if ($avatar_atual->avatar) {
            unlink($dir . '/' . $avatar_atual->avatar);
        }

        $retorno = $this->mxcode_model->excluirAvatarUsuario(getUserId());
        if ($retorno) {
            $usuario = $this->mxcode_model->getUsuario(getUserId());
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
            'complemento' => $this->input->post('complemento'),
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


    public function setWidgetLancamentos()
    {
        if ($_POST['value'] == 1) {
            $this->configs_model->setWidgetLancamentos(getUserId());
        } else {
            $this->configs_model->unsetWidgetLancamentos(getUserId());
        }
    }

    public function setWidgetCartaoCredito()
    {
        if ($_POST['value'] == 1) {
            $this->configs_model->setWidgetCartaoCredito(getUserId());
        } else {
            $this->configs_model->unsetWidgetCartaoCredito(getUserId());
        }
    }

    public function setWidgetInvestimentos()
    {
        if ($_POST['value'] == 1) {
            $this->configs_model->setWidgetInvestimentos(getUserId());
        } else {
            $this->configs_model->unsetWidgetInvestimentos(getUserId());
        }
    }

    public function setWidgetPendencias()
    {
        if ($_POST['value'] == 1) {
            $this->configs_model->setWidgetPendencias(getUserId());
        } else {
            $this->configs_model->unsetWidgetPendencias(getUserId());
        }
    }
}
