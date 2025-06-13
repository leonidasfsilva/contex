<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Despesas extends CI_Controller
{
    protected $yearsList     = [];
    protected $mesReferencia = null;
    protected $anoReferencia = null;
    protected $redirectURL   = null;

    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $this->load->library('pagination');
        $this->redirectURL = $_SERVER['HTTP_REFERER'] ?? base_url($_SERVER['REDIRECT_URL'] ?? base_url());
        $this->yearsList   = range(2018, date('Y') + 3);

        if (ENVIRONMENT == 'production') {
            // $this->session->set_flashdata('erro', 'Módulo de Despesas em desenvolvimento.<br>Por favor, tente novamente mais tarde.');
            // redirect($this->redirectURL);
        }

        //TODO: criar uma funçao em despesa_helper para a automaçao de Despesas
        integracaoDespesasUsuario();
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
            redirect($this->redirectURL);
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
            redirect($this->redirectURL);
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
            $limit,
            $config['total_rows'],
            $config['per_page'],
            $start,
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
            redirect($this->redirectURL);
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/despesas');
        }

        $descricao        = $_POST["descricao"];
        $observacoes      = $_POST["observacoes"] ?? null;
        $autoVinculo      = isset($_POST["autoVinculo"]) ?? null;
        $valor            = $_POST["valor"];
        $fornecedor       = $_POST["fornecedor"] ?? null;
        $despesaParcelada = $_POST["despesa_parcelada"] ?? null;
        $despesaTerceiros = $_POST["despesa_terceiros"] ?? null;
        $despesaOculta    = $_POST["despesa_oculta"] ?? null;
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
            redirect($this->redirectURL);
        }

        if (!validate_money($valorParcela)) {
            $valorParcela = str_replace(array('.', ','), array('', '.'), $valorParcela);
        }

        if (!$diaVencimento) {
            $today         = date('d');
            $diaVencimento = $today;
        }

        if (abs($qntParcelas) < 10) {
            $qntParcelas = sprintf('0%s', abs($qntParcelas));
        }

        $newDespesa = [
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
            'despesa_oculta'     => $despesaOculta,
            'auto_vinculo'       => $autoVinculo,
        ];

        $this->session->set_flashdata('erro', 'Erro ao tentar registrar despesa');

        if ($this->despesa_model->add($newDespesa)) {
            $this->session->set_flashdata('sucesso', 'Despesa registrada com sucesso');
        }
        redirect($this->redirectURL);
    }

    public function copiar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para copiar despesas.');
            redirect($this->redirectURL);
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/despesas');
        }

        $descricao        = $_POST["descricao"];
        $observacoes      = $_POST["observacoes"] ?? null;
        $valor            = $_POST["valor"];
        $fornecedor       = $_POST["fornecedor"] ?? null;
        $despesaParcelada = $_POST["despesa_parcelada"] ?? null;
        $despesaTerceiros = $_POST["despesa_terceiros"] ?? null;
        $despesaOculta    = $_POST["despesa_oculta"] ?? null;
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
            $qntParcelas  = '01';
            $valorParcela = $valor;
        }

        if ($qntParcelas > 48) {
            $this->session->set_flashdata('erro', 'O parcelamento informado excede o máximo permitido (até 48x)');
            redirect($this->redirectURL);
        }

        if (!validate_money($valorParcela)) {
            $valorParcela = str_replace(array('.', ','), array('', '.'), $valorParcela);
        }

        if (!$diaVencimento) {
            $today         = date('d');
            $diaVencimento = $today;
        }

        if (abs($qntParcelas) < 10) {
            $qntParcelas = sprintf('0%s', abs($qntParcelas));
        }

        $dataToDbPersist = [
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
            'despesa_oculta'     => $despesaOculta,
        ];

        $this->session->set_flashdata('erro', 'Erro ao tentar copiar despesa');

        if ($this->despesa_model->add($dataToDbPersist)) {
            $this->session->set_flashdata('sucesso', 'Despesa copiada com sucesso');
        }
        redirect($this->redirectURL);
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar despesas.');
            redirect($this->redirectURL);
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/despesas');
        }

        $idDespesa        = $_POST["id_despesa"];
        $descricao        = $_POST["descricao"];
        $observacoes      = $_POST["observacoes"] ?? null;
        $valor            = $_POST["valor"];
        $fornecedor       = $_POST["fornecedor"] ?? null;
        $despesaParcelada = $_POST["despesa_parcelada"] ?? null;
        $despesaTerceiros = $_POST["despesa_terceiros"] ?? null;
        $despesaOculta    = $_POST["despesa_oculta"] ?? null;
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
            redirect($this->redirectURL);
        }

        if (!validate_money($valorParcela)) {
            $valorParcela = str_replace(array('.', ','), array('', '.'), $valorParcela);
        }

        if (!$diaVencimento) {
            $today         = date('d');
            $diaVencimento = $today;
        }

        $despesaUpdate = [
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
            'despesa_oculta'     => $despesaOculta,
        ];

        if ($this->despesa_model->edit($despesaUpdate, 'id', $idDespesa)) {
            $this->session->set_flashdata('sucesso', 'Despesa alterada com sucesso');
            redirect($this->redirectURL);
        }
        $this->session->set_flashdata('erro', 'Erro ao tentar alterar despesa');
        redirect($this->redirectURL);
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir despesas.');
            redirect($this->redirectURL);
        }

        $idDespesa = $this->input->post('idDespesa');

        if (!$idDespesa) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/despesas');
        }

        if (!$this->despesa_model->deleteDespesa($idDespesa)) {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir despesa');
            redirect($this->redirectURL);
        }
        $this->session->set_flashdata('sucesso', 'Despesa excluída com sucesso');
        redirect($this->redirectURL);
    }

    public function excluirLancamento()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir registro de despesas.');
            redirect($this->redirectURL);
        }

        $idLancamentoDespesa = $this->input->post('idLancamentoDespesa') ?? null;

        if (!$idLancamentoDespesa) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($this->redirectURL);
        }

        $registroDespesa = $this->despesa_model->getLancamentoDespesaById($idLancamentoDespesa);

        if ($this->despesa_model->deleteLancamentoDespesa($idLancamentoDespesa)) {
            $this->excluiRegistroEmModuloLancamentos($registroDespesa->id_despesa, $registroDespesa->data_vencimento);
            $this->session->set_flashdata('sucesso', 'Registro excluído com sucesso');
            redirect($this->redirectURL);
        }

        $this->session->set_flashdata('erro', 'Erro ao tentar excluir registro de despesa');
        redirect($this->redirectURL);
    }

    public function excluirSerieLancamentos()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir registro de despesas.');
            redirect($this->redirectURL);
        }

        $idsArray = $this->input->post('id');
        $result   = false;

        if (is_array($idsArray) && count($idsArray) > 0) {
            foreach ($idsArray as $id) {
                $lancamento = $this->despesa_model->getLancamentoDespesaById($id);
                $result     = $this->despesa_model->deleteLancamentoDespesa($id);
                $this->excluiRegistroEmModuloLancamentos($lancamento->id_despesa, $lancamento->data_vencimento);
            }

            if (!$result) {
                $this->session->set_flashdata('erro', 'Erro ao tentar excluir série de registros');
                redirect($this->redirectURL);
            }
            $this->session->set_flashdata('sucesso', 'Registros excluídos com sucesso');
            redirect($this->redirectURL);
        }
        $this->session->set_flashdata('erro', 'Método não permitido');
        redirect($this->redirectURL);
    }

    public function pagar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para pagar despesas.');
            redirect($this->redirectURL);
        }
        $idLancamentoDespesa = $this->input->post('idLancamentoDespesa');
        $formaPagamento      = $this->input->post('formaPagamento');
        $dataPagamento       = date('d/m/Y');

        $registroDespesa = $this->despesa_model->getDetalhesLancamentoDespesa($idLancamentoDespesa);

        if ($_REQUEST['dataPagamento']) {
            $dataPagamento = $this->input->post('dataPagamento');
        }

        $dataPagamento = explode('/', $dataPagamento);
        $dataPagamento = $dataPagamento[2] . '-' . $dataPagamento[1] . '-' . $dataPagamento[0];

        $data = array(
            'id'                 => $idLancamentoDespesa,
            'registro_pago'      => 1,
            'data_pagamento'     => $dataPagamento,
            'id_forma_pagamento' => $formaPagamento
        );

        if ($this->despesa_model->editLancamentoDespesa($data)) {
            $vinculo = $this->despesa_model->getVinculoDespesaComModuloLancamentos($registroDespesa->id_despesa, $registroDespesa->data_vencimento);

            if ($vinculo) {
                $data = [
                    'baixado'    => 1,
                    'forma_pgto' => $formaPagamento,
                ];
                $this->financeiro_model->edit('lancamentos', $data, 'id_despesa', $registroDespesa->id_despesa);
            }
            $this->session->set_flashdata('sucesso', 'Registro de despesa pago com sucesso');
            redirect($this->redirectURL);
        }

        $this->session->set_flashdata('erro', 'Erro ao tentar pagar registro de despesa');
        redirect($this->redirectURL);
    }

    public function ativar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para ativar integração de despesas.');
            redirect($this->redirectURL);
        }

        if ($this->input->post('id')) $id = $this->input->post('id');

        if (!$id) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($this->redirectURL);
        }

        if (!$this->despesa_model->ativaDespesa($id)) {
            $this->session->set_flashdata('erro', 'Erro ao tentar ativar integração automática');
            redirect($this->redirectURL);
        }
        $this->session->set_flashdata('sucesso', 'Integração automática ativada com sucesso');
        redirect($this->redirectURL);
    }

    public function desativar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para desativar integração de despesas.');
            redirect($this->redirectURL);
        }

        if ($this->input->post('id')) $id = $this->input->post('id');

        if (!$id) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($this->redirectURL);
        }

        if (!$this->despesa_model->desativaDespesa($id)) {
            $this->session->set_flashdata('erro', 'Erro ao tentar desativar integração automática');
            redirect($this->redirectURL);
        }
        $this->session->set_flashdata('sucesso', 'Integração automática desativada com sucesso');
        redirect($this->redirectURL);
    }

    public function registrarLancamentoDespesa()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para vincular despesas.');
            redirect($this->redirectURL);
        }

        $vincular            = isset($_POST["vincular"]) ?? null;
        $this->mesReferencia = $this->input->post('mesReferencia') ?? null;
        $this->anoReferencia = $this->input->post('anoReferencia') ?? null;
        $idDespesa           = $this->input->post('idDespesa');
        $despesa             = $this->despesa_model->getDespesaById($idDespesa);
        $dataReferencia      = sprintf('%s-%s-%s', $this->anoReferencia, $this->mesReferencia, $despesa->dia_vencimento);
        $registroDespesa     = $this->despesa_model->getLancamentoDespesaByVencimento($idDespesa, $dataReferencia);

        if (!$despesa) {
            $this->session->set_flashdata('erro', 'Despesa não encontrada');
            redirect($this->redirectURL);
        }

        if ($registroDespesa) {
            $this->session->set_flashdata('erro', 'Registro já existente para o período solicitado');
            redirect($this->redirectURL);
        }

        if (!criaLancamentoDespesa($idDespesa, $dataReferencia)) {
            $this->session->set_flashdata('erro', 'Erro ao tentar adicionar registro de despesa');
            redirect($this->redirectURL);
        }

        if ($vincular) {
            if (!copiaRegistroEmModuloLancamentos($idDespesa, $dataReferencia)) {
                $this->session->set_flashdata('erro', 'Erro ao tentar vincular despesa');
                redirect($this->redirectURL);
            }
        }

        $this->session->set_flashdata('sucesso', 'Registro adicionado com sucesso');
        redirect($this->redirectURL);
    }

    private function excluiRegistroEmModuloLancamentos($idDespesa, $dataReferencia = null)
    {
        $where = [
            'id_despesa'      => $idDespesa,
            'data_lancamento' => $dataReferencia,
        ];

        $data = [
            'status' => 0
        ];

        if (!$this->despesa_model->excluiRegistroEmModuloLancamentos($where, $data))
            return false;

        return true;
    }

    public function desvincularDespesas()
    {
        //TODO: avaliar a real necessidade de permitir a desvinculação de despesas
        // (desvincular um lote de despesas, botao disponivel apenas na view gerenciar_despesas)
    }

    public function gerenciarLoteVinculos()
    {
        //TODO: estudar a real necessidade de um metodo para gerenciar vinculos de despesas
        // (vincular um lote de despesas, botao disponivel apenas na view gerenciar_despesas)
    }

    public function vincular()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para vincular despesas.');
            redirect($this->redirectURL);
        }
        $idDespesa      = $this->input->post('idDespesa');
        $idRegistro     = $this->input->post('idRegistro');
        $dataVencimento = $this->input->post('dataVencimento');

        if ($this->despesa_model->setFlagRegistroVinculado($idRegistro)) {
            if (!copiaRegistroEmModuloLancamentos($idDespesa, $dataVencimento)) {
                $this->session->set_flashdata('erro', 'Erro ao tentar vincular o registro da despesa');
                redirect($this->redirectURL);
            }

            $this->session->set_flashdata('sucesso', 'Registro vinculado com sucesso');
            redirect($this->redirectURL);
        }

        $this->session->set_flashdata('erro', 'Erro ao tentar vincular o registro da despesa');
        redirect($this->redirectURL);
    }

    public function configurar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar despesas.');
            redirect($this->redirectURL);
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
        redirect($this->redirectURL);
    }

    private function monitaIntegracaoAtivaComModuloLancamentos()
    {

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

        if ($detalhesDespesa->despesa_parcelada) {
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
                        $data['data_vencimento']    = sprintf(
                            '%s-%s-%s',
                            $lancamentosVinculadosDespesa[$indexVinculo]->ano_referencia,
                            $lancamentosVinculadosDespesa[$indexVinculo]->mes_referencia,
                            $detalhesDespesa->dia_vencimento
                        );
                        $data['mes_referencia']     = $lancamentosVinculadosDespesa[$indexVinculo]->mes_referencia;
                        $data['ano_referencia']     = $lancamentosVinculadosDespesa[$indexVinculo]->ano_referencia;
                        $data['registro_vinculado'] = $lancamentosVinculadosDespesa[$indexVinculo]->registro_vinculado;
                        $data['id_lancamento']      = $lancamentosVinculadosDespesa[$indexVinculo]->id_lancamento;
                    }
                }

                if (!$this->despesa_model->addLancamentoDespesa($data)) return false;

                unset($data['mes_referencia']);
                unset($data['ano_referencia']);
                unset($data['registro_vinculado']);
                unset($data['id_lancamento']);
                unset($data['data_vencimento']);
            }
            return true;
        }

        if ($lancamentosVinculadosDespesa) {
            foreach ($lancamentosVinculadosDespesa as $lancamentoVinculo) {
                $data['data_vencimento']    = sprintf(
                    '%s-%s-%s',
                    $lancamentoVinculo->ano_referencia,
                    $lancamentoVinculo->mes_referencia,
                    $detalhesDespesa->dia_vencimento
                );
                $data['mes_referencia']     = $lancamentoVinculo->mes_referencia;
                $data['ano_referencia']     = $lancamentoVinculo->ano_referencia;
                $data['registro_vinculado'] = $lancamentoVinculo->registro_vinculado;
                $data['id_lancamento']      = $lancamentoVinculo->id_lancamento;

                if (!$this->despesa_model->addLancamentoDespesa($data)) return false;
            }
            return true;
        }

        if (!$this->despesa_model->editLancamentosDespesa($data)) return false;

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
}
