<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Lancamentos extends CI_Controller
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
        $this->global_url = site_url() . 'financeiro/lancamentos/';

    }

    public function index()
    {
        $this->lancamentos();
    }

    //MODULO DE LANCAMENTOS
    public function lancamentos()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar lançamentos.');
            redirect(base_url());
        }
        $status = $this->input->get('status');
        $tipo = $this->input->get('tipo');
        $periodo = $this->input->get('periodo');
        $inicio = $this->input->get('dataInicial');
        $fim = $this->input->get('dataFinal');

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
                $limit = 10;
                break;
        }

        if (isset($status) && $status != null) {
            if ($status == 'pendente') {
                if (!isset($where)) {
                    $where = 'baixado = 0';
                } else {
                    $where .= ' AND baixado = 0';
                }
            } elseif ($status == 'efetivado') {
                if (!isset($where)) {
                    $where = 'baixado = 1';
                } else {
                    $where .= ' AND baixado = 1';
                }
            }
        }

        if (isset($tipo) && $tipo != null) {
            if ($tipo == 'entrada') {
                if (!isset($where)) {
                    $where = 'tipo = 1';
                } else {
                    $where .= ' AND tipo = 1';
                }
            } elseif ($tipo == 'saida') {
                if (!isset($where)) {
                    $where = 'tipo = 2';
                } else {
                    $where .= ' AND tipo = 2';
                }
            }
        }

        if (isset($inicio) && isset($fim) && $inicio != null && $fim != null) {
            $inicio = explode('/', $inicio);
            $inicio = $inicio[2] . '-' . $inicio[1] . '-' . $inicio[0];

            $fim = explode('/', $fim);
            $fim = $fim[2] . '-' . $fim[1] . '-' . $fim[0];

            $limit = null;
            if (!isset($where)) {
                $where = 'data_lancamento BETWEEN "' . $inicio . '" AND "' . $fim . '"';
            } else {
                $where .= ' AND data_lancamento BETWEEN "' . $inicio . '" AND "' . $fim . '"';
            }
        }

        $this->load->library('pagination');

        $config['base_url'] = site_url() . 'financeiro/lancamentos/';
        $config['total_rows'] = $this->financeiro_model->count(
            'lancamentos',
            $where,
            id_usuario());
        $config['per_page'] = null;
        $config['page_query_string'] = true;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="background-color: #337ab7; color: white"><b>';
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

        $this->pagination->initialize($config);

        $this->data['total_entradas'] = $this->financeiro_model->getTotalEntradas(id_usuario());
        $this->data['saidas_pendentes'] = $this->financeiro_model->getSaidasPendentes(id_usuario());
        $this->data['entradas_pendentes'] = $this->financeiro_model->getEntradasPendentes(id_usuario());
        $this->data['total'] = $this->financeiro_model->getTotal(id_usuario());
        $this->data['formasPagamento'] = $this->financeiro_model->getFormasPagamento();
        $this->data['results'] = $this->financeiro_model->get(
            'lancamentos',
            '*',
            $where,
            id_usuario(),
            $limit,
            $config['total_rows'],
            $config['per_page'],
            $this->input->get('per_page'));

        $this->data['view'] = 'financeiro/lancamentos';
        $this->load->view('tema/topo', $this->data);

    }

    function receita()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para adicionar lançamentos.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');

        $vencimento = $this->input->post('vencimento');
        $recebimento = $this->input->post('recebimento');

        if ($vencimento != null) {
            $vencimento = explode('/', $vencimento);
            $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
        } else {
            $vencimento = date('Y-m-d');
        }

        if ($recebimento != null) {
            $recebimento = explode('/', $recebimento);
            $recebimento = $recebimento[2] . '-' . $recebimento[1] . '-' . $recebimento[0];
        }

        $valor = $this->input->post('valor');

        if (!validate_money($valor)) {
            $valor = str_replace(array('.', ','), array('', '.'), $valor);
        }

        $data = array(
            'descricao' => padronizarString($this->input->post('descricao')),
            'valor' => $valor,
            'id_usuario' => id_usuario(),
            'data_lancamento' => $vencimento,
            'data_pagamento' => $recebimento != null ? $recebimento : $vencimento,
            'baixado' => $this->input->post('recebido') ?: 0,
            'cliente_fornecedor' => padronizarString($this->input->post('fornecedor')),
            'forma_pgto' => ($this->input->post('formaPgto') ?: 6),
            'tipo' => 1
        );

        if ($this->financeiro_model->add('lancamentos', $data) == true) {
            $this->session->set_flashdata('sucesso', 'Entrada registrada com sucesso!');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar registrar entrada.');
            redirect($urlAtual);
        }

        $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar registrar entrada.');
        redirect($urlAtual);
    }

    function despesa()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para adicionar lançamentos.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');

        $vencimento = $this->input->post('vencimento');
        $pagamento = $this->input->post('pagamento');

        if ($vencimento != null) {
            $vencimento = explode('/', $vencimento);
            $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
        } else {
            $vencimento = date('Y-m-d');
        }

        if ($pagamento != null) {
            $pagamento = explode('/', $pagamento);
            $pagamento = $pagamento[2] . '-' . $pagamento[1] . '-' . $pagamento[0];
        }

        $valor = $this->input->post('valor');
        $valor = '-' . $valor;

        if (!validate_money($valor)) {
            $valor = str_replace(array('.', ','), array('', '.'), $valor);
        }

        $data = array(
            'descricao' => padronizarString($this->input->post('descricao')),
            'valor' => $valor,
            'id_usuario' => id_usuario(),
            'data_lancamento' => $vencimento,
            'data_pagamento' => $pagamento != null ? $pagamento : $vencimento,
            'baixado' => $this->input->post('pago') ?: 0,
            'cliente_fornecedor' => padronizarString($this->input->post('fornecedor')),
            'forma_pgto' => ($this->input->post('formaPgto') ?: 3),
            'tipo' => 2
        );

        if ($this->financeiro_model->add('lancamentos', $data) == true) {
            $this->session->set_flashdata('sucesso', 'Saída registrada com sucesso!');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar registrar saída.');
            redirect($urlAtual);
        }

        $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar registrar saída.');
        redirect($urlAtual);
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para editar lançamentos.');
            redirect(base_url());
        }

        if ($this->input->post('id')) {

            $urlAtual = $this->input->post('urlAtual');
            $vencimento = $this->input->post('vencimento');
            $pagamento = $this->input->post('pagamento');

            if ($vencimento != null) {
                $vencimento = explode('/', $vencimento);
                $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
            } else {
                $vencimento = date('Y-m-d');
            }

            if ($pagamento != null) {
                $pagamento = explode('/', $pagamento);
                $pagamento = $pagamento[2] . '-' . $pagamento[1] . '-' . $pagamento[0];
            }

            $tipo = ($this->input->post('tipo'));
            $valor = $this->input->post('valor');

            if ($tipo == 2) {
                $valor = '-' . $valor;
            }

            $valor = str_replace(array('.', ','), array('', '.'), $valor);

            $data = array(
                'descricao' => padronizarString($this->input->post('descricao')),
                'valor' => $valor,
                'data_lancamento' => $vencimento,
                'data_pagamento' => $pagamento != null ? $pagamento : $vencimento,
                'baixado' => $this->input->post('pago') ?: 0,
                'cliente_fornecedor' => padronizarString($this->input->post('fornecedor')),
                'forma_pgto' => ($this->input->post('formaPgto')),
                'tipo' => $tipo
            );

            if ($this->financeiro_model->edit('lancamentos', $data, 'id_lancamento', $this->input->post('id')) == true) {
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

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
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

            if ($this->financeiro_model->delete('lancamentos', $data, 'id_lancamento', $id) == true) {
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
