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
        $this->load->view('mxcode/redefinir_senha', $data);

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
                    $assunto_resposta = "CONTEX - Redefinição de senha";

                    $msg_resposta = '
<html>
<head>
<style>
#inner_table {
  border: 2px solid lightgray;
  border-radius: 10px;
}
td {
  padding: 0px 20px 20px 20px;
  text-align: left;    
}
</style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="white">
	<tbody>
		<tr>
          <td valign="top" width="100%">
            <table id="inner_table" align="center" cellpadding="0" cellspacing="0" border="0" align="center">
              <tr>
                <td colspan="2" style="border-bottom: 4px solid #0098da; padding: 20px 20px 20px 20px;">
                  <img src="https://mxcode.net/contex/assets/img/contex_brand.png" alt="CONTEX - Sistema de Gestão" style="width:120px;">
                </td>
              </tr>
              <tr>
                <td style="padding-top: 20px">
                  <span style="font-size: 16pt;">Olá, ' . $nomeremetente . '!</span>
                </td>
              </tr>
              <tr>
                <td>
                  <span>Recebemos uma solicitação de alteração de senha para seu cadastro em nosso sistema.</span>
                </td>
              </tr>
              <tr>
                <td>
                  <span>Origem da solicitação:
                    <br />
                    IP: ' . $ip . '
                    <br />
                    Navegador: ' . $navegador . '
                    <br />
                    Data e hora: ' . $date . '
                    <br />
                    <br />
                    Caso você tenha solicitado a troca de sua senha, clique no botão abaixo:</span>
                </td>
              </tr>
              <tr>
                <td style="border-radius: 3px; padding: 20px 20px 40px 20px; text-align: left">
                  <a href="' . $link . '" target="_blank" style="padding: 10px 30px; background-color:#0098da; border: 1px solid #0098da;border-radius: 3px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">
                    REDEFINIR SENHA         
                  </a>
                </td>
              </tr>
              <tr>
                <td>
                  <p>Por questões de segurança, este link só estará válido por alguns minutos, caso este link já tenha expirado, efetue uma nova solicitação clicando no botão <strong>Esqueci minha senha</strong> na página inicial do sistema.</p>
                  <p>Caso não tenha solicitado a troca de sua senha, por favor, desconsidere e exclua este email, nenhuma outra ação é necessária. Não se preocupe, sua conta está segura.</p>
                  <p>Caso necessite de suporte para o sistema Contex, contate-nos em <a href="mailto:suporte@mxcode.net?Subject=Solicitação de suporte" target="_top"><strong>suporte@mxcode.net</strong></a>
                </td>
              </tr>
              <tr>
                <td>
                  <span>Atenciosamente,</span>
                  <br>
                  <span style="font-size: 14pt"><strong>Equipe MX Code Sistemas</strong></span>
                  <br>
                  <a href="https://mxcode.net/contex" target="_blank"><p><strong>https://mxcode.net/contex</strong></a><br>CONTEX - Sistema de Gestão</p>
                </td>
              </tr>
              <tr>
                <td style="border-top: 2px dotted #0098da; padding-top: 20px">
                  <p style="font-size:10pt; color: gray">
                  Não é necessário responder este e-mail, mensagem automática.
                  <p>
                </td>
              </tr>
            </table>
          </td>
		</tr>
	</tbody>
  </table>                
</body>
</html>
                ';

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
            $novasenha = $this->input->post('novaSenha');
            $repitasenha = $this->input->post('confirmarSenha');

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

                        $query = $this->redefinicao_model->getDadosUsuarioById($resultToken->id_usuario);
                        $result = $query->row();

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
                            $this->load->view('mxcode/redefinir_senha', $data3);

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
