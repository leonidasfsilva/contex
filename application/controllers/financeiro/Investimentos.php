<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Investimentos extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $this->load->model('investimentos_model', '', true);
        $this->load->model('financeiro_model', '', true);
        $this->load->model('pendencia_model', '', true);
        $this->load->model('fatura_model', '', true);
        $this->load->model('clientes_model', '', true);
        $this->data['menuFinanceiro'] = 'investimentos';
        $this->global_url = site_url() . 'financeiro/investimentos/';

    }

    public function index()
    {
        $this->investimentos();
    }

    public function investimentos()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vInvestimentos')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar investimentos.');
            redirect(base_url());
        }

        $status     = $_GET['status'] ?? null;
        $tipo       = $_GET['tipo'] ?? null;
        $periodo    = $_GET['periodo'] ?? null;
        $inicio     = $_GET['dataInicial'] ?? null;
        $fim        = $_GET['dataFinal'] ?? null;
        $start      = $_GET['per_page'] ?? null;
        $where      = null;
        $limit      = null;


        switch ($periodo) {
            case 'todos':
                $limit = null;
                break;
            case '3dias':
                $semana = $this->getLastThreeDays();
                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '5dias':
                $semana = $this->getLastFiveDays();
                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '7dias':
                $semana = $this->getLastSevenDays();
                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '15dias':
                $semana = $this->getLastFifteenDays();
                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '30dias':
                $semana = $this->getLastTirthyDays();
                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '60dias':
                $semana = $this->getLastSixtyDays();
                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '90dias':
                $semana = $this->getLastNinetyDays();
                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            default:
                if ($this->financeiro_model->countLancamentos(getUserId()) > 20) {
                    $limit = 20;
                    $start = $this->financeiro_model->countLancamentos(getUserId()) - $limit;
                }
                $order_by = [
                    'data_lancamento' => 'desc',
                    'id_lancamento' => 'desc',
                ];
                $limitado = true;
                break;
        }

        $this->load->library('pagination');

        $config['base_url']             = site_url() . 'financeiro/investimentos/?periodo=' . $periodo . '&situacao=' . $status;
        $config['total_rows']           = $this->investimentos_model->count('investimentos', 'status = 1 AND id_usuario = ' . getUserId());
        $config['per_page']             = 100;
        $config['page_query_string']    = true;
        $config['next_link']            = 'Próxima';
        $config['prev_link']            = 'Anterior';
        $config['full_tag_open']        = '<ul class="pagination pagination-sm">';
        $config['full_tag_close']       = '</ul>';
        $config['num_tag_open']         = '<li>';
        $config['num_tag_close']        = '</li>';
        $config['cur_tag_open']         = '<li><a style="background-color: #337ab7; color: white"><b>';
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

        $this->data['total_entradas']       = $this->investimentos_model->getTotalEntradas(getUserId());
        $this->data['saidas_pendentes']     = $this->investimentos_model->getSaidasPendentes(getUserId());
        $this->data['entradas_pendentes']   = $this->investimentos_model->getEntradasPendentes(getUserId());
        $this->data['total']                = $this->investimentos_model->getTotal(getUserId());
        $this->data['formasPagamento']      = $this->financeiro_model->getFormasPagamento();
        $this->data['results']              = $this->investimentos_model->get(
            'investimentos',
            '*',
            $where,
            getUserId(),
            $limit,
            $config['total_rows'],
            $config['per_page'],
            $this->input->get('per_page'),
            $order_by ? $order_by : 'desc'
        );

        $this->data['view'] = 'financeiro/investimentos';
        $this->load->view('tema/topo', $this->data);

    }

    function aplicacao()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aInvestimentos')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para adicionar lançamentos.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');
        $vencimento = $this->input->post('vencimento');

        if ($vencimento != null) {
            $vencimento = explode('/', $vencimento);
            $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
        } else {
            $vencimento = date('Y-m-d');
        }

        if (!validate_money($this->input->post('valor'))) {
            $valor = str_replace(array('.', ','), array('', '.'), $this->input->post('valor'));
        }
        $valor_corrente = '-' . $valor;


        if ($this->input->post('descricao')) {
            $descricao = padronizarString($this->input->post('descricao'));
        } else {
            $descricao = 'APLICACAO EM INVESTIMENTOS';
        }

        $data = array(
            'descricao' => $descricao,
            'valor' => $valor,
            'id_usuario' => getUserId(),
            'data_lancamento' => $vencimento,
            'forma_pgto' => ($this->input->post('formaPgto')),
            'tipo' => 1
        );

        if ($this->investimentos_model->add('investimentos', $data) == true) {
            if ($this->input->post('debito_conta')) {
                $data2 = array(
                    'descricao' => $descricao,
                    'cliente_fornecedor' => padronizarString($this->session->userdata('nome')),
                    'valor' => $valor_corrente,
                    'id_usuario' => getUserId(),
                    'data_lancamento' => $vencimento,
                    'data_pagamento' => $vencimento,
                    'baixado' => 1,
                    'forma_pgto' => ($this->input->post('formaPgto')),
                    'tipo' => 2
                );
                $this->financeiro_model->add('lancamentos', $data2);
            }
            $this->session->set_flashdata('sucesso', 'Aplicação registrada com sucesso!');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar registrar aplicação.');
            redirect($urlAtual);
        }

        $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar registrar aplicação.');
        redirect($urlAtual);

    }

    function resgate()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aInvestimentos')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para adicionar lançamentos.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');
        $vencimento = $this->input->post('vencimento');

        if ($vencimento != null) {
            $vencimento = explode('/', $vencimento);
            $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
        } else {
            $vencimento = date('Y-m-d');
        }

        if (!validate_money($this->input->post('valor'))) {
            $valor = str_replace(array('.', ','), array('', '.'), $this->input->post('valor'));
        }
        $valor_investimentos = '-' . $valor;


        if ($this->input->post('descricao')) {
            $descricao = padronizarString($this->input->post('descricao'));
        } else {
            $descricao = 'RESGATE DE INVESTIMENTOS';
        }

        $data = array(
            'descricao' => $descricao,
            'valor' => $valor_investimentos,
            'id_usuario' => getUserId(),
            'data_lancamento' => $vencimento,
            'forma_pgto' => ($this->input->post('formaPgto')),
            'tipo' => 2
        );

        if ($this->financeiro_model->add('investimentos', $data) == true) {
            if ($this->input->post('debito_conta')) {
                $data2 = array(
                    'descricao' => $descricao,
                    'cliente_fornecedor' => padronizarString($this->session->userdata('nome')),
                    'valor' => $valor,
                    'id_usuario' => getUserId(),
                    'data_lancamento' => $vencimento,
                    'data_pagamento' => $vencimento,
                    'baixado' => 1,
                    'forma_pgto' => ($this->input->post('formaPgto')),
                    'tipo' => 1
                );
                $this->financeiro_model->add('lancamentos', $data2);
            }

            $this->session->set_flashdata('sucesso', 'Resgate registrado com sucesso!');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar registrar resgate.');
            redirect($urlAtual);
        }

        $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar registrar resgate.');
        redirect($urlAtual);


    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eInvestimentos')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para editar lançamentos.');
            redirect(base_url());
        }

        if ($this->input->post('id')) {

            $urlAtual = $this->input->post('urlAtual');
            $vencimento = $this->input->post('vencimento');

            if ($vencimento != null) {
                $vencimento = explode('/', $vencimento);
                $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
            } else {
                $vencimento = date('Y-m-d');
            }

            $tipo = ($this->input->post('tipo'));
            $valor = $this->input->post('valor');

            if ($tipo == 2) {
                if ($this->input->post('descricao')) {
                    $descricao = padronizarString($this->input->post('descricao'));
                } else {
                    $descricao = 'RESGATE DE INVESTIMENTOS';
                }
                $valor = '-' . $valor;
            } elseif ($tipo == 1) {
                if ($this->input->post('descricao')) {
                    $descricao = padronizarString($this->input->post('descricao'));
                } else {
                    $descricao = 'APLICAÇÃO EM INVESTIMENTOS';
                }
            }

            $valor = str_replace(array('.', ','), array('', '.'), $valor);

            $data = array(
                'descricao' => $descricao,
                'valor' => $valor,
                'data_lancamento' => $vencimento,
                'forma_pgto' => ($this->input->post('formaPgto')),
                'tipo' => $tipo
            );

            if ($this->investimentos_model->edit('investimentos', $data, 'id_lancamentos', $this->input->post('id')) == true) {
                $this->session->set_flashdata('sucesso', 'Lançamento editado com sucesso!');
                redirect($urlAtual);
            } else {
                $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar editar este lançamento.');
                redirect($urlAtual);
            }

            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar editar este lançamento.');
            redirect($urlAtual);

        } else {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect($this->global_url);

        }

    }

    public function excluir()
    {

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dInvestimentos')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para excluir lançamentos.');
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

            if ($this->financeiro_model->delete('investimentos', $data, 'id_lancamentos', $id) == true) {
                $this->session->set_flashdata('sucesso', 'Lançamento excluído com sucesso!');
                redirect($urlAtual);

            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar excluir lançamento.');
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
