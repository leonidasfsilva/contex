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
			'SISTEMA',
			'USUÁRIOS',
			'VENDAS',
		);
		
		// $data['maintenanceMode']   = $this->load->get_var('maintenanceMode');
		$data['menuConfiguracoes'] = true;
		$data['results']           = $this->configs_model->getConfigs();
		$data['view']              = 'configuracoes/sistema';
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
		$data['dados']             = $this->configs_model->getConfigsUsuario(getUserId());
		$data['view']              = 'configuracoes/usuario';
		$this->load->view('tema/topo', $data);
	}
	
	public function logs()
	{
		if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
			$this->session->set_flashdata('erro', 'Você não tem permissão para visualizar logs do sistema');
			redirect(base_url());
		}
		$this->load->library('pagination');
		
		$config['base_url']          = base_url('configuracoes/logs/');
		$config['suffix']            = '#logs';
		$config['first_url']         = $config['base_url'] . '#logs';
		$config['total_rows']        = $this->usuarios_model->countLogs();
		$config['per_page']          = 40;
		$config['page_query_string'] = true;
		$config['next_link']         = false;
		$config['prev_link']         = false;
		$config['full_tag_open']     = '<ul class="pagination pagination-sm">';
		$config['full_tag_close']    = '</ul>';
		$config['num_tag_open']      = '<li>';
		$config['num_tag_close']     = '</li>';
		$config['cur_tag_open']      = '<li class="disabled"><a style="background-color:#337ab7; color: white" class="js:"><b>';
		$config['cur_tag_close']     = '</b></a></li>';
		$config['prev_tag_open']     = '<li>';
		$config['prev_tag_close']    = '</li>';
		$config['next_tag_open']     = '<li>';
		$config['next_tag_close']    = '</li>';
		$config['first_link']        = 'Primeira';
		$config['last_link']         = 'Última';
		$config['first_tag_open']    = '<li>';
		$config['first_tag_close']   = '</li>';
		$config['last_tag_open']     = '<li>';
		$config['last_tag_close']    = '</li>';
		
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
			'setor'     => $_POST['setor'],
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
			'setor'     => $_POST['setor'],
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
		
		$id   = $this->input->post('id');
		$data = [
			'status' => 0
		];
		
		if ($this->configs_model->delete('configs_opcoes', $data, 'id', $id)) {
			$this->session->set_flashdata('sucesso', 'Opção excluída com sucesso!');
			redirect(base_url('configuracoes/sistema'));
		}
		
		$this->session->set_flashdata('erro', 'Erro ao tentar excluir opção!');
		redirect(base_url('configuracoes/sistema'));
	}
	
	function desativar()
	{
		$id = $this->input->post('id');
		
		$data = [
			'ativo' => 0
		];
		
		if ($this->configs_model->delete('configs_opcoes', $data, 'id', $id)) {
			$this->session->set_flashdata('sucesso', 'Opção desativada com sucesso!');
			redirect(base_url('configuracoes/sistema'));
		}
		
		$this->session->set_flashdata('erro', 'Erro ao tentar desativar opção do sistema.');
		redirect(base_url('configuracoes/sistema'));
	}
	
	function ativar()
	{
		$id = $this->input->post('id');
		
		$data = array(
			'ativo' => 1
		);
		
		if ($this->configs_model->delete('configs_opcoes', $data, 'id', $id)) {
			$this->session->set_flashdata('sucesso', 'Opção ativada com sucesso!');
			redirect(base_url('configuracoes/sistema'));
		}
		
		$this->session->set_flashdata('erro', 'Erro ao tentar ativar opção do sistema.');
		redirect(base_url('configuracoes/sistema'));
	}
	
	public function pesquisar()
	{
		if ((!session_id()) || (!$this->session->userdata('logado'))) {
			redirect('mxcode/login');
		}
		
		$termo = $this->input->get('termo');
		
		$data['results']        = $this->mxcode_model->pesquisar($termo, getUserId());
		$this->data['produtos'] = $data['results']['produtos'];
		$this->data['servicos'] = $data['results']['servicos'];
		$this->data['os']       = $data['results']['os'];
		$this->data['clientes'] = $data['results']['clientes'];
		$this->data['view']     = 'mxcode/pesquisa';
		$this->load->view('tema/topo', $this->data);
	}
	
	public function login()
	{
		if (($this->session->userdata('logado'))) {
			redirect($this->index());
		}
		$this->load->view('mxcode/login');
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
			'format'             => 'zip',
			'foreign_key_checks' => false,
			'filename'           => 'backup' . date('d-m-Y') . '.sql',
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
		$data['dados']             = $this->mxcode_model->getEmitente(getUserId());
		$data['view']              = 'mxcode/emitente';
		$this->load->view('tema/topo', $data);
	}
	
	public function autocomplete()
	{
		if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
			$this->session->set_flashdata('erro', 'Você não tem permissão para modificar termos de autocomplete.');
			redirect(base_url());
		}
		
		if (!$_POST) {
			$this->session->set_flashdata('erro', 'Método não permitido.');
			redirect(base_url('configuracoes/sistema'));
		}
		
		if ($_POST['modulo'] == 'faturas') {
			$descricao     = padronizarString($_POST['descricao']);
			$descricao_alt = padronizarString($_POST['descricao_alt']);
			$terceiro      = padronizarString($_POST['terceiro']);
			$terceiro_alt  = padronizarString($_POST['terceiro_alt']);
			
			if (!$descricao || !$descricao_alt) {
				$this->session->set_flashdata('erro', 'Alteração não realizada');
				redirect(base_url('configuracoes/sistema'));
			}
			
			$data = [
				'descricao' => $descricao_alt
			];
			
			if ($this->fatura_model->atualizaDescricao($descricao, $data)) {
				$this->session->set_flashdata('sucesso', 'Alteração efetuada com sucesso!');
				redirect(base_url('configuracoes/sistema'));
			}
		}
		$this->session->set_flashdata('erro', 'Erro ao tentar efetuar alterações!');
		redirect(base_url() . 'configuracoes/sistema');
		
	}
	
	public function setWidgetLancamentos()
	{
		if ($_POST['value'] == 1) {
			$this->configs_model->setWidgetLancamentos(getUserId());
			return;
		}
		$this->configs_model->unsetWidgetLancamentos(getUserId());
	}
	
	public function setWidgetCartaoCredito()
	{
		if ($_POST['value'] == 1) {
			$this->configs_model->setWidgetCartaoCredito(getUserId());
			return;
		}
		$this->configs_model->unsetWidgetCartaoCredito(getUserId());
	}
	
	public function setWidgetInvestimentos()
	{
		if ($_POST['value'] == 1) {
			$this->configs_model->setWidgetInvestimentos(getUserId());
			return;
		}
		$this->configs_model->unsetWidgetInvestimentos(getUserId());
	}
	
	public function setWidgetPendencias()
	{
		if ($_POST['value'] == 1) {
			$this->configs_model->setWidgetPendencias(getUserId());
			return;
		}
		$this->configs_model->unsetWidgetPendencias(getUserId());
	}
	
	public function activateMaintenanceMode()
	{
		if ($_POST['maintenanceMode'] == 1) {
			$this->configs_model->activateMaintenanceMode(getUserId());
			$this->session->set_flashdata('sucesso', 'Modo Manutenção ativado!');
			echo true;
		}
		$this->session->set_flashdata('erro', 'Não foi possível ativar o Modo Manutenção');
		echo false;
	}
	
	public function deactivateMaintenanceMode()
	{
		if ($_POST['maintenanceMode'] == 0) {
			if (checkForcedLogout()) {
				$this->configs_model->deactivateForcedLogout();
			}
			$this->configs_model->deactivateMaintenanceMode();
			$this->session->set_flashdata('sucesso', 'Modo manutenção desativado!');
			echo true;
		}
		$this->session->set_flashdata('erro', 'Não foi possível desativar o modo manutenção');
		echo false;
	}
	
	public function activateForcedLogout()
	{
		if ($_POST['forceLogout'] == 1) {
			if (!checkMaintenanceMode()) {
				$this->configs_model->activateMaintenanceMode(getUserId());
			}
			$this->configs_model->activateForcedLogout(getUserId());
			$this->session->set_flashdata('sucesso', 'Logout força bruta ativado!');
			echo true;
		}
		$this->session->set_flashdata('erro', 'Não foi possível ativar o logout força bruta');
		echo false;
	}
	
	public function deactivateForcedLogout()
	{
		if ($_POST['forceLogout'] == 0) {
			$this->configs_model->deactivateForcedLogout();
			$this->session->set_flashdata('sucesso', 'Logout força bruta desativado!');
			echo true;
		}
		$this->session->set_flashdata('erro', 'Não foi possível desativar o logout força bruta');
		echo false;
	}
}
