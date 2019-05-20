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
        $this->load->helper(array('codegen_helper'));
        $this->id_usuario = $this->session->userdata('id');
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
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar pendências.');
            redirect(base_url());
        }
        $where = '';
        $periodo = $this->input->get('periodo');
        $cliente = $this->input->get('id_cliente');

        // busca todos os lançamentos
        if ($periodo == 'todos') {


        } else {

            // busca lançamentos do dia
            if ($periodo == '7dias') {
                $semana = $this->getLastSevenDays();

                $where = 'data_pendencia BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

            } else if ($periodo == null) {
                $limit = 5;
            } else {

                // busca lançamentos da semana
                if ($periodo == '5dias') {
                    $semana = $this->getLastFiveDays();
                    $where = 'data_pendencia BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                } else {

                    // busca lançamentos da semana
                    if ($periodo == '3dias') {
                        $semana = $this->getLastThreeDays();
                        $where = 'data_pendencia BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                    } else {
                        // busca lançamentos da semana
                        if ($periodo == '15dias') {
                            $semana = $this->getLastFifteenDays();
                            $where = 'data_pendencia BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                        } else {

                            // busca lançamentos da semana
                            if ($periodo == '30dias') {
                                $semana = $this->getLastTirthyDays();
                                $where = 'data_pendencia BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                            } else {

                                // busca lançamentos da semana
                                if ($periodo == '60dias') {
                                    $semana = $this->getLastSixtyDays();
                                    $where = 'data_pendencia BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                                } else {

                                    // busca lançamentos da semana
                                    if ($periodo == '90dias') {
                                        $semana = $this->getLastNinetyDays();
                                        $where = 'data_pendencia BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->load->library('pagination');

        $config['base_url'] = site_url() . '/financeiro/pendencias/?periodo=' . $periodo;
        $config['total_rows'] = $this->pendencia_model->count('pendencias', 'status = 1 AND id_usuario = ' . $this->id_usuario);
        $config['per_page'] = 100;
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

        if ($cliente) {
            if ($where == '') {
                $where .= 'id_cliente = ' . $cliente;
            } else {
                $where .= ' AND id_cliente = ' . $cliente;
            }
        }

        $this->pagination->initialize($config);

        $this->data['results'] = $this->pendencia_model->get(
            'pendencias',
            '*',
            $where,
            $this->id_usuario,
            $limit,
            $config['total_rows'],
            $config['per_page'],
            $this->input->get('per_page'));

        $this->data['clientes'] = $this->pendencia_model->getClientes($this->id_usuario);
        $this->data['formasPagamento'] = $this->financeiro_model->getFormasPagamento();
        $this->data['selected'] = $cliente;

        $this->data['pendencias_credito'] = $this->pendencia_model->getPendenciasParcialCredito($this->id_usuario, $cliente, $where);
        $this->data['pendencias_debito'] = $this->pendencia_model->getPendenciasParcialDebito($this->id_usuario, $cliente, $where);
        $this->data['total_credito'] = $this->pendencia_model->getPendenciasTotalCredito($this->id_usuario);
        $this->data['total_debito'] = $this->pendencia_model->getPendenciasTotalDebito($this->id_usuario);
//        $this->data['total'] = $this->pendencia_model->getTotal($this->id_usuario);


        $this->data['view'] = 'financeiro/pendencias';
        $this->load->view('tema/topo', $this->data);

    }

    function adicionar()
    {

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aPendencias')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar pendências.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');

        $valor = $this->input->post('valor');
        $tipo = $this->input->post('tipo');
        $data_pendencia = $this->input->post('data_pendencia');

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

        if($tipo == 2) {
            $valor = '-' . $valor;
        }

        $data = array(
            'id_usuario' => $this->id_usuario,
            'id_cliente' => $this->input->post('id_cliente'),
            'descricao' => padronizarString($this->input->post('descricao')),
            'tipo' => $tipo,
            'valor' => $valor,
            'data_pendencia' => $data_pendencia,
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
            $data_pendencia = $this->input->post('data_pendencia');

            if (!validate_money($valor)) {
                $valor = str_replace(array('.', ','), array('', '.'), $valor);
            }

            if ($data_pendencia != null) {
                $data_pendencia = explode('/', $data_pendencia);
                $data_pendencia = $data_pendencia[2] . '-' . $data_pendencia[1] . '-' . $data_pendencia[0];
            } else {
                $data_pendencia = date('Y-m-d');
            }

            if($tipo == 2) {
                $valor = '-' . $valor;
            }

            $data = array(
                'id_cliente' => $this->input->post('id_cliente'),
                'descricao' => padronizarString($this->input->post('descricao')),
                'tipo' => $tipo,
                'valor' => $valor,
                'data_pendencia' => $data_pendencia,
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
            $this->pendencia_model->edit('pendencias', $data, 'id_pendencia', $id);

            $pendencia = $this->pendencia_model->getById($id);
            $cliente = $this->clientes_model->getById($pendencia->id_cliente);

            $consulta = array(
                'id_usuario' => $pendencia->id_usuario,
                'descricao' => $pendencia->descricao,
                'valor' => $pendencia->valor,
                'data_lancamento' => $pendencia->data_pendencia,
                'data_pagamento' => $pendencia->data_pagamento,
                'cliente_fornecedor' => $cliente->nomeCliente,
                'forma_pgto' => $_REQUEST['forma_pagamento'],
                'tipo' => $pendencia->tipo,
                'baixado' => 1,
            );

            if ($this->financeiro_model->add('lancamentos', $consulta) == true) {
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
}
