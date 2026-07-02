<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mxcode extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('chamados_model', '', true);
        $this->load->model('configs_model', '', true);
        $this->load->model('mxcode_model', '', true);
        $this->load->model('financeiro_model', '', true);
        $this->load->model('investimentos_model', '', true);
        $this->load->model('fatura_model', '', true);
        $this->load->model('pendencia_model', '', true);
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
        $data['usuario']             = $this->mxcode_model->getById(getUserId());
        $data['lancamentos']         = $this->financeiro_model->getTotal(getUserId());
        $data['investimentos']       = $this->investimentos_model->getTotal(getUserId());
        $data['pendencias']          = $this->pendencia_model->getTotalDebito(getUserId());
        $data['fatura']              = $this->fatura_model->getValorTotalFaturaAtual(getUserId());
        $data['widgetLancamentos']   = $this->configs_model->getWidgetLancamentos(getUserId());
        $data['widgetCartaoCredito'] = $this->configs_model->getWidgetCartaoCredito(getUserId());
        $data['widgetInvestimentos'] = $this->configs_model->getWidgetInvestimentos(getUserId());
        $data['widgetPendencias']    = $this->configs_model->getWidgetPendencias(getUserId());
        $data['anuncios']            = $this->anuncios_model->getAnuncios('habilitado = 1 AND direcionado != 1');
        $data['direcionados']        = $this->anuncios_model->getAnuncios('habilitado = 1 AND direcionado = 1 AND id_usuario = ' . getUserId());

        $data['menuPainel'] = true;
        $data['view']       = 'mxcode/painel';
        $this->load->view('tema/topo', $data);
    }

    public function perfil()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $data['dados']      = $this->mxcode_model->getUsuario(getUserId());
        $data['usuario']    = $this->mxcode_model->getById(getUserId());
        $data['view']       = 'mxcode/perfil';
        $data['minhaConta'] = 'Conta';
        $this->load->view('tema/topo', $data);
    }

    public function alterarSenha()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
        $antigaSenha = $this->input->post('antigaSenha');
        $senha       = $this->input->post('novaSenha');

        $usuario = $this->mxcode_model->getUsuario($this->session->userdata('id'));


        if (password_verify($antigaSenha, $usuario->senha)) {
            $result = $this->mxcode_model->alterarSenha($usuario->id_usuarios, $senha);
            if ($result == true) {
                $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso!');
                redirect(base_url() . 'mxcode/perfil');
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a senha!');
                redirect(base_url() . 'mxcode/perfil');
            }
        } else {
            $this->session->set_flashdata('erro', 'Senha atual incorreta!');
            redirect(base_url() . 'mxcode/perfil');
        }
    }

    public function pesquisar()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $termo = $this->input->get('termo');

        $modulosBusca          = $this->configs_model->getModulosBuscaGlobal(getUserId());
        $data['results']       = $this->mxcode_model->pesquisar($termo, getUserId(), $modulosBusca);
        $this->data['modulosBusca'] = $modulosBusca;
        $this->data['lancamentos'] = $data['results']['lancamentos'];
        $this->data['faturas']     = $data['results']['faturas'];
        $this->data['despesas']    = $data['results']['despesas'];
        $this->data['clientes']    = $data['results']['clientes'];
        $this->data['cartoes']     = $data['results']['cartoes'];
        $this->data['view']        = 'mxcode/pesquisa';
        $this->load->view('tema/topo', $this->data);
    }

    public function login()
    {
        if (($this->session->userdata('logado'))) {
            redirect($this->index());
        }

        if ($this->session->userdata('last_url')) {
            $this->session->set_flashdata('error', 'Acesse sua conta para continuar');
        }

        $this->load->view('mxcode/login');
    }

    public function logout($forcedLogout = false)
    {
        if ((session_id()) && ($this->session->userdata('logado'))) {
            gravaLog(getUserId(), getUserName(), getUserEmail(), 'Logout no sistema', getenv("REMOTE_ADDR"));
            reconciliarFinanceiroUsuario(getUserId(), 'logoff');
        }

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

        if (!$this->form_validation->run()) {
            // print_array(validation_errors());
            $msgValidation = validation_errors();
            $this->session->set_flashdata('erro', 'Formato de e-mail inválido.');
            redirect('mxcode/login');
        }

        $email    = $this->input->post('email');
        $password = $this->input->post('senha');
        $usuario  = $this->mxcode_model->check_credentials($email);

        if ($usuario) {
            if (password_verify($password, $usuario->senha)) {
                if ($usuario->ativo == 0) {
                    $this->session->set_flashdata('erro', 'Conta de usuário desativada.<br>Por favor, contate o administrador do sistema.');
                    redirect('mxcode/login');
                }

                if (checkMaintenanceMode()) {
                    if ($usuario->permissoes_id != 1) {
                        $this->session
                            ->set_flashdata(
                                'erro',
                                '<strong>SISTEMA EM MANUTENÇÃO</strong><br>Não foi possível efetuar seu login no momento, pois estamos realizando alguns ajustes e trabalhando para normalizar o sistema o mais breve possível.<br>Agradecemos a compreensão.');
                        redirect('mxcode/login');
                    }
                }

                $session_data = array(
                    'nome'      => $usuario->nome,
                    'avatar'    => $usuario->avatar,
                    'email'     => $usuario->email,
                    'id'        => $usuario->id_usuarios,
                    'permissao' => $usuario->permissoes_id,
                    'logado'    => true
                );

                $this->session->set_userdata($session_data);
                gravaLog(getUserId(), getUserName(), getUserEmail(), 'Login no sistema', getenv("REMOTE_ADDR"));
                reconciliarFinanceiroUsuario(getUserId(), 'login');

                if ($this->session->userdata('last_url')) {
                    header('location:' . $this->session->userdata('last_url'));
                    return;
                }
                redirect('mxcode/login');
            }
            gravaLog($usuario->id_usuarios, $usuario->nome, $usuario->email, 'Tentativa de login recusada: senha incorreta', getenv("REMOTE_ADDR"));
            $this->session->set_flashdata('erro', 'Dados de acesso inválidos, por favor tente novamente.');
            redirect('mxcode/login');
        }
        gravaLog(null, null, $email, 'Tentativa de login recusada: email inexistente', getenv("REMOTE_ADDR"));
        $this->session->set_flashdata('erro', 'Dados de acesso inválidos, por favor tente novamente.');
        redirect('mxcode/login');
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
        $fileNameFormat = sprintf('dump_contex_%s-%s', ENVIRONMENT, date('d-m-Y'));
        $prefs          = array(
            'format'             => 'zip',
            'foreign_key_checks' => false,
            'filename'           => sprintf('%s.sql', $fileNameFormat)
		);

        try {
            $backup = $this->dbutil->backup($prefs);

            $this->load->helper('file');
            write_file(base_url() . 'backup/backup.zip', $backup);

            $this->load->helper('download');
            force_download(sprintf('%s.zip', $fileNameFormat), $backup);
            $this->session->set_flashdata('sucesso', 'Backup do banco de dados exportado com sucesso!');
            redirect(base_url());
        } catch (\Throwable $e) {
            $this->session->set_flashdata('erro', 'Erro ao tentar gerar backup do banco de dados.');
            redirect(base_url());
        }
    }

    public function emitente()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $data['menuConfiguracoes'] = 'Configuracoes';
        $data['dados']             = $this->mxcode_model->getEmitente(getUserId());
        $data['view']              = 'mxcode/emitente';
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
            $dir   = 'assets/uploads/logomarcas';
            $image = $this->do_upload($dir, base_url('mxcode/emitente'));
        } else {
            $image = null;
        }

        $data = array(
            'logomarca'   => $image,
            'emitente'    => padronizarString($this->input->post('emitente')),
            'cnpj'        => $this->input->post('cnpj'),
            'ie'          => $this->input->post('ie'),
            's_n'         => $this->input->post('s_n'),
            'logradouro'  => padronizarString($this->input->post('logradouro')),
            'complemento' => padronizarString($this->input->post('complemento')),
            'numero'      => $this->input->post('numero'),
            'bairro'      => padronizarString($this->input->post('bairro')),
            'cidade'      => padronizarString($this->input->post('cidade')),
            'uf'          => padronizarString($this->input->post('uf')),
            'cep'         => $this->input->post('cep'),
            'telefone'    => $this->input->post('telefone'),
            'email'       => $this->input->post('email'),
            'id_usuario'  => getUserId(),
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
            'emitente'    => padronizarString($this->input->post('emitente')),
            'cnpj'        => $this->input->post('cnpj'),
            'ie'          => $this->input->post('ie'),
            's_n'         => $this->input->post('s_n'),
            'logradouro'  => padronizarString($this->input->post('logradouro')),
            'complemento' => padronizarString($this->input->post('complemento')),
            'numero'      => $this->input->post('numero'),
            'bairro'      => padronizarString($this->input->post('bairro')),
            'cidade'      => padronizarString($this->input->post('cidade')),
            'uf'          => padronizarString($this->input->post('uf')),
            'cep'         => $this->input->post('cep'),
            'telefone'    => $this->input->post('telefone'),
            'email'       => $this->input->post('email'),
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

    function do_upload($dir, $url = null)
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
            'upload_path'   => $image_upload_folder,
            'allowed_types' => 'png|jpg|jpeg|bmp|gif',
            'max_size'      => 5000,
            'max_widht'     => 5000,
            'max_height'    => 5000,
            'remove_space'  => true,
            'encrypt_name'  => true,
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
        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a logomarca.');
            redirect(base_url() . 'mxcode/emitente');
        }

        $logo_atual = $this->mxcode_model->getLogoEmitente(getUserId());

        if ($logo_atual->logomarca) {
            unlink('assets/uploads/logomarcas/' . $logo_atual->logomarca);
        }
        $dir  = 'assets/uploads/logomarcas';
        $logo = $this->do_upload($dir, base_url('mxcode/emitente'));

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
        $retorno   = $this->mxcode_model->editLogo($id, $nova_logo);

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

            $dir          = 'assets/uploads/avatars';
            $avatar_atual = $this->mxcode_model->getAvatarUsuario(getUserId());

            if ($avatar_atual->avatar) {
                unlink($dir . '/' . $avatar_atual->avatar);
            }

            if ($image = $this->do_upload($dir, base_url() . 'mxcode/perfil')) {
                $data = array('upload_data' => $this->upload->data());

                $config['image_library']  = 'gd2';
                $config['source_image']   = ($dir) . '/' . $data['upload_data']['file_name'];
                $config['create_thumb']   = false;
                $config['maintain_ratio'] = true;
                $config['width']          = 200;
                $config['height']         = 200;
                $config['new_image']      = ($dir) . '/' . $data['upload_data']['file_name'];
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    $this->session->set_flashdata('erro', $this->image_lib->display_errors());
                }
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a foto de perfil.<br>ERRO: do_upload()');
                //                redirect(base_url() . 'mxcode/perfil');
            }

            $retorno = $this->mxcode_model->editAvatarUsuario(getUserId(), $image);

            if ($retorno) {
                $session_data = array(
                    'avatar' => $image,
                );

                $this->session->set_userdata($session_data);
                $this->session->set_flashdata('sucesso', 'Foto de perfil alterada com sucesso!');
                //                redirect(base_url() . 'mxcode/perfil');
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar alterar a foto de perfil.<br>ERRO: editAvatarUsuario()');
                //                redirect(base_url() . 'mxcode/perfil');
            }
        } else {
            $this->session->set_flashdata('erro', 'Nenhum arquivo enviado.');
            // redirect(base_url() . 'mxcode/perfil');
        }
    }

    public function excluirFotoUsuario()
    {

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $dir          = 'assets/uploads/avatars';
        $avatar_atual = $this->mxcode_model->getAvatarUsuario(getUserId());

        if ($avatar_atual->avatar) {
            unlink($dir . '/' . $avatar_atual->avatar);
        }

        $retorno = $this->mxcode_model->excluirAvatarUsuario(getUserId());

        if ($retorno) {
            $usuario      = $this->mxcode_model->getUsuario(getUserId());
            $session_data = array(
                'avatar' => $usuario->avatar,
            );
            $this->session->set_userdata($session_data);

            $this->session->set_flashdata('sucesso', 'Foto de perfil removida com sucesso!');
            redirect(base_url() . 'mxcode/perfil');
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar excluir a foto de perfil.');
            redirect(base_url() . 'mxcode/perfil');
        }
    }

    public function atualizarPerfil()
    {
        $dados_usuario = $this->mxcode_model->getUsuario(getUserId());
        if (!$dados_usuario->cpf) {
            if ($this->mxcode_model->verificaCPF($_POST['cpf'])) {
                $this->session->set_flashdata('erro', 'O CPF informado pertence a outro usuário');
                redirect(base_url() . 'mxcode/perfil');
            }
        } else {
            if ($dados_usuario->cpf != $_POST['cpf']) {
                if ($this->mxcode_model->verificaCPF($_POST['cpf'])) {
                    $this->session->set_flashdata('erro', 'O CPF informado pertence a outro usuário');
                    redirect(base_url() . 'mxcode/perfil');
                }
            }
        }

        $data = array(
            'nome'        => $this->input->post('nome'),
            'rg'          => $this->input->post('rg'),
            'cpf'         => $this->input->post('cpf'),
            'cep'         => $this->input->post('cep'),
            'logradouro'  => $this->input->post('logradouro'),
            'complemento' => $this->input->post('complemento'),
            'numero'      => $this->input->post('numero'),
            's_n'         => $this->input->post('s_n') == 1 ? 1 : 0,
            'bairro'      => $this->input->post('bairro'),
            'cidade'      => $this->input->post('cidade'),
            'uf'          => $this->input->post('uf'),
            //            'email' => $this->input->post('email'),
            'telefone'    => $this->input->post('telefone'),
        );

        $this->mxcode_model->edit('usuarios', $data, 'id_usuarios', $this->input->post('id_usuarios'));
        $this->session->set_flashdata('sucesso', 'Conta de usuário atualizada com sucesso!');
        redirect(base_url() . 'mxcode/perfil');
    }

    public function error_general()
    {
        $data = array(
            'heading' => 'Oops...',
            'message' => 'Um erro desconhecido ocorreu.'
        );
        $this->load->view('errors/html/error_general', $data);
    }

    public function error_404()
    {
        $data = array(
            'heading' => 'Erro 404: Página não encontrada',
            'message' => 'A página que você solicitou não foi encontrada.<br><a href="' . base_url() . '"> << Voltar à página inicial.</a>'
        );
        $this->load->view('errors/html/error_404', $data);
    }

    public function exibirPermissoes()
    {
        print_array_exit($this->session->userdata('permissao'));
    }

    public function checarPermissoes()
    {
        print_array_exit($this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente'));
    }

    public function teste($get = null)
    {
        $data = array(
            'heading' => 'Erro 404: Página não encontrada',
            'message' => 'A página solicitada não foi encontrada.<br><a href="' . base_url() . '"> << Voltar à página inicial.</a>'
        );

        $data = array(
            'heading' => 'Página para Testes de Scripts PHP',
            'message' => 'Efetue seus testes aqui.<br><a href="' . base_url() . '"> << Voltar à página inicial.</a>'
        );

        if (!$get) {
            return $this->load->view('errors/html/test', $data);
        }

        $n = 4;
        // $ratings = array();
        // resposta certa: 123
        $ratings = [
            [512, 2],
            [123, 3],
            [987, 4],
            [123, 5]
        ];
        // for ($i_ratings = 0; $i_ratings < $n; $i_ratings++) {
        //     array_push($ratings, explode(" ", 4));
        // }

        $out_ = $this->solution($n, $ratings);
        //        dd($get);
        // echo $ratings;
        return $this->load->view('errors/html/test', $data);
    }

    public function phpinfo()
    {
        echo phpinfo();
    }
}
