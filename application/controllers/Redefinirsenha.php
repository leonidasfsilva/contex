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
		if ((!$data) || ($this->session->userdata('logado'))) {
			redirect('mxcode/login');
		}
		//        redirect('redefinirsenha/');
		$this->load->view('mxcode/redefinir_senha', $data);
		
	}
	
	//    RECUPERAÇÂO DE SENHA
	public function gerarToken()
	{
		if ($this->session->userdata('logado')) {
			$this->session->set_flashdata(
				'erro',
				'Método não permitido.');
			redirect('mxcode/login');
		}
		
		$usuarioemail = $this->input->post('email');
		
		if (!$this->input->post('email')) {
			$this->session->set_flashdata(
				'erro',
				'Método não permitido.');
			echo json_encode('Método não permitido.');
			redirect(site_url(), 'refresh');
		}
		
		$query = $this->redefinicao_model->getDadosUsuarioByEmail($usuarioemail);
		
		try {
			if ($query->num_rows()) {
				$result = $query->row();
				
				//$token = sha1(uniqid(rand('AaBbCcDdEeFfGgHh1234567890'), 80));
				$token = hash('sha256', uniqid(generateToken()));
				// $token = generateToken();
				
				$data = array(
					'token'      => $token,
					'email'      => $result->email,
					'id_usuario' => $result->id_usuarios
				);
				
				$this->redefinicao_model->gravaToken($data);
				$last_id = $this->db->insert_id();
				
				$ajax = array(
					'email'     => $result->email,
					'validacao' => true
				);
				
				if (ENVIRONMENT != 'production') {
					$ajax['token'] = $token;
				}
				
				$link           = sprintf('redefinirsenha/verificacao?token=%s', $token);
				$link           = base_url($link);
				$ip             = getenv("REMOTE_ADDR");
				$navegador      = $_SERVER['HTTP_USER_AGENT'];
				$nomeRemetente  = $result->nome;
				$emailremetente = $result->email;
				
				//AUTO RESPOSTA
				$headers_         = "MIME-Version: 1.0\r\n";
				$headers_         .= "Content-type: text/html; charset=utf-8\r\n";
				$headers_         .= "From: nao-responda@mxcode.net\r\n";
				$assunto_resposta = "CONTEX - Redefinição de Senha";
				
				$msg_resposta = getResetPasswordEmail($nomeRemetente, $ip, $navegador, $link);
				
				if (ENVIRONMENT == 'production') {
					mail($emailremetente, $assunto_resposta, $msg_resposta, $headers_);
				}
				
				//por hora, para efeito de teste, estou retornando o token para o aJax
				// header('Content-Type: application/json');
				
				// return $this->output
				// 	->set_content_type('application/json')
				// 	->set_status_header(200)
				// 	->set_output(json_encode($ajax));
				echo json_encode($ajax);
				return;
			}
			
			$data = array(
				'validacao' => false,
				'email'     => $usuarioemail
			);
			
			echo json_encode($data);
			return;
		} catch (\Exception $exception) {
			$data = array(
				'exception' => $exception,
				'validacao' => false,
				'email'     => $usuarioemail
			);
			
			echo json_encode($data, JSON_PRETTY_PRINT);
			return;
		}
	}
	
	public function verificacao()
	{
		if ($this->session->userdata('logado')) {
			redirect('mxcode/login');
		}
		
		$tokenUsuario = $this->input->get('token');
		
		if (!$tokenUsuario) {
			$this->session->set_flashdata(
				'erro',
				'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
			redirect('mxcode/login');
		}
		
		$query = $this->redefinicao_model->checkToken($tokenUsuario);
		
		if ($query->num_rows()) {
			$result    = $query->row();
			$tokenReal = $result->token;
			
			if ($tokenUsuario && $tokenUsuario == $tokenReal) {
				// validade do link (minutos)
				$validade = 30;
				
				if (isset($result->validade) && $result->validade < $validade) {
					$rs = $this->redefinicao_model->getDadosUsuarioById($result->id_usuario)->row();
					
					$data = array(
						'nome'  => $rs->nome,
						'token' => $tokenReal,
					);
					
					$this->redefinicao_model->validaToken($tokenReal);
					$this->index($data);
					return;
				}
				
				$this->redefinicao_model->invalidaToken($tokenReal);
				$this->session->set_flashdata(
					'erro',
					'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
				redirect('mxcode/login');
			}
		}
		
		$this->session->set_flashdata(
			'erro',
			'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
		redirect('mxcode/login');
	}
	
	public function alterarSenha()
	{
		if ($this->session->userdata('logado')) {
			redirect('mxcode/login');
		}
		
		$token       = $this->input->post('token') ?? null;
		$novasenha   = $this->input->post('novaSenha') ?? null;
		$repitasenha = $this->input->post('confirmarSenha') ?? null;
		
		if (!$token) {
			$this->session->set_flashdata(
				'erro',
				'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
			redirect('mxcode/login');
		}
		
		$resultCheckToken = $this->redefinicao_model->checkToken($token)->row();
		$tokenReal        = $resultCheckToken->token;
		
		if ($tokenReal == $token) {
			// validade do link (minutos)
			$validade = 30;
			
			if (isset($resultCheckToken->validade) && $resultCheckToken->validade < $validade) {
				if (($novasenha) && ($repitasenha) && ($novasenha == $repitasenha)) {
					$data = array(
						'senha' => password_hash($repitasenha, PASSWORD_DEFAULT)
					);
					
					$this->redefinicao_model->atualizaUsuario($resultCheckToken->id_usuario, $data);
					$this->redefinicao_model->validaToken($token, true);
					$this->session->set_flashdata('sucesso', 'Senha alterada com sucesso!');
					redirect('mxcode/login');
				}
			}
		}
		
		$this->session->set_flashdata(
			'erro',
			'Link de redefinição de senha expirado. Solicite um novo link clicando no botão <a href="javascript:" onclick="recuperar_senha()">Esqueci minha senha</a>.');
		redirect('mxcode/login');
		
	}
	
	public function invalidaToken()
	{
		if ($this->session->userdata('logado')) {
			redirect('mxcode/login');
		}
		
		$id = (int)$this->input->post('id');
		$this->redefinicao_model->invalidaToken($id);
		redirect('mxcode/login');
	}
	
	public function validaToken()
	{
		if ($this->session->userdata('logado')) {
			redirect('mxcode/login');
		}
		
		$id = (int)$this->input->post('id');
		$this->redefinicao_model->validaToken($id);
		redirect('mxcode/login');
	}
	
}
