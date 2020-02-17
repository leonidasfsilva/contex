<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Processamento extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mxcode_model', '', true);
        $this->load->model('configs_model', '', true);
        $this->load->model('usuarios_model', '', true);
        $this->load->model('financeiro_model', '', true);
        $this->load->model('investimentos_model', '', true);
        $this->load->model('fatura_model', '', true);
        $this->load->model('pendencia_model', '', true);
        $this->load->helper('file');
        $this->load->library('upload');
        $this->load->library('image_lib');
    }

    public function preencheConfigsUsuarios()
    {
        $dados_usuarios = $this->usuarios_model->getDadosUsuarios();
        $dados_configs = $this->configs_model->getDadosConfigs();

        foreach ($dados_usuarios as $u) {
            foreach ($dados_configs as $c) {

                if ($u->id_usuarios == $c->id_usuario) {
                    print_array($u->nome . ': Usuário possui configurações cadastradas!');

                }
            }

            $data = array(
                'id_usuario' => $u->id_usuarios,
                'nome' => $u->nome,
                'avatar' => $u->avatar,
            );

            if($this->configs_model->registraConfigsUsuario($data) == true) {
                print_array($u->nome . ':Configurações de usuário cadastradas!');

            }

        }

    }

    public function alertaFatura()
    {

    }

    function enviaEmail()
    {
        $usuarios = $this->usuarios_model->getUsuariosAtivos();

        print_array_exit($usuarios);

        $link = base_url('redefinirsenha/verificacao?token=');
        $date = date("d/m/Y h:i");
        $ip = getenv("REMOTE_ADDR");
        $navegador = $_SERVER['HTTP_USER_AGENT'];
        $nomeremetente = $result->nome;
        $emailremetente = $result->email;

        $headers_ = "MIME-Version: 1.0\r\n";
        $headers_ .= "Content-type: text/html; charset=utf-8\r\n";
        $headers_ .= "From: nao-responda@mxcode.net\r\n";
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
                  <img src="' . base_url() . '/assets/img/contex_brand.png" alt="CONTEX - Sistema de Gestão" style="width:120px;">
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
                  <span style="font-size: 14pt"><strong>Equipe MXCODE Sistemas</strong></span>
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

    }
}