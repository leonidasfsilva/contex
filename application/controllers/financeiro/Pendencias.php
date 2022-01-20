<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pendencias extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $this->load->model('financeiro_model', '', true);
        $this->load->model('pendencia_model', '', true);
        $this->load->model('fatura_model', '', true);
        $this->load->model('clientes_model', '', true);
        $this->data['menuFinanceiro'] = 'Lancamentos';
        $this->global_url = site_url() . '/financeiro/pendencias/';
    }

    public function index()
    {
        $this->pendencias();
    }

    //MODULO DE PENDENCIAS
    public function pendencias()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vPendencias')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para visualizar pendências.');
            redirect(base_url());
        }
        $periodo = $this->input->get('periodo');
        $status = $this->input->get('status');
        $tipo = $this->input->get('tipo');
        $cliente = $this->input->get('cliente');
        $inicio = $this->input->get('dataInicial');
        $fim = $this->input->get('dataFinal');


        $this->load->library('pagination');

        $config['base_url'] = site_url() . '/financeiro/pendencias/?periodo=' . $periodo;
        $config['total_rows'] = $this->pendencia_model->count('pendencias', 'status = 1 AND id_usuario = ' . getUserId());
        $config['per_page'] = null;
        $config['page_query_string'] = true;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $limit = $this->pendencia_model->count('pendencias', 'status = 1 AND quitado = 0 AND id_usuario = ' . getUserId());

        switch ($periodo) {
            case 'todos':
                $limit = null;
                break;
            case '3dias':
                $semana = $this->getLastThreeDays();
                $where = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '5dias':
                $semana = $this->getLastFiveDays();
                $where = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '7dias':
                $semana = $this->getLastSevenDays();
                $where = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '15dias':
                $semana = $this->getLastFifteenDays();
                $where = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '30dias':
                $semana = $this->getLastTirthyDays();
                $where = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '60dias':
                $semana = $this->getLastSixtyDays();
                $where = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '90dias':
                $semana = $this->getLastNinetyDays();
                $where = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
        }

        if (isset($status) && $status != null) {
            $limit = null;
            if ($status == 'pendente') {
                if (!isset($where)) {
                    $where = 'quitado = 0';
                } else {
                    $where .= ' AND quitado = 0';
                }
            } elseif ($status == 'pago') {
                if (!isset($where)) {
                    $where = 'quitado = 1';
                } else {
                    $where .= ' AND quitado = 1';
                }
            }
        }

        if (isset($tipo) && $tipo != null) {
            if ($tipo == 'credito') {
                if (!isset($where)) {
                    $where = 'tipo = 1';
                } else {
                    $where .= ' AND tipo = 1';
                }
            } elseif ($tipo == 'debito') {
                if (!isset($where)) {
                    $where = 'tipo = 2';
                } else {
                    $where .= ' AND tipo = 2';
                }
            }
        }

        if (isset($periodo) && $periodo == 'todos') {
            $limit = null;
        }

        if (isset($cliente) && $cliente != null) {
            $limit = null;
            if (!isset($where)) {
                $where = 'id_cliente = ' . $cliente;
            } else {
                $where .= ' AND id_cliente = ' . $cliente;
            }
        }

        if (isset($inicio) && isset($fim) && $inicio != null && $fim != null) {
            $inicio = explode('/', $inicio);
            $inicio = $inicio[2] . '-' . $inicio[1] . '-' . $inicio[0];

            $fim = explode('/', $fim);
            $fim = $fim[2] . '-' . $fim[1] . '-' . $fim[0];

            $limit = null;
            if (!isset($where)) {
                $where = 'data_vencimento BETWEEN "' . $inicio . '" AND "' . $fim . '"';
            } else {
                $where .= ' AND data_vencimento BETWEEN "' . $inicio . '" AND "' . $fim . '"';
            }
        }

        $this->pagination->initialize($config);

        $this->data['results'] = $this->pendencia_model->get(
            'pendencias',
            '*',
            $where,
            getUserId(),
            $limit,
            $config['total_rows'],
            $config['per_page'],
            $this->input->get('per_page')
        );

        $this->data['clientes'] = $this->pendencia_model->getClientes(getUserId());
        $this->data['formasPagamento'] = $this->financeiro_model->getFormasPagamento();
        $this->data['selected_cliente'] = $cliente;

        $this->data['pendencias_credito'] = $this->pendencia_model->getPendenciasParcialCredito(getUserId(), $cliente, $where);
        $this->data['pendencias_debito'] = $this->pendencia_model->getPendenciasParcialDebito(getUserId(), $cliente, $where);
        $this->data['total_credito'] = $this->pendencia_model->getPendenciasTotalCredito(getUserId());
        $this->data['total_debito'] = $this->pendencia_model->getPendenciasTotalDebito(getUserId());
        // $this->data['total'] = $this->pendencia_model->getTotal(getUserId());


        $this->data['view'] = 'financeiro/pendencias';
        $this->load->view('tema/topo', $this->data);
    }

    public function adicionar()
    {

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aPendencias')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar pendências.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');

        $valor = $this->input->post('valor');
        $tipo = $this->input->post('tipo');
        $data_pendencia = $this->input->post('data_vencimento');

        if (!validate_money($valor)) {
            $valor = str_replace(array('.', ','), array('', '.'), $valor);
        }

        if ($data_pendencia == null) {
            $data_pendencia = date('d/m/Y');
        }

        try {

            $data_pendencia = explode('/', $data_pendencia);
            $data_pendencia = $data_pendencia[2] . '-' . $data_pendencia[1] . '-' . $data_pendencia[0];
        } catch (Exception $e) {
            $data_pendencia = date('Y/m/d');
        }

        if ($tipo == 2) {
            $valor = '-' . $valor;
        }

        $data = array(
            'id_usuario' => getUserId(),
            'id_cliente' => $this->input->post('id_cliente'),
            'descricao' => padronizarString($this->input->post('descricao')),
            'tipo' => $tipo,
            'valor' => $valor,
            'data_vencimento' => $data_pendencia,
        );

        if ($this->pendencia_model->add('pendencias', $data) == true) {
            $this->session->set_flashdata('sucesso', 'Pendência adicionada com sucesso!');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar adicionar pendência');
            redirect($urlAtual);
        }
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'ePendencias')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar pendências.');
            redirect(base_url());
        }

        if ($this->input->post('id_cliente')) {

            $urlAtual = $this->input->post('urlAtual');

            $valor = $this->input->post('valor');
            $tipo = $this->input->post('tipo');
            $data_pendencia = $this->input->post('data_vencimento');

            if (!validate_money($valor)) {
                $valor = str_replace(array('.', ','), array('', '.'), $valor);
            }

            if ($data_pendencia != null) {
                $data_pendencia = explode('/', $data_pendencia);
                $data_pendencia = $data_pendencia[2] . '-' . $data_pendencia[1] . '-' . $data_pendencia[0];
            } else {
                $data_pendencia = date('Y-m-d');
            }

            if ($tipo == 2) {
                $valor = '-' . $valor;
            }

            $data = array(
                'id_cliente' => $this->input->post('id_cliente'),
                'descricao' => padronizarString($this->input->post('descricao')),
                'tipo' => $tipo,
                'valor' => $valor,
                'data_vencimento' => $data_pendencia,
            );

            if ($this->pendencia_model->edit('pendencias', $data, 'id_pendencia', $this->input->post('id_pendencia')) == true) {
                $this->session->set_flashdata('sucesso', 'Pendência alterada com sucesso!');
                redirect($urlAtual);
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar editar pendência');
                redirect($urlAtual);
            }
        } else {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect($this->global_url);
        }
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dPendencias')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para excluir pendências.');
            redirect($this->global_url);
        }

        $id = $this->input->post('id');
        $urlAtual = $this->input->post('urlAtual');

        if ($id == null || !is_numeric($id)) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect($this->global_url);
        } else {

            $data = array(
                'status' => 0
            );

            if ($this->pendencia_model->delete('pendencias', $data, 'id_pendencia', $id) == true) {
                $this->session->set_flashdata('sucesso', 'Pendência excluída com sucesso!');
                redirect($urlAtual);
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar excluir pendência.');
                redirect($urlAtual);
            }
        }
    }

    public function pagar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'ePendencias')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para pagar pendências.');
            redirect(base_url());
        }

        $urlAtual = $this->input->post('urlAtual');
        $id = $this->input->post('id_pendencia');

        if ($id == null || !is_numeric($id)) {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar adicionar pendência: (ID null)');
            redirect($this->global_url);
        } else {

            if ($_REQUEST['data_pagamento']) {
                $data_pagamento = $_REQUEST['data_pagamento'];
                $data_pagamento = explode('/', $data_pagamento);
                $data_pagamento = $data_pagamento[2] . '-' . $data_pagamento[1] . '-' . $data_pagamento[0];
            } else {
                $data_pagamento = date('Y/m/d');
            }

            $data = array(
                'quitado' => 1,
                'data_pagamento' => $data_pagamento,
            );
            $result = $this->pendencia_model->edit('pendencias', $data, 'id_pendencia', $id);

            $pendencia = $this->pendencia_model->getById($id);
            $cliente = $this->clientes_model->getById($pendencia->id_cliente);

            $consulta = array(
                'id_usuario' => $pendencia->id_usuario,
                'descricao' => $pendencia->descricao,
                'valor' => $pendencia->valor,
                'data_lancamento' => $pendencia->data_vencimento,
                'data_pagamento' => $pendencia->data_pagamento,
                'cliente_fornecedor' => $cliente->nome,
                'forma_pgto' => $_REQUEST['forma_pagamento'],
                'tipo' => $pendencia->tipo,
                'baixado' => 1,
            );

            if ($_POST['registrar']) {
                $this->financeiro_model->add('lancamentos', $consulta);
            }

            if ($result) {
                $this->session->set_flashdata('sucesso', 'Pendência paga com sucesso!');
                redirect($urlAtual);
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar pagar pendência');
                redirect($urlAtual);
            }
        }
    }

    //MODULO DE TESTES
    public function getTeste($id = null)
    {

        $pendencia = $this->pendencia_model->getById($id);
        $cliente = $this->clientes_model->getById($pendencia->id_cliente);

        print_array($pendencia);
        print_array($cliente);
        echo 'TESTE OK!';
    }

    //MODULO DE RETORNO DE FILTROS POR PERIODO
    protected function getThisYear()
    {

        $dias = date("z");
        $primeiro = date("Y-m-d", strtotime("-" . ($dias) . " day"));
        $ultimo = date("Y-m-d", strtotime("+" . (364 - $dias) . " day"));
        return array($primeiro, $ultimo);
    }

    protected function getThisWeek()
    {

        return array(date("Y/m/d", strtotime("last sunday", strtotime("now"))), date("Y/m/d", strtotime("next saturday", strtotime("now"))));
    }

    protected function getLastThreeDays()
    {

        return array(date("Y-m-d", strtotime("-3 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastFiveDays()
    {

        return array(date("Y-m-d", strtotime("-5 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastSevenDays()
    {

        return array(date("Y-m-d", strtotime("-7 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastFifteenDays()
    {

        return array(date("Y-m-d", strtotime("-15 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastTirthyDays()
    {

        return array(date("Y-m-d", strtotime("-30 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastSixtyDays()
    {

        return array(date("Y-m-d", strtotime("-60 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastNinetyDays()
    {

        return array(date("Y-m-d", strtotime("-90 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getThisMonth()
    {

        $mes = date('m');
        $ano = date('Y');
        $qtdDiasMes = date('t');
        $inicia = $ano . "-" . $mes . "-01";

        $ate = $ano . "-" . $mes . "-" . $qtdDiasMes;
        return array($inicia, $ate);
    }

    public function autoCompleteCliente()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->clientes_model->autoCompleteCliente($q, getUserId());
        }
    }

    public function getClientName() {
        $clientId = $_POST['clientId'] ?? null;
        $json = [
            'success' => false
        ];
        
        if ($clientId) {
            $result = $this->clientes_model->getClientName($clientId);
            if ($result) {
                $json = [
                    'success' => true,
                    'data' => $result
                ];
            }
        }
        echo json_encode($json, JSON_PRETTY_PRINT);
    }
}
