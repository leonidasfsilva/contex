<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Redefinirsenha extends CI_Controller
{

    /**
     * author: Leônidas Ferreira
     * email: leonidas.f.silva@hotmail.com
     *
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->model('redefinicao_model', '', true);
        $this->load->library('form_validation');
    }

    public function index($data = null)
    {
        if (($data == null) || ($this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
//        redirect('redefinirsenha/');
        $this->load->view('mapos/redefinir_senha', $data);

    }

//    RECUPERAÇÂO DE SENHA
    public function gerarToken()
    {
        if ($this->session->userdata('logado') == true) {
            redirect('mxcode/login');

        } else {
            $usuarioemail = $this->input->post('email');

            if (isset($usuarioemail)) {

                $query = $this->redefinicao_model->getDadosUsuarioByEmail($usuarioemail);

                if ($query->num_rows() > 0) {
                    $result = $query->row();

                    //$token = sha1(uniqid(rand('AaBbCcDdEeFfGgHh1234567890'), 80));
                    $token = str_shuffle(
                        '1234567890' .
                        'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvXxYyWwZz' .
                        'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvXxYyWwZz' .
                        '1234567890'
                    );

                    $data = array(
                        'token' => $token,
                        'email' => $result->email,
                        'id_usuario' => $result->id_usuarios
                    );

                    $this->redefinicao_model->gravaToken($data);
                    $last_id = $this->db->insert_id();

                    $ajax = array(
                        'token' => $token,
                        'email' => $result->email,
                        'id' => $last_id,
                        'validacao' => true
                    );


                    //aqui entra o MAIL() para enviar o link de recuperação com o token gerado para o usuário


                    $link = base_url('redefinirsenha/verificacao?token=' . $token . '&id=' . $last_id);
                    $date = date("d/m/Y h:i");
                    $ip = getenv("REMOTE_ADDR");
                    $navegador = $_SERVER['HTTP_USER_AGENT'];
                    $nomeremetente = $result->nome;
                    $emailremetente = $result->email;

                    //AUTO RESPOSTA
                    $headers_ = "MIME-Version: 1.0\r\n";
                    $headers_ .= "Content-type: text/html; charset=utf-8\r\n";
                    $headers_ .= "From: naoresponda@mxcode.net\r\n";
                    $assunto_resposta = "Redefinição de senha";

                    $msg_resposta = '
                <p>Olá, ' . $nomeremetente . '!</p>
                <p>Recebemos um pedido para alteração de sua senha de cadastro em nosso sistema.
                <br />
                Origem da solicitação:
                <br />
                IP: ' . $ip . '
                <br />
                Navegador: ' . $navegador . '
                <br />
                Data e hora: ' . $date . '
                <br />
                <br />
                Caso você tenha solicitado a troca de sua senha, clique no botão abaixo:
                <br />
                <br />
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <table cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="border-radius: 2px;" bgcolor="#ED2939">
                                        <a href="' . $link . '" target="_blank" style="padding: 8px 12px; border: 1px solid #ED2939;border-radius: 2px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">
                                            Redefinir minha senha         
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <br />
                <p>Por questões de segurança, este link só estará válido por alguns minutos, caso seu link tenha expirado, faça uma nova solicitação clicando no botão <strong>Esqueci minha senha</strong></a> na página inicial do sistema.
                <br />
                Caso não tenha solicitado a troca de sua senha, por favor, desconsidere e exclua este email, nenhuma outra ação é necessária. Não se preocupe, sua conta está segura.
                <br />
                <p>Caso necessite de suporte específico, contate-nos em <a href="mailto:suporte@mxcode.net?Subject=Solicitação de suporte" target="_top"><strong>suporte@mxcode.net</strong></a>
                <br />
                <p>Atenciosamente,</p>
                <h3><strong>Equipe MX Code Sistemas.</strong></h3>
                <a href="https://mxcode.net" target="_blank"><strong>https://mxcode.net</strong></a><br />
                <br />_________________________________________________________________________
                <br />
                Não é necessário responder este e-mail, mensagem automática.';

                    mail($emailremetente, $assunto_resposta, $msg_resposta, $headers_);

                    //por hora, para efeito de teste, estou retornando o token para o aJax
                    echo json_encode($ajax, JSON_PRETTY_PRINT);


                } else {

                    $data = array(
                        'validacao' => false,
                        'email' => $usuarioemail
                    );

                    echo json_encode($data, JSON_PRETTY_PRINT);
                }
            } else {
                redirect(site_url(), 'refresh');
            }
        }

    }

    public function verificacao()
    {
        if ($this->session->userdata('logado') == true) {
            redirect('mxcode/login');

        } else {
            $tokenUsuario = $this->input->get('token');
            $id = $this->input->get('id');

            if ($id != null) {
                $query = $this->redefinicao_model->validaTokenById($id);

                if ($query->num_rows() > 0) {
                    $result = $query->row();
                    $tokenReal = $result->token;

                    if ($tokenUsuario != null && $tokenUsuario == $tokenReal) {

                        $query = $this->redefinicao_model->verificaValidadeToken($id);
                        $result = $query->row();

                        //tempo de validade do link (em minutos)
                        $validade = 30;

                        if (isset($result->validade) && $result->validade < $validade) {

                            $qr = $this->redefinicao_model->getDadosUsuarioById($result->id_usuario);
                            $rs = $qr->row();

                            $data = array(
                                'id' => $id,
                                'nome' => $rs->nome,
                                'token' => $tokenReal,
                            );

                            $this->index($data);

                        } else {
                            $this->redefinicao_model->invalidaToken($id);
                            $this->session->set_flashdata(
                                'erro',
                                'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
                            redirect('mxcode/login');
                        }

                    } else {
                        $this->session->set_flashdata(
                            'erro',
                            'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
                        redirect('mxcode/login');
                    }
                } else {
                    $this->session->set_flashdata(
                        'erro',
                        'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
                    redirect('mxcode/login');
                }
            } else {
                $this->session->set_flashdata(
                    'erro',
                    'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
                redirect('mxcode/login');
            }
        }
    }

    public function alterarSenha()
    {
        if ($this->session->userdata('logado') == true) {
            redirect('mxcode/login');

        } else {
            $id = (int)$this->input->post('id');
            $token = $this->input->post('token');
            $novasenha = $this->input->post('novasenha');
            $repitasenha = $this->input->post('repitasenha');

            if (($token != null) && ($id != null)) {

                $queryToken = $this->redefinicao_model->validaTokenById($id);
                $resultToken = $queryToken->row();
                $tokenReal = $resultToken->token;

                if ($tokenReal == $token) {

                    $query = $this->redefinicao_model->verificaValidadeToken($id);
                    $result = $query->row();

                    //tempo de validade do link (em minutos)
                    $validade = 30;

                    if (isset($result->validade) && $result->validade < $validade) {
                        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible small font-weight-bold" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> ', '</div>');
                        $this->form_validation->set_rules('novasenha', '"Nova senha"', 'required|min_length[6]');
//                        $this->form_validation->set_rules('repitasenha', '"Confirme nova senha"', 'required|min_length[6]');

                        $query = $this->redefinicao_model->getDadosUsuarioById($resultToken->id_usuario);
                        $result = $query->row();

                        if ($this->form_validation->run() == FALSE) {
                            $data = array(
                                'id' => $id,
                                'nome' => $result->nome,
                                'token' => $token,
                            );
                            $this->load->view('mapos/redefinir_senha', $data);

                        } else {

                            if (($novasenha != null) && ($repitasenha != null) && ($novasenha == $repitasenha)) {
                                $data = array(
                                    'senha' => password_hash($repitasenha, PASSWORD_DEFAULT)
                                );

                                $this->redefinicao_model->atualizaAdmin($resultToken->id_usuario, $data);
                                $this->redefinicao_model->invalidaToken($id);
                                $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso!');
                                redirect('mxcode/login');

                            } elseif (($novasenha != null) && ($repitasenha != null) && ($novasenha != $repitasenha)) {
                                $data3 = array(
                                    'id' => $id,
                                    'nome' => $result->nome,
                                    'token' => $token,
                                    'senhaAlterada' => false
                                );
                                $this->session->set_flashdata('erro', 'As senhas não correspondem.');
                                $this->load->view('mapos/redefinir_senha', $data3);

                            } else {
                                $this->session->set_flashdata(
                                    'erro',
                                    'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
                                redirect('mxcode/login');

                            }
                        }
                    } else {
                        $this->session->set_flashdata(
                            'erro',
                            'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
                        redirect('mxcode/login');
                    }

                }
            } else {
                $this->session->set_flashdata(
                    'erro',
                    'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
                redirect('mxcode/login');
            }
        }
    }

    public function invalidaToken()
    {
        if ($this->session->userdata('logado') == true) {
            redirect('mxcode/login');

        } else {
            $id = (int)$this->input->post('id');
            $this->redefinicao_model->invalidaToken($id);
            redirect('mxcode/login');
        }
    }

}
