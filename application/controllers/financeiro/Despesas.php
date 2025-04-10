<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Despesas extends CI_Controller
{
    protected $yearsList     = [];
    protected $mesReferencia = null;
    protected $anoReferencia = null;

    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (ENVIRONMENT == 'production') {
            $this->session->set_flashdata('erro', 'Módulo de Despesas em desenvolvimento.<br>Por favor, tente novamente mais tarde.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->load->library('pagination');
        $this->yearsList = range(2018, date('Y') + 3);
        $this->monitorarVinculosDespesasFromUser();
    }

    public function index()
    {
        $this->despesas();
    }

    //MODULO DE DESPESAS
    public function despesas()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar despesas.');
            redirect(base_url());
        }

        $terceiro       = $_GET['terceiro'] ?? null;
        $status         = $_GET['status'] ?? null;
        $tipo           = $_GET['tipo'] ?? null;
        $periodo        = $_GET['periodo'] ?? null;
        $inicio         = $_GET['dataInicial'] ?? null;
        $fim            = $_GET['dataFinal'] ?? null;
        $start          = $_GET['per_page'] ?? null;
        $referenceMonth = $_GET['mesReferencia'] ?? null;
        $referenceYear  = $_GET['anoReferencia'] ?? null;
        $where          = null;
        $limit          = null;


        $order_by = [
            'criado_em' => 'desc',
            'id'        => 'desc',
        ];

        $this->load->library('pagination');

        switch ($periodo) {
            case 'todos':
                $limit = null;
                break;
            case '3dias':
                $semana = $this->getLastThreeDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '5dias':
                $semana = $this->getLastFiveDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '7dias':
                $semana = $this->getLastSevenDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '15dias':
                $semana = $this->getLastFifteenDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '30dias':
                $semana = $this->getLastTirthyDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '60dias':
                $semana = $this->getLastSixtyDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '90dias':
                $semana = $this->getLastNinetyDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case 'especifico':
                if (isset($inicio) && isset($fim) && $inicio != null && $fim != null) {
                    $inicio = explode('/', $inicio);
                    $inicio = $inicio[2] . '-' . $inicio[1] . '-' . $inicio[0];

                    $fim = explode('/', $fim);
                    $fim = $fim[2] . '-' . $fim[1] . '-' . $fim[0];

                    if (!isset($where)) {
                        $where = 'data_vencimento BETWEEN "' . $inicio . '" AND "' . $fim . '"';
                    } else {
                        $where .= ' AND data_vencimento BETWEEN "' . $inicio . '" AND "' . $fim . '"';
                    }
                }
                break;
            case 'mensal':
                if (isset($referenceMonth) && $referenceMonth) {
                    $todayDate      = date('Y-m-d');
                    $todayArray     = explode('-', $todayDate);
                    $daysInMonth    = cal_days_in_month(CAL_GREGORIAN, $referenceMonth, $todayArray[0]);
                    $todayStartDate = $todayArray[0] . '-' . $referenceMonth . '-01';
                    $todayEndDate   = $todayArray[0] . '-' . $referenceMonth . '-' . $daysInMonth;

                    if (isset($referenceYear) && $referenceYear) {
                        $todayStartDate = $referenceYear . '-' . $referenceMonth . '-01';
                        $todayEndDate   = $referenceYear . '-' . $referenceMonth . '-' . $daysInMonth;
                    } else {
                        $referenceYear = $todayArray[0];
                    }

                    if (!isset($where)) {
                        $where = "data_vencimento BETWEEN '$todayStartDate' AND '$todayEndDate'";
                    } else {
                        $where .= " AND data_vencimento BETWEEN '$todayStartDate' AND '$todayEndDate'";
                    }
                }
                break;
            default:
                $todayDate      = date('Y-m-d');
                $todayArray     = explode('-', $todayDate);
                $daysInMonth    = cal_days_in_month(CAL_GREGORIAN, $todayArray[1], $todayArray[0]);
                $todayStartDate = $todayArray[0] . '-' . $todayArray[1] . '-01';
                $todayEndDate   = $todayArray[0] . '-' . $todayArray[1] . '-' . $daysInMonth;
                $referenceMonth = $todayArray[1];

                if (isset($referenceYear) && $referenceYear) {
                    $todayStartDate = $referenceYear . '-' . $todayArray[1] . '-01';
                    $todayEndDate   = $referenceYear . '-' . $todayArray[1] . '-' . $daysInMonth;
                } else {
                    $referenceYear = $todayArray[0];
                }

                if (!isset($where)) {
                    // $where = "data_vencimento BETWEEN '$todayStartDate' AND '$todayEndDate'";
                } else {
                    // $where .= " AND data_vencimento BETWEEN '$todayStartDate' AND '$todayEndDate'";
                }

                if ($this->despesa_model->countDespesasFromUser($where) > 20) {
                    // $limit = 20;
                    // $start = $this->financeiro_model->countLancamentos(getUserId()) - $limit;
                }
                break;
        }

        if (isset($status) && $status != null) {
            if ($status == 'pendente') {
                if (!isset($where)) {
                    $where = 'pago = 0';
                } else {
                    $where .= ' AND pago = 0';
                }
            } elseif ($status == 'efetivado') {
                if (!isset($where)) {
                    $where = 'pago = 1';
                } else {
                    $where .= ' AND pago = 1';
                }
            }
        }

        if ($tipo) {
            if ($tipo == 'unica') {
                if (!isset($where)) {
                    $where = 'tipo = 1';
                } else {
                    $where .= ' AND tipo = 1';
                }
            } elseif ($tipo == 'recorrente') {
                if (!isset($where)) {
                    $where = 'tipo = 2';
                } else {
                    $where .= ' AND tipo = 2';
                }
            }
        }

        $query_string = null;
        foreach ($_GET as $key => $value) {
            if ($key != 'per_page') {
                $query_string .= $key . '=' . $value . '&';
            }
        }

        if ($referenceMonth) {
            $dateFormatter     = new \IntlDateFormatter(
                'pt_BR',
                \IntlDateFormatter::FULL,
                \IntlDateFormatter::NONE,
                'America/Sao_Paulo',
                \IntlDateFormatter::GREGORIAN,
                "MMMM"
            );
            $dateObj           = DateTime::createFromFormat('!m', ($referenceMonth));
            $nextMonthObj      = DateTime::createFromFormat('!m', ($referenceMonth + 1));
            $prevMonthObj      = DateTime::createFromFormat('!m', ($referenceMonth - 1));
            $data['month']     = str_replace('.', '', strtoupper($dateFormatter->format($dateObj)));
            $data['nextMonth'] = str_replace('.', '', strtoupper($dateFormatter->format($nextMonthObj)));
            $data['prevMonth'] = str_replace('.', '', strtoupper($dateFormatter->format($prevMonthObj)));
        }

        $config['base_url']          = base_url('financeiro/despesas');
        $config['suffix']            = '&' . $query_string;
        $config['first_url']         = $config['base_url'] . '?' . $query_string;
        $config['total_rows']        = $this->despesa_model->countDespesasFromUser($where ?? null);
        $config['per_page']          = 15;
        $config['page_query_string'] = true;
        $config['prev_link']         = '<i class="fas fa-angle-left"></i>';
        $config['next_link']         = '<i class="fas fa-angle-right"></i>';
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

        $results = $this->despesa_model->get(
            $where,
            $limit,
            $config['total_rows'],
            $config['per_page'],
            $start,
            $order_by ?: 'desc'
        );

        $data['yearsList']          = $this->yearsList;
        $data['referenceMonth']     = null;
        $data['autoLinkExpesnses']  = true;
        $data['referenceYear']      = $referenceYear;
        $data['total_provisorio']   = $this->financeiro_model->getTotalProvisorio(getUserId());
        $data['saidas_pendentes']   = $this->financeiro_model->getSaidasPendentes(getUserId());
        $data['entradas_pendentes'] = $this->financeiro_model->getEntradasPendentes(getUserId());
        $data['total']              = $this->financeiro_model->getTotal(getUserId());
        $data['formasPagamento']    = $this->financeiro_model->getFormasPagamento();
        $data['results']            = $this->getPaymentFormDescription($results);

        $data['menuFinanceiro'] = true;
        $data['view']           = 'despesas/gerenciar_despesas';

        $this->load->view('tema/topo', $data);
    }

    public function detalhes($idDespesa)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar detalhes de despesas.');
            redirect(base_url());
        }

        if (!is_numeric($idDespesa)) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/despesas');
        }

        $detalhesDespesa = $this->despesa_model->getDespesaById($idDespesa);

        if (!$detalhesDespesa) {
            $this->session->set_flashdata('erro', 'Despesa não encontrada');
            redirect('financeiro/despesas');
        }

        $terceiro       = $_GET['terceiro'] ?? null;
        $status         = $_GET['status'] ?? null;
        $tipo           = $_GET['tipo'] ?? null;
        $periodo        = $_GET['periodo'] ?? null;
        $inicio         = $_GET['dataInicial'] ?? null;
        $fim            = $_GET['dataFinal'] ?? null;
        $start          = $_GET['per_page'] ?? null;
        $referenceMonth = $_GET['mesReferencia'] ?? null;
        $referenceYear  = $_GET['anoReferencia'] ?? null;
        $where          = null;
        $limit          = null;
        $order_by       = [
            'data_vencimento' => 'desc',
        ];

        $this->load->library('pagination');

        switch ($periodo) {
            case 'todos':
                break;
            case '3dias':
                $semana = $this->getLastThreeDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '5dias':
                $semana = $this->getLastFiveDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '7dias':
                $semana = $this->getLastSevenDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '15dias':
                $semana = $this->getLastFifteenDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '30dias':
                $semana = $this->getLastTirthyDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '60dias':
                $semana = $this->getLastSixtyDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '90dias':
                $semana = $this->getLastNinetyDays();
                $where  = 'data_vencimento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case 'especifico':
                if (isset($inicio) && isset($fim) && $inicio != null && $fim != null) {
                    $inicio = explode('/', $inicio);
                    $inicio = $inicio[2] . '-' . $inicio[1] . '-' . $inicio[0];

                    $fim = explode('/', $fim);
                    $fim = $fim[2] . '-' . $fim[1] . '-' . $fim[0];

                    if (!isset($where)) {
                        $where = 'data_vencimento BETWEEN "' . $inicio . '" AND "' . $fim . '"';
                    } else {
                        $where .= ' AND data_vencimento BETWEEN "' . $inicio . '" AND "' . $fim . '"';
                    }
                }
                break;
            case 'mensal':
                if (isset($referenceMonth) && $referenceMonth) {
                    $todayDate      = date('Y-m-d');
                    $todayArray     = explode('-', $todayDate);
                    $daysInMonth    = cal_days_in_month(CAL_GREGORIAN, $referenceMonth, $todayArray[0]);
                    $todayStartDate = $todayArray[0] . '-' . $referenceMonth . '-01';
                    $todayEndDate   = $todayArray[0] . '-' . $referenceMonth . '-' . $daysInMonth;

                    if (isset($referenceYear) && $referenceYear) {
                        $todayStartDate = $referenceYear . '-' . $referenceMonth . '-01';
                        $todayEndDate   = $referenceYear . '-' . $referenceMonth . '-' . $daysInMonth;
                    } else {
                        $referenceYear = $todayArray[0];
                    }

                    if (!isset($where)) {
                        $where = "data_vencimento BETWEEN '$todayStartDate' AND '$todayEndDate'";
                    } else {
                        $where .= " AND data_vencimento BETWEEN '$todayStartDate' AND '$todayEndDate'";
                    }
                }
                break;
            default:
                $todayDate      = date('Y-m-d');
                $todayArray     = explode('-', $todayDate);
                $daysInMonth    = cal_days_in_month(CAL_GREGORIAN, $todayArray[1], $todayArray[0]);
                $todayStartDate = $todayArray[0] . '-' . $todayArray[1] . '-01';
                $todayEndDate   = $todayArray[0] . '-' . $todayArray[1] . '-' . $daysInMonth;
                $referenceMonth = $todayArray[1];

                if (isset($referenceYear) && $referenceYear) {
                    $todayStartDate = $referenceYear . '-' . $todayArray[1] . '-01';
                    $todayEndDate   = $referenceYear . '-' . $todayArray[1] . '-' . $daysInMonth;
                } else {
                    $referenceYear = $todayArray[0];
                }

                if (!isset($where)) {
                    // $where = "data_vencimento BETWEEN '$todayStartDate' AND '$todayEndDate'";
                } else {
                    // $where .= " AND data_vencimento BETWEEN '$todayStartDate' AND '$todayEndDate'";
                }

                if ($this->despesa_model->countDespesasFromUser($where) > 20) {
                    // $limit = 20;
                    // $start = $this->financeiro_model->countLancamentos(getUserId()) - $limit;
                }
                break;
        }

        if (isset($status) && $status != null) {
            if ($status == 'pendente') {
                if (!isset($where)) {
                    $where = 'pago = 0';
                } else {
                    $where .= ' AND pago = 0';
                }
            } elseif ($status == 'efetivado') {
                if (!isset($where)) {
                    $where = 'pago = 1';
                } else {
                    $where .= ' AND pago = 1';
                }
            }
        }

        if ($tipo) {
            if ($tipo == 'unica') {
                if (!isset($where)) {
                    $where = 'tipo = 1';
                } else {
                    $where .= ' AND tipo = 1';
                }
            } elseif ($tipo == 'recorrente') {
                if (!isset($where)) {
                    $where = 'tipo = 2';
                } else {
                    $where .= ' AND tipo = 2';
                }
            }
        }

        $query_string = null;
        foreach ($_GET as $key => $value) {
            if ($key != 'per_page') {
                $query_string .= $key . '=' . $value . '&';
            }
        }

        if ($referenceMonth) {
            $dateFormatter     = new \IntlDateFormatter(
                'pt_BR',
                \IntlDateFormatter::FULL,
                \IntlDateFormatter::NONE,
                'America/Sao_Paulo',
                \IntlDateFormatter::GREGORIAN,
                "MMMM"
            );
            $dateObj           = DateTime::createFromFormat('!m', ($referenceMonth));
            $nextMonthObj      = DateTime::createFromFormat('!m', ($referenceMonth + 1));
            $prevMonthObj      = DateTime::createFromFormat('!m', ($referenceMonth - 1));
            $data['month']     = str_replace('.', '', strtoupper($dateFormatter->format($dateObj)));
            $data['nextMonth'] = str_replace('.', '', strtoupper($dateFormatter->format($nextMonthObj)));
            $data['prevMonth'] = str_replace('.', '', strtoupper($dateFormatter->format($prevMonthObj)));
        }

        $config['base_url']          = base_url('financeiro/despesas/detalhes/' . $idDespesa);
        $config['suffix']            = '&' . $query_string;
        $config['first_url']         = $config['base_url'] . '?' . $query_string;
        $config['total_rows']        = $this->despesa_model->countLancamentosFromDespesa($idDespesa, $where ?? null);
        $config['per_page']          = 12;
        $config['page_query_string'] = true;
        $config['prev_link']         = '<i class="fas fa-angle-left"></i>';
        $config['next_link']         = '<i class="fas fa-angle-right"></i>';
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

        $lancamentosDespesa = $this->despesa_model->getLancamentosDespesa(
            $idDespesa,
            $where,
            $config['total_rows'],
            $config['per_page'],
            $order_by ?: ['data_vencimento' => 'desc']
        );

        $data['yearsList']       = $this->yearsList;
        $data['referenceMonth']  = null;
        $data['referenceYear']   = $referenceYear;
        $data['formasPagamento'] = $this->financeiro_model->getFormasPagamento();
        $data['results']         = $this->getPaymentFormDescription($this->getTranslateMonth($lancamentosDespesa, true));
        $data['despesa']         = $this->getPaymentFormDescription($this->addZeroOnInstallments($detalhesDespesa));
        $data['hasLancamentos']  = $this->despesa_model->getLancamentosDespesa($idDespesa);
        $data['parcelamento']    = (int)$data['despesa']->total_parcelas ?? null;
        $data['parcelasPagas']   = $this->despesa_model->getParcelasPagas($idDespesa);
        $data['menuFinanceiro']  = true;
        $data['view']            = 'despesas/detalhes_despesa';

        $this->load->view('tema/topo', $data);
    }

    private function getTranslateMonth($request, $abbreviate = false)
    {
        if (is_array($request)) {
            foreach ($request as $item) {
                if (is_object($item) && isset($item->id_forma_pagamento)) {
                    $item->mes_descricao = translateMonth($item->mes_referencia, $abbreviate);
                }

                if (is_array($item) && isset($item['id_forma_pgto'])) {
                    $item['mes_descricao'] = translateMonth($item['mes_descricao'], $abbreviate);
                }
            }
        }

        if (is_object($request) && isset($request->id_forma_pagamento)) {
            $request->mes_descricao = translateMonth($request->mes_referencia, $abbreviate);
        }

        return $request;
    }

    private function getPaymentFormDescription($request)
    {
        if (is_array($request)) {
            foreach ($request as $item) {
                if (is_object($item) && isset($item->id_forma_pagamento)) {
                    $item->descricao_pagamento = $this->financeiro_model->getFormasPagamento($item->id_forma_pagamento);
                }

                if (is_array($item) && isset($item['id_forma_pgto'])) {
                    $item['descricao_pagamento'] = $this->financeiro_model->getFormasPagamento($item['id_forma_pgto']);
                }
            }
        }

        if (is_object($request) && isset($request->id_forma_pagamento)) {
            $request->descricao_pagamento = $this->financeiro_model->getFormasPagamento($request->id_forma_pagamento);
        }

        return $request;
    }

    private function addZeroOnInstallments($request)
    {
        if (is_array($request)) {
            foreach ($request as $item) {
                if (is_object($item) && isset($item->total_parcelas)) {
                    if ($item->total_parcelas < 10) {
                        $item->total_parcelas = sprintf('0%d', $item->total_parcelas);
                    }
                }

                if (is_array($item) && isset($item['id_forma_pgto'])) {
                    if ($item['total_parcelas'] < 10) {
                        $item['total_parcelas'] = sprintf('0%d', $item['total_parcelas']);
                    }
                }
            }
        }

        if (is_object($request) && isset($request->id_forma_pagamento)) {
            if ($request->total_parcelas < 10) {
                $request->total_parcelas = sprintf('0%d', $request->total_parcelas);
            }
        }

        return $request;
    }

    public function registrar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para cadastrar despesas.');
            redirect(base_url());
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/despesas');
        }

        $descricao        = $_POST["descricao"];
        $observacoes      = $_POST["observacoes"] ?? null;
        $urlAtual         = $_POST["urlAtual"];
        $valor            = $_POST["valor"];
        $fornecedor       = $_POST["fornecedor"] ?? null;
        $despesaParcelada = $_POST["despesa_parcelada"] ?? null;
        $despesaTerceiros = $_POST["despesa_terceiros"] ?? null;
        $nomeTerceiro     = $_POST["nome_terceiro"] ?? null;
        $qntParcelas      = $_POST["qnt_parcelas"] != '' ? $_POST["qnt_parcelas"] : null;
        $diaVencimento    = $_POST["dia_vencimento"] != '' ? $_POST["dia_vencimento"] : null;
        $formaPagto       = $_POST["forma_pagamento"] ?? null;
        $valorParcela     = $_POST["valor_parcela"] ?? null;
        $tipoDespesa      = $_POST["tipo"] == 'unica' ? 1 : 2;

        if (!validate_money($valor)) {
            $valor = str_replace(array('.', ','), array('', '.'), $valor);
        }

        if (!$qntParcelas || !$despesaParcelada) {
            $qntParcelas  = null;
            $valorParcela = $valor;
        }

        if ($qntParcelas > 48) {
            $this->session->set_flashdata('erro', 'O parcelamento informado excede o máximo permitido (até 48x)');
            redirect($urlAtual);
        }

        if (!validate_money($valorParcela)) {
            $valorParcela = str_replace(array('.', ','), array('', '.'), $valorParcela);
        }

        if (!$diaVencimento) {
            $today         = date('d');
            $diaVencimento = $today;
        }

        $dataArrayToDbPersist = [
            'id_usuario'         => getUserId(),
            'descricao'          => padronizarString($descricao),
            'fornecedor'         => padronizarString($fornecedor),
            'nome_terceiro'      => padronizarString($nomeTerceiro),
            'observacoes'        => sanitizarString($observacoes),
            'tipo_despesa'       => $tipoDespesa,
            'id_forma_pagamento' => $formaPagto,
            'valor_parcela'      => $valorParcela,
            'valor_total'        => $valor,
            'dia_vencimento'     => $diaVencimento,
            'total_parcelas'     => $qntParcelas,
            'despesa_parcelada'  => $despesaParcelada,
            'despesa_terceiros'  => $despesaTerceiros,
        ];

        $this->session->set_flashdata('erro', 'Erro ao tentar registrar despesa');

        if ($this->despesa_model->add($dataArrayToDbPersist)) {
            $lastInsertedId = $this->despesa_model->lastInsertedId();
            if ($tipoDespesa == 1) {
                $this->criaLancamentoDespesa($lastInsertedId);
            }
            $this->session->set_flashdata('sucesso', 'Despesa registrada com sucesso');
        }
        redirect($urlAtual);
    }

    public function copiar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para copiar despesas.');
            redirect(base_url());
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/despesas');
        }

        $descricao        = $_POST["descricao"];
        $observacoes      = $_POST["observacoes"] ?? null;
        $urlAtual         = $_POST["urlAtual"];
        $valor            = $_POST["valor"];
        $fornecedor       = $_POST["fornecedor"] ?? null;
        $despesaParcelada = $_POST["despesa_parcelada"] ?? null;
        $despesaTerceiros = $_POST["despesa_terceiros"] ?? null;
        $nomeTerceiro     = $_POST["nome_terceiro"] ?? null;
        $qntParcelas      = $_POST["qnt_parcelas"] != '' ? $_POST["qnt_parcelas"] : null;
        $diaVencimento    = $_POST["dia_vencimento"] != '' ? $_POST["dia_vencimento"] : null;
        $formaPagto       = $_POST["forma_pagamento"] ?? null;
        $valorParcela     = $_POST["valor_parcela"] ?? null;
        $tipoDespesa      = $_POST["tipo"] == 'unica' ? 1 : 2;

        if (!validate_money($valor)) {
            $valor = str_replace(array('.', ','), array('', '.'), $valor);
        }

        if (!$qntParcelas || !$despesaParcelada) {
            $qntParcelas  = 1;
            $valorParcela = $valor;
        }

        if ($qntParcelas > 48) {
            $this->session->set_flashdata('erro', 'O parcelamento informado excede o máximo permitido (até 48x)');
            redirect($urlAtual);
        }

        if (!validate_money($valorParcela)) {
            $valorParcela = str_replace(array('.', ','), array('', '.'), $valorParcela);
        }

        if (!$diaVencimento) {
            $today         = date('d');
            $diaVencimento = $today;
        }

        $dataArrayToDbPersist = [
            'id_usuario'         => getUserId(),
            'descricao'          => padronizarString($descricao),
            'fornecedor'         => padronizarString($fornecedor),
            'nome_terceiro'      => padronizarString($nomeTerceiro),
            'observacoes'        => sanitizarString($observacoes),
            'tipo_despesa'       => $tipoDespesa,
            'id_forma_pagamento' => $formaPagto,
            'valor_parcela'      => $valorParcela,
            'valor_total'        => $valor,
            'dia_vencimento'     => $diaVencimento,
            'total_parcelas'     => $qntParcelas,
            'despesa_parcelada'  => $despesaParcelada,
            'despesa_terceiros'  => $despesaTerceiros,
        ];

        $this->session->set_flashdata('erro', 'Erro ao tentar copiar despesa');

        if ($this->despesa_model->add($dataArrayToDbPersist)) {
            $lastInsertedId = $this->despesa_model->lastInsertedId();
            if ($tipoDespesa == 1) {
                $this->criaLancamentoDespesa($lastInsertedId);
            }
            $this->session->set_flashdata('sucesso', 'Despesa copiada com sucesso');
        }
        redirect($urlAtual);
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar despesas.');
            redirect(base_url());
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/despesas');
        }

        $idDespesa        = $_POST["id_despesa"];
        $descricao        = $_POST["descricao"];
        $observacoes      = $_POST["observacoes"] ?? null;
        $urlAtual         = $_POST["urlAtual"];
        $valor            = $_POST["valor"];
        $fornecedor       = $_POST["fornecedor"] ?? null;
        $despesaParcelada = $_POST["despesa_parcelada"] ?? null;
        $despesaTerceiros = $_POST["despesa_terceiros"] ?? null;
        $nomeTerceiro     = $_POST["nome_terceiro"] ?? null;
        $qntParcelas      = $_POST["qnt_parcelas"] != '' ? $_POST["qnt_parcelas"] : null; // testar qntParcelas como varchar, em caso de erro, definir como INT
        $diaVencimento    = $_POST["dia_vencimento"] != '' ? $_POST["dia_vencimento"] : null;
        $formaPagto       = $_POST["forma_pagamento"] ?? null;
        $valorParcela     = $_POST["valor_parcela"] ?? null;
        $tipoDespesa      = $_POST["tipo"] == 'unica' ? 1 : 2;

        if (!validate_money($valor)) {
            $valor = str_replace(array('.', ','), array('', '.'), $valor);
        }

        if (!$qntParcelas || !$despesaParcelada) {
            $qntParcelas  = 1;
            $valorParcela = $valor;
        }

        if ($qntParcelas > 48) {
            $this->session->set_flashdata('erro', 'O parcelamento informado excede o máximo permitido (até 48x)');
            redirect($urlAtual);
        }

        if (!validate_money($valorParcela)) {
            $valorParcela = str_replace(array('.', ','), array('', '.'), $valorParcela);
        }

        if (!$diaVencimento) {
            $today         = date('d');
            $diaVencimento = $today;
        }

        $dataArrayToDbUpdate = [
            'descricao'          => padronizarString($descricao),
            'fornecedor'         => padronizarString($fornecedor),
            'nome_terceiro'      => padronizarString($nomeTerceiro),
            'observacoes'        => sanitizarString($observacoes),
            'tipo_despesa'       => $tipoDespesa,
            'id_forma_pagamento' => $formaPagto,
            'valor_parcela'      => $valorParcela,
            'valor_total'        => $valor,
            'dia_vencimento'     => $diaVencimento,
            'total_parcelas'     => $qntParcelas,
            'despesa_parcelada'  => $despesaParcelada,
            'despesa_terceiros'  => $despesaTerceiros,
        ];

        if ($this->despesa_model->edit($dataArrayToDbUpdate, 'id', $idDespesa)) {
            if ($this->editarLancamentosDespesas($idDespesa)) {
                $this->session->set_flashdata('sucesso', 'Despesa alterada com sucesso');
                redirect($urlAtual);
            }
        }
        $this->session->set_flashdata('erro', 'Erro ao tentar alterar despesa');
        redirect($urlAtual);
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir despesas.');
            redirect(base_url());
        }
        $urlAtual  = $this->input->post('urlAtual');
        $idDespesa = $this->input->post('idDespesa');

        if (!$idDespesa) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/despesas');
        }

        if (!$this->despesa_model->deleteDespesa($idDespesa)) {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir despesa');
            redirect($urlAtual);
        }
        $this->session->set_flashdata('sucesso', 'Despesa excluída com sucesso');
        redirect($urlAtual);
    }

    public function excluirLancamento()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir registro de despesas.');
            redirect(base_url());
        }
        $urlAtual            = $this->input->post('urlAtual');
        $idLancamentoDespesa = $this->input->post('idLancamentoDespesa') ?? null;

        if (!$idLancamentoDespesa) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($urlAtual);
        }

        if (!$this->despesa_model->deleteLancamentoDespesa($idLancamentoDespesa)) {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir registro de despesa');
            redirect($urlAtual);
        }
        $this->session->set_flashdata('sucesso', 'Registro excluído com sucesso');
        redirect($urlAtual);
    }

    public function pagar()
    {
        //TODO: implementar metodo para ativar despesa

        //TODO: implementar metodo para desativar despesa
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para pagar despesas.');
            redirect(base_url());
        }
        $urlAtual       = $this->input->post('urlAtual');
        $data_pagamento = date('d/m/Y');

        if ($_REQUEST['data_pagamento']) {
            $data_pagamento = $_REQUEST['data_pagamento'];
        }

        $data_pagamento = explode('/', $data_pagamento);
        $data_pagamento = $data_pagamento[2] . '-' . $data_pagamento[1] . '-' . $data_pagamento[0];

        $data = array(
            'fatura_paga'    => 1,
            'data_pagamento' => $data_pagamento,
            'forma_pgto'     => $_POST['forma_pagamento']
        );

        if ($this->fatura_model->edit('faturas', $data, 'id_fatura', $_POST['id_fatura'])) {
            $vinculoFatura = $this->fatura_model->getVinculoFatura($_POST['id_fatura']);
            if ($vinculoFatura) {
                $detalhesFatura       = $this->fatura_model->getDetalhesFatura($_POST['id_fatura']);
                $valorTotalFatura     = $this->fatura_model->getValorTotalFatura($_POST['id_fatura']);
                $detalhesCartaoFatura = $this->cartoes_model->getCartao($detalhesFatura->id_cartao);
                $n_cartao             = explode(" ", trim(decriptar($detalhesCartaoFatura->numero)));
                $final                = $n_cartao[3];
                $apelido              = $detalhesCartaoFatura->apelido ? ' - ' . $detalhesCartaoFatura->apelido : null;

                $data = array(
                    'descricao'          => 'FATURA CARTAO DE CREDITO' . $apelido,
                    'valor'              => '-' . $valorTotalFatura,
                    'data_lancamento'    => $detalhesFatura->data_pagamento ?? $detalhesFatura->vencimento,
                    'data_pagamento'     => $detalhesFatura->data_pagamento,
                    'cliente_fornecedor' => $detalhesCartaoFatura->bandeira ? $detalhesCartaoFatura->bandeira . ' - FINAL ' . $final : null,
                    'forma_pgto'         => $detalhesFatura->forma_pgto ?? 5,
                    'pago'               => 1,
                    'tipo'               => 2,
                );
                $this->fatura_model->edit('lancamentos', $data, 'id_fatura', $_POST['id_fatura']);
            }
            $this->session->set_flashdata('sucesso', 'Fatura paga com sucesso');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar pagar a fatura.');
            redirect($urlAtual);
        }
    }

    public function ativar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para ativar despesas.');
            redirect(base_url());
        }

        $id       = $this->input->post('id');
        $urlAtual = $this->input->post('urlAtual');

        if (!$id) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($urlAtual);
        }

        if (!$this->despesa_model->ativaDespesa($id)) {
            $this->session->set_flashdata('erro', 'Erro ao tentar ativar despesa');
            redirect($urlAtual);
        }
        $this->session->set_flashdata('sucesso', 'Despesa ativada com sucesso');
        redirect($urlAtual);
    }

    public function desativar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para desativar despesas.');
            redirect(base_url());
        }

        $id       = $this->input->post('id');
        $urlAtual = $this->input->post('urlAtual');

        if (!$id) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($urlAtual);
        }

        if (!$this->despesa_model->desativaDespesa($id)) {
            $this->session->set_flashdata('erro', 'Erro ao tentar desativar despesa');
            redirect($urlAtual);
        }
        $this->session->set_flashdata('sucesso', 'Despesa desativada com sucesso');
        redirect($urlAtual);
    }

    public function vincularLancamentoDespesa()
    {
        //TODO: metodo para vincular um lancamento individual de uma despesa
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para vincular despesas.');
            redirect(base_url());
        }
        $urlAtual                 = $this->input->post('urlAtual');
        $this->mesReferencia      = $this->input->post('mesReferencia') ?? null;
        $this->anoReferencia      = $this->input->post('anoReferencia') ?? null;
        $idDespesa                = $this->input->post('idDespesa');
        $idLancamentoDespesa      = $this->input->post('idLancamentoDespesa') ?? null;
        $lancamentosDespesa       = $this->despesa_model->getLancamentosDespesa($idDespesa);
        $vinculoLancamentoDespesa = $this->despesa_model->getVinculoLancamentoDespesa($idDespesa);
        $despesa                  = $this->despesa_model->getDespesaById($idDespesa);

        if (!$despesa) {
            $this->session->set_flashdata('erro', 'Despesa não encontrada');
            redirect($urlAtual);
        }
        // a condiçao abaixo só se aplica para vínculos individuais (botao de vinculo em cada sub-registro da despesa)
        if ($vinculoLancamentoDespesa) {
            $this->session->set_flashdata('erro', 'Vínculo já existente para o registro solicitado');
            redirect($urlAtual);
        }

        //TODO:
        // Despesa parcelada: efetuar o vinculo com Lançamentos mediante mes e ano de referencia, e o dia de vencimento da despesa


        // if (!$lancamentosDespesa) {
        //TODO:
        // Despesa recorrente: criar um registro na tabela lancamentos_despesas e repetir o mesmo procedimento para despesa parcelada
        if ($despesa->tipo_despesa == 2) {
            if (!$this->criaLancamentoDespesa($idDespesa)) {
                $this->session->set_flashdata('erro', 'Erro ao tentar adicionar registro de despesa');
                redirect($urlAtual);
            }

            //TODO:
            // apos criar o registro em lancamento_despesa, gerar vinculo com o modulo de Lancamentos para este mesmo registro = OK

            //TODO: separar a logica do vinculo com o modulo de Lancamentos em um método específico

            $idLancamentoDespesa = $this->despesa_model->lastInsertedId();
        }
        // }

        //chamada do metodo copiaRegistroEmModuloLancamentos()
        if (!$this->copiaRegistroEmModuloLancamentos($idDespesa, $idLancamentoDespesa)) {
            $this->session->set_flashdata('erro', 'Erro ao tentar vincular despesa');
            redirect($urlAtual);
        }

        $this->session->set_flashdata('sucesso', 'Registro adicionado com sucesso');
        redirect($urlAtual);
    }

    private function copiaRegistroEmModuloLancamentos($idDespesa, $idLancamentoDespesa)
    {
        $despesa           = $this->despesa_model->getDespesaById($idDespesa);
        $lancamentoDespesa = $this->despesa_model->getLancamentoDespesaById($idLancamentoDespesa);
        $valorFormatado    = sprintf('-%s', $despesa->valor_total);
        $tipoLancamento    = 2;
        $dataLancamento    = sprintf('%s-%s-%s', $lancamentoDespesa->ano_referencia, $lancamentoDespesa->mes_referencia, $despesa->dia_vencimento);

        if ($despesa->despesa_terceiros) {
            $valorFormatado = sprintf('%s', $despesa->valor_total);
            $tipoLancamento = 1;
        }

        $data = [
            'id_usuario'         => getUserId(),
            'id_despesa'         => $despesa->id,
            'descricao'          => $despesa->descricao,
            'observacoes'        => $despesa->observacoes,
            'cliente_fornecedor' => $despesa->nome_terceiro ?: $despesa->fornecedor,
            'valor'              => $valorFormatado,
            'data_lancamento'    => $dataLancamento,
            'data_pagamento'     => $lancamentoDespesa->data_pagamento ?: null,
            'forma_pgto'         => $despesa->id_forma_pagamento,
            'baixado'            => ($despesa->despesa_quitada == 1) ?: 0,
            'tipo'               => $tipoLancamento
        ];

        if (!$this->financeiro_model->add('lancamentos', $data)) {
            return false;
        }
        return true;
    }

    public function desvincularDespesas()
    {
        //TODO: implementar metodo para vincular despesas
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para vincular despesas.');
            redirect(base_url());
        }
        $urlAtual           = $this->input->post('urlAtual');
        $idDespesa          = $this->input->post('idDespesa');
        $lancamentosDespesa = $this->despesa_model->getLancamentosDespesa($idDespesa);

        if (!$lancamentosDespesa) {
            $this->session->set_flashdata('erro', 'A despesa solicitada não possui parcelas disponíveis para gerenciamento');
            redirect($urlAtual);
        }

        //TODO:
        // Despesa parcelada: efetuar o vinculo com Lançamentos mediante mes e ano de referencia, e o dia de vencimento da despesa
        // Despesa recorrente: criar um registro na tabela lancamentos_despesas e repetir o mesmo procedimento para despesa parcelada

        if ($this->despesa_model->vinculaDespesa($idDespesa)) {
            $detalhesFatura       = $this->fatura_model->getDetalhesFatura($idDespesa);
            $valorTotalFatura     = $this->fatura_model->getValorTotalFatura($idDespesa);
            $detalhesCartaoFatura = $this->cartoes_model->getCartao($detalhesFatura->id_cartao);
            $n_cartao             = explode(" ", trim(decriptar($detalhesCartaoFatura->numero)));
            $final                = $n_cartao[3];
            $apelido              = $detalhesCartaoFatura->apelido ? ' - ' . $detalhesCartaoFatura->apelido : null;

            $data = array(
                'id_usuario'         => getUserId(),
                'id_fatura'          => $idDespesa,
                'descricao'          => 'FATURA CARTAO DE CREDITO' . $apelido,
                'cliente_fornecedor' => $detalhesCartaoFatura->bandeira ? $detalhesCartaoFatura->bandeira . ' - FINAL ' . $final : null,
                'valor'              => '-' . $valorTotalFatura,
                'data_lancamento'    => $detalhesFatura->vencimento ?? $detalhesFatura->data_pagamento,
                'data_pagamento'     => $detalhesFatura->data_pagamento ?? $detalhesFatura->vencimento,
                'forma_pgto'         => $detalhesFatura->forma_pgto ?? 5,
                'pago'               => ($detalhesFatura->fatura_paga == 1),
                'tipo'               => 2
            );
            $this->financeiro_model->add('lancamentos', $data);
            $this->session->set_flashdata('sucesso', 'Fatura vinculada com sucesso');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar vincular a fatura');
        }
        redirect($urlAtual);
    }

    public function gerenciarLoteVinculos()
    {
        //TODO: estudar a real necessidade de um metodo para gerenciar vinculos de despesas
        // (vincular e desvincular um lote de despesas, botao disponivel apenas na view gerenciar_despesas)

    }

    public function desvincular()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para desvincular despesas.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');
        $idFatura = $this->input->post('idFatura');

        $data = array(
            'fatura_vinculada' => 0
        );

        if ($this->fatura_model->edit('faturas', $data, 'id_fatura', $idFatura)) {
            $vinculoFatura = $this->fatura_model->getVinculoFatura($idFatura);
            if (!$vinculoFatura) {
                $this->session->set_flashdata('erro', 'A fatura solicitada não possui vínculo ativo ao módulo de Lançamentos');
                redirect($urlAtual);
            }

            $this->fatura_model->delete_real('lancamentos', 'id_fatura', $idFatura);
            $this->session->set_flashdata('sucesso', 'Fatura desvinculada com sucesso');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar desvincular a fatura');
            redirect($urlAtual);
        }
    }

    public function configurar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar despesas.');
            redirect(base_url());
        }
        $urlAtual  = $_POST['urlAtual'];
        $id_cartao = $_POST['id_cartao'];
        $dia       = $_POST['dia_vencimento'];

        $data = array(
            'id_usuario'     => getUserId(),
            'id_cartao'      => $id_cartao,
            'dia_vencimento' => $dia
        );

        if ($this->fatura_model->existeConfiguracao($id_cartao)) {
            $this->fatura_model->edit('configs_faturas', $data, 'id_cartao', $id_cartao);
            $adicionais = $this->cartoes_model->getCartoesAdicionais($id_cartao);

            $faturas = $this->fatura_model->getFaturasAbertasCartao($id_cartao);
            foreach ($faturas as $fatura) {
                $vencimento = explode('-', $fatura->vencimento);
                $vencimento = $vencimento[0] . '-' . $vencimento[1] . '-' . $dia;

                $data = array(
                    'vencimento' => $vencimento
                );
                $this->fatura_model->edit('faturas', $data, 'id_fatura', $fatura->id_fatura);
            }

            foreach ($adicionais as $adicional) {
                $faturas = $this->fatura_model->getFaturasAbertasCartao($adicional->id_cartao);
                foreach ($faturas as $fatura) {
                    $vencimento = explode('-', $fatura->vencimento);
                    $vencimento = $vencimento[0] . '-' . $vencimento[1] . '-' . $dia;

                    $data = array(
                        'vencimento' => $vencimento
                    );
                    $this->fatura_model->edit('faturas', $data, 'id_fatura', $fatura->id_fatura);
                }

                $data_adicional = array(
                    'id_usuario'        => $adicional->id_usuario,
                    'id_cartao'         => $adicional->id_cartao,
                    'id_cartao_titular' => $id_cartao,
                    'dia_vencimento'    => $dia,
                    'adicional'         => 1
                );
                $this->fatura_model->edit('configs_faturas', $data_adicional, 'id_cartao', $adicional->id_cartao);
            }
            $this->session->set_flashdata('sucesso', 'Configurações alteradas com sucesso');
        } else {
            $this->fatura_model->add('configs_faturas', $data);
            $adicionais = $this->cartoes_model->getCartoesAdicionais($id_cartao);

            $faturas = $this->fatura_model->getFaturasAbertasCartao($id_cartao);
            foreach ($faturas as $fatura) {
                $vencimento = explode('-', $fatura->vencimento);
                $vencimento = $vencimento[0] . '-' . $vencimento[1] . '-' . $dia;

                $data = array(
                    'vencimento' => $vencimento
                );
                $this->fatura_model->edit('faturas', $data, 'id_fatura', $fatura->id_fatura);
            }

            foreach ($adicionais as $adicional) {
                $faturas = $this->fatura_model->getFaturasAbertasCartao($adicional->id_cartao);
                foreach ($faturas as $fatura) {
                    $vencimento = explode('-', $fatura->vencimento);
                    $vencimento = $vencimento[0] . '-' . $vencimento[1] . '-' . $dia;

                    $data = array(
                        'vencimento' => $vencimento
                    );
                    $this->fatura_model->edit('faturas', $data, 'id_fatura', $fatura->id_fatura);
                }

                $data_adicional = array(
                    'id_usuario'        => $adicional->id_usuario,
                    'id_cartao'         => $adicional->id_cartao,
                    'id_cartao_titular' => $id_cartao,
                    'dia_vencimento'    => $dia,
                    'adicional'         => 1
                );
                $this->fatura_model->add('configs_faturas', $data_adicional);
            }
            $this->session->set_flashdata('sucesso', 'Configurações salvas com sucesso');
        }
        redirect($urlAtual);
    }

    private function criaLancamentoDespesa(int $idDespesa): bool
    {
        $detalhesDespesa = $this->despesa_model->getDespesaById($idDespesa);

        /**
         * mudar relacionamento de DB para Despesas:
         * o ID que será utilizado como vinculo na tabela Lançamentos,
         * será o ID da tabela Despesa (despesas.id) e não mais (lancamentos_despesas.id)
         *
         * implementar duas logicas diferentes para ambos os tipos de despesas: recorrente e única
         * cada lógica terá uma abordagem diferente.
         *
         * LOGICA DESPESA RECORRENTE (contas fixas a cada mes):
         * ao criar uma nova Despesa, não criar registros de Despesa automaticamente (lancamentos_despesas)
         * os registros de Despesa serao criados quando um novo Vinculo for criado (selecionar mes e ano alvos)
         * no caso de ediçao de uma Despesa, os vinculos ja criados não serão reeditados
         * no caso de exclusão de um registro de Despesa, o vinculo ja criado será excluido
         * no caso de exclusão de uma Despesa, os vinculos ja lançados no modulo de Lançamentos não serao excluidos
         *
         */

        if (!$detalhesDespesa) return false;

        $dataVencimento       = sprintf('%s-%s-%s', $this->anoReferencia, $this->mesReferencia, $detalhesDespesa->dia_vencimento);
        $newLancamentoDespesa = [
            'id_despesa'         => $detalhesDespesa->id,
            'id_forma_pagamento' => $detalhesDespesa->id_forma_pagamento,
            'data_vencimento'    => $dataVencimento,
            'mes_referencia'     => $this->mesReferencia,
            'ano_referencia'     => $this->anoReferencia,
        ];

        if ($detalhesDespesa->tipo_despesa == 2) {
            $newLancamentoDespesa['despesa_vinculada'] = 1;
        }

        if ($detalhesDespesa->despesa_parcelada) {
            for ($i = 1; $i <= $detalhesDespesa->total_parcelas; $i++) {
                (string)$contadorParcela = $i;
                $newLancamentoDespesa['num_parcela'] = $contadorParcela < 10 ? '0' . $contadorParcela : $contadorParcela;

                if (!$this->despesa_model->addLancamentoDespesa($newLancamentoDespesa)) return false;
            }
        }

        if (!$this->despesa_model->addLancamentoDespesa($newLancamentoDespesa)) return false;

        return true;
    }

    private function monitorarVinculosDespesasFromUser()
    {
        $despesas = $this->despesa_model->getDespesasAtivas();

        if ($despesas) {
            foreach ($despesas as $despesa) {
                $lancamentosDespesa = $this->despesa_model->getLancamentosDespesa($despesa->id);

                if ($lancamentosDespesa) {
                    foreach ($lancamentosDespesa as $lancamento) {
                        if ($lancamento->despesa_vinculada) {
                            $vinculo = $this->despesa_model->getVinculoLancamentoDespesa($lancamento->id);

                            if (!$vinculo) {
                                $this->copiaRegistroEmModuloLancamentos($despesa->id, $lancamento->id);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    private function editarLancamentosDespesas(int $idDespesa): bool
    {
        $detalhesDespesa = $this->despesa_model->getDespesaById($idDespesa);

        if (!$detalhesDespesa) return false;

        $lancamentosVinculadosDespesa = $this->despesa_model->getLancamentosVinculadosDespesa($idDespesa);
        $data                         = [
            'id_despesa'         => $detalhesDespesa->id,
            'id_forma_pagamento' => $detalhesDespesa->id_forma_pagamento,
        ];

        if ($detalhesDespesa->despesa_parcelada == 1) {
            $this->despesa_model->deleteLancamentosDespesa($idDespesa);

            for ($i = 1; $i <= $detalhesDespesa->total_parcelas; $i++) {
                (string)$contadorParcela = $i;
                $data['num_parcela'] = $contadorParcela < 10 ? '0' . $contadorParcela : $contadorParcela;

                if ($lancamentosVinculadosDespesa) {
                    $indexVinculo = $i - 1;

                    if (
                        isset($lancamentosVinculadosDespesa[$indexVinculo]->num_parcela)
                        && $lancamentosVinculadosDespesa[$indexVinculo]->num_parcela == $data['num_parcela']
                    ) {
                        $data['data_vencimento']   = sprintf(
                            '%s-%s-%s',
                            $lancamentosVinculadosDespesa[$indexVinculo]->ano_referencia,
                            $lancamentosVinculadosDespesa[$indexVinculo]->mes_referencia,
                            $detalhesDespesa->dia_vencimento
                        );
                        $data['mes_referencia']    = $lancamentosVinculadosDespesa[$indexVinculo]->mes_referencia;
                        $data['ano_referencia']    = $lancamentosVinculadosDespesa[$indexVinculo]->ano_referencia;
                        $data['despesa_vinculada'] = $lancamentosVinculadosDespesa[$indexVinculo]->despesa_vinculada;
                        $data['id_lancamento']     = $lancamentosVinculadosDespesa[$indexVinculo]->id_lancamento;
                    }
                }

                if (!$this->despesa_model->addLancamentoDespesa($data)) return false;

                unset($data['mes_referencia']);
                unset($data['ano_referencia']);
                unset($data['despesa_vinculada']);
                unset($data['id_lancamento']);
                unset($data['data_vencimento']);
            }
            return true;
        }

        if ($lancamentosVinculadosDespesa) {
            foreach ($lancamentosVinculadosDespesa as $lancamentoVinculo) {
                $data['data_vencimento']   = sprintf(
                    '%s-%s-%s',
                    $lancamentoVinculo->ano_referencia,
                    $lancamentoVinculo->mes_referencia,
                    $detalhesDespesa->dia_vencimento
                );
                $data['mes_referencia']    = $lancamentoVinculo->mes_referencia;
                $data['ano_referencia']    = $lancamentoVinculo->ano_referencia;
                $data['despesa_vinculada'] = $lancamentoVinculo->despesa_vinculada;
                $data['id_lancamento']     = $lancamentoVinculo->id_lancamento;

                if (!$this->despesa_model->addLancamentoDespesa($data)) return false;
            }
            return true;
        }

        if (!$this->despesa_model->editLancamentoDespesa($data)) return false;

        return true;
    }


    public function pesquisaLancamentos()
    {
        $termo                   = $this->input->post('termo');
        $data['total']           = $this->faturas_model->getTotal(getUserId());
        $data['formasPagamento'] = $this->faturas_model->getFormasPagamento();
        $data['results']         = $this->faturas_model->pesquisa($termo, getUserId());
        $data['view']            = 'financeiro/lancamentos';
        $this->load->view('tema/topo', $data);
    }

    public function terceiros()
    {
        if (!isset($_GET['nome'])) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/faturas');
        }

        $nome     = $_GET['nome'] ?? null;
        $idCartao = $_GET['cartao'] ?? null;
        $mes      = $_GET['mesReferencia'] ?? null;
        $ano      = $_GET['anoReferencia'] ?? null;

        if (!is_string($nome) || is_numeric($nome)) {
            $this->session->set_flashdata('erro', 'O nome pesquisado para o terceiro é inválido');
            redirect('financeiro/faturas?cartao=' . $idCartao);
        }

        $todayDate      = date('Y-m-d');
        $todayArray     = explode('-', $todayDate);
        $mesReferencia  = $todayArray[1];
        $anoReferencia  = $todayArray[0];
        $result         = [];
        $data['idCard'] = $idCartao;

        if ($mes) {
            $mesReferencia = $mes;
        }

        if ($ano) {
            $anoReferencia = $ano;
        }

        $faturasTerceiros = $this->fatura_model->getFaturasTerceiros(getUserId(), $nome, $mesReferencia, $anoReferencia);

        $dateFormatterExtended = new \IntlDateFormatter(
            'pt_BR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
            'America/Sao_Paulo',
            \IntlDateFormatter::GREGORIAN,
            "MMMM"
        );

        $dateObj   = DateTime::createFromFormat('!m', ($mesReferencia));
        $monthName = str_replace('.', '', strtoupper($dateFormatterExtended->format($dateObj)));

        if ($faturasTerceiros) {
            foreach ($faturasTerceiros as $fatura) {
                $lancamentosTerceiros       = $this->fatura_model->getLancamentosTerceiros(getUserId(), $fatura['id_cartao'], $nome, $mesReferencia);
                $data['lancamentoEditavel'] = $this->fatura_model->getLancamentoEditavel($fatura['mes_referencia'], $fatura['ano_referencia']);

                $dateObj   = DateTime::createFromFormat('!m', ($fatura['mes_referencia']));
                $monthName = str_replace('.', '', strtoupper($dateFormatterExtended->format($dateObj)));
                $reference = $monthName . ' / ' . $fatura['ano_referencia'];

                $result[$fatura['id_fatura']]                = $fatura;
                $result[$fatura['id_fatura']]['cartao']      = $this->cartoes_model->getDetalhesCartao($fatura['id_cartao']);
                $result[$fatura['id_fatura']]['reference']   = $reference;
                $result[$fatura['id_fatura']]['lancamentos'] = $lancamentosTerceiros;
            }
        }

        $data['results']         = $result;
        $data['name']            = $nome;
        $data['yearsList']       = $this->yearsList;
        $data['referencePeriod'] = sprintf('%s / %s', $monthName, $anoReferencia);
        $data['referenceMonth']  = $mesReferencia;
        $data['referenceYear']   = $anoReferencia;
        $data['view']            = 'faturas/lancamentos_terceiros';
        $this->load->view('tema/topo', $data);
    }

    public function autoCompleteTerceiros()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->despesa_model->autoCompleteTerceiros($q, getUserId());
        }
    }

    public function autoCompleteDescricao()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->despesa_model->autoCompleteDescricao($q, getUserId());
        }
    }

    public function autoCompleteFornecedor()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->despesa_model->autoCompleteFornecedor($q, getUserId());
        }
    }

    public function ajaxDiaVencimentoFatura()
    {
        $id_cartao = $_POST['id_cartao'];
        echo json_encode($this->fatura_model->getDiaVencimentoFatura($id_cartao));
    }


    // MODULO DE RETORNO DE FILTROS POR PERIODO
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
}
