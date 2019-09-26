<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cadastro extends CI_Controller
{

    /**
     * author: Leônidas Ferreira
     * email: leonidas.f.silva@hotmail.com
     *
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->model('cadastro_model', '', true);
        $this->load->library('form_validation');
    }

    public function index()
    {
//        if (($data == null) || ($this->session->userdata('logado'))) {
//            redirect('mxcode/login');
//        }
//        redirect('redefinirsenha/');
        $this->load->view('mxcode/cadastro');

    }

//    PRE-CADASTRO NO SISTEMA
    public function cadastrar()
    {
        if ($this->session->userdata('logado') == true) {
            redirect('mxcode/login');
        } else {
            if (!$_POST) {
                redirect('cadastro');
            }

            $nome = $this->input->post('nome') . ' ' . $this->input->post('sobrenome');
            $email = $this->input->post('email');
            $senha = $this->input->post('novaSenha');
            $confirmarSenha = $this->input->post('confirmarSenha');

            if ($this->cadastro_model->verificaEmailExistente($email) == true) {
                gravaLog(null, 'Usuário desconhecido', $email, 'Tentativa de cadastro recusada: email já existente', getenv("REMOTE_ADDR"));
                $this->session->set_flashdata('erro', 'O email informado já se encontra em uso, por favor informe um email diferente.');
                redirect('cadastro');
            }

            if (($senha != null) && ($confirmarSenha != null) && ($senha == $confirmarSenha)) {
                $senhaUsuario = password_hash($confirmarSenha, PASSWORD_DEFAULT);
            }

            //$token = sha1(uniqid(rand('AaBbCcDdEeFfGgHh1234567890'), 80));
            $token = str_shuffle(
                '1234567890' .
                'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvXxYyWwZz' .
                'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvXxYyWwZz' .
                '1234567890'
            );

            $preCadastro = array(
                'nome' => $nome,
                'email' => $email,
                'senha' => $senhaUsuario,
            );

            if ($this->cadastro_model->gravaPreCadastro($preCadastro) == true) {
                $id_pre_cadastro = $this->db->insert_id();
            } else {
                $this->session->set_flashdata('erro', 'Não foi possível registrar pré cadastro de usuário.<br>ERRO: gravaPreCadastro()');
                redirect('cadastro');
            }

            $validacao = array(
                'id_pre_cadastro' => $id_pre_cadastro,
                'email' => $email,
                'token' => $token,
            );

            if (!$this->cadastro_model->gravaValidacao($validacao) == true) {
                $this->session->set_flashdata('erro', 'Não foi possível registrar pré cadastro de usuário.<br>ERRO: gravaValidacao()');
                redirect('cadastro');
            }

            //aqui entra o MAIL() para enviar o link de verificação de conta com o token e id de validação gerado para o usuário
            $link = base_url('cadastro/validacao?token=' . $token . '&id=' . $id_pre_cadastro);
            $date = date("d/m/Y h:i");
            $ip = getenv("REMOTE_ADDR");
            $navegador = $_SERVER['HTTP_USER_AGENT'];
            $nomedestinatario = $nome;
            $emaildestinatario = $email;

            //AUTO RESPOSTA
            $headers_ = "MIME-Version: 1.0\r\n";
            $headers_ .= "Content-type: text/html; charset=utf-8\r\n";
            $headers_ .= "From: nao-responda@mxcode.net\r\n";
            $assunto_resposta = "CONTEX - Validação de sua conta";

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
                  <img src="' . base_url() . 'assets/img/contex_brand.png" alt="CONTEX - Sistema de Gestão" style="width:120px;">
                </td>
              </tr>
              <tr>
                <td style="padding-top: 20px">
                  <span style="font-size: 16pt;">Olá, ' . $nomedestinatario . '!</span>
                </td>
              </tr>
              <tr>
                <td>
                  <span>Recebemos sua solicitação de cadastro em nosso sistema.</span>
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
                    Precisamos verificar se foi você mesmo quem solicitou o cadastro em nosso sistema, para confirmar a verificação, clique no botão abaixo para validar sua conta:</span>
                </td>
              </tr>
              <tr>
                <td style="border-radius: 3px; padding: 20px 20px 40px 20px; text-align: left">
                  <a href="' . $link . '" target="_blank" style="padding: 10px 30px; background-color:#0098da; border: 1px solid #0098da;border-radius: 3px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">
                    VALIDAR MINHA CONTA         
                  </a>
                </td>
              </tr>
              <tr>
                <td>
                  <p>Por questões de segurança, este link só estará válido por alguns minutos, caso este link já tenha expirado, efetue uma nova solicitação clicando no botão <strong>Não recebi o email de verificação</strong> na página de cadastro do sistema.</p>
                  <p>Caso não tenha solicitado o cadastro em nosso sistema, por favor, desconsidere e exclua este email, nenhuma outra ação é necessária.</p>
                  <p>Caso ainda esteja com dúvidas, contate-nos em <a href="mailto:suporte@mxcode.net?Subject=Solicitação de suporte" target="_top"><strong>suporte@mxcode.net</strong></a>
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

            mail($emaildestinatario, $assunto_resposta, $msg_resposta, $headers_);

            $this->session->set_flashdata(
                'sucesso',
                'Pré cadastro realizado com sucesso!<br>Enviamos um e-mail para <strong class="text-success">' . $email . '</strong> verifique sua caixa de entrada ou pasta de <i>spam</i> e siga as instruções para validar sua conta.');
            redirect('mxcode/login');
        }
    }

    public function validacao()
    {
        if ($this->session->userdata('logado') == true) {
            redirect('mxcode/login');

        } else {
            $tokenUsuario = $this->input->get('token');
            $id = $this->input->get('id');

            if ($id != null) {
                $query = $this->cadastro_model->validaTokenById($id);

                if ($query->num_rows() > 0) {
                    $result = $query->row();
                    $tokenReal = $result->token;

                    if ($tokenUsuario != null && $tokenUsuario == $tokenReal) {

                        $query = $this->cadastro_model->verificaValidadeToken($id);
                        $result = $query->row();

                        //tempo de validade do link (em minutos)
                        $validade = 30;

                        if (isset($result->validade) && $result->validade < $validade) {

                            $this->cadastro_model->validaPreCadastro($id);

                            $qr = $this->cadastro_model->getPreCadastroById($id);
                            if ($qr->num_rows() > 0) {
                                $result = $qr->row();

                                $data = array(
                                    'nome' => $result->nome,
                                    'email' => $result->email,
                                    'senha' => $result->senha,
                                    'permissoes_id' => 6,
                                    'ativo' => 1,
                                );

                                if ($this->cadastro_model->registraUsuario($data) == true) {
                                    $this->session->set_flashdata(
                                        'sucesso',
                                        'Conta verificada com sucesso!<br>Obrigado por validar sua conta, agora você já pode acessar o sistema utilizando seu email e senha.');
                                    redirect('mxcode/login');
                                } else {
                                    $this->session->set_flashdata(
                                        'erro',
                                        'Erro ao tentar registrar nova conta de usuário.<br>ERRO: registraUsuario()');
                                    redirect('cadastro');
                                }
                            }

                        } else {
                            $this->cadastro_model->invalidaToken($id);
                            $this->session->set_flashdata(
                                'erro',
                                'Link de validação de conta expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="verificar_conta()">Não recebi o email de verificação</a>.');
                            redirect('cadastro');
                        }
                    } else {
                        $this->session->set_flashdata(
                            'erro',
                            'Link de validação de conta expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="verificar_conta()">Não recebi o email de verificação</a>.');
                        redirect('cadastro');
                    }
                } else {
                    $this->session->set_flashdata(
                        'erro',
                        'Link de validação de conta expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="verificar_conta()">Não recebi o email de verificação</a>.');
                    redirect('cadastro');
                }
            } else {
                $this->session->set_flashdata(
                    'erro',
                    'Link de validação de conta expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="verificar_conta()">Não recebi o email de verificação</a>.');
                redirect('cadastro');
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

                $queryToken = $this->cadastro_model->validaTokenById($id);
                $resultToken = $queryToken->row();
                $tokenReal = $resultToken->token;

                if ($tokenReal == $token) {

                    $query = $this->cadastro_model->verificaValidadeToken($id);
                    $result = $query->row();

                    //tempo de validade do link (em minutos)
                    $validade = 30;

                    if (isset($result->validade) && $result->validade < $validade) {

                        $query = $this->cadastro_model->getDadosUsuarioById($resultToken->id_usuario);
                        $result = $query->row();

                        if (($novasenha != null) && ($repitasenha != null) && ($novasenha == $repitasenha)) {
                            $data = array(
                                'senha' => password_hash($repitasenha, PASSWORD_DEFAULT)
                            );

                            $this->cadastro_model->atualizaAdmin($resultToken->id_usuario, $data);
                            $this->cadastro_model->invalidaToken($id);
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
            $this->cadastro_model->invalidaToken($id);
            redirect('mxcode/login');
        }
    }

}
