<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Faturas extends CI_Controller
{
    protected $yearsList = [];

    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
        $this->load->library('pagination');
        $this->yearsList = range(2018, date('Y') + 3);
        vinculoAutomaticoFaturas();
        vinculoAutomaticoComprasTerceiros();
    }

    public function index()
    {
        $this->faturas();
    }

    // MODULO DE FATURAS
    public function faturas()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar faturas.');
            redirect(base_url());
        }
        $where      = '';
        $periodo    = $this->input->get('periodo');
        $status     = $this->input->get('status');
        $pagamento  = $this->input->get('pagamento');
        $inicio     = $this->input->get('dataInicial');
        $fim        = $this->input->get('dataFinal');
        $cliente    = $this->input->get('terceiro');
        $start      = $_GET['per_page'] ?? null;
        $limit      = null;
        $idCartao   = null;

        $order_by = [
            'ano_referencia' => 'desc',
            'mes_referencia' => 'desc',
        ];

        if ($this->input->get('cartao')) {
            $idCartao = $this->input->get('cartao');
        }

        $query_string = null;
        $lastElement  = end($_GET);

        foreach ($_GET as $key => $value) {
            if ($key != 'per_page') {
                if ($value == $lastElement) {
                    $query_string .= $key . '=' . $value;
                } else {
                    $query_string .= $key . '=' . $value . '&';
                }
            }
        }

        $whereFiltros = [
            'status = 1',
            'id_usuario = ' . getUserId(),
        ];

        if ($status == 'aberta') {
            $whereFiltros[] = 'fatura_aberta = 1';
        } elseif ($status == 'fechada') {
            $whereFiltros[] = 'fatura_aberta = 0';
        } elseif ($status == 'futura') {
            $whereFiltros[] = 'fatura_aberta = 2';
        }

        if ($pagamento == 'paga') {
            $whereFiltros[] = 'fatura_paga = 1';
        } elseif ($pagamento == 'pendente') {
            $whereFiltros[] = 'fatura_paga = 2';
        }

        if ($periodo == 'especifico' && $inicio && $fim) {
            $inicioFiltro = explode('/', $inicio);
            $fimFiltro    = explode('/', $fim);

            if (count($inicioFiltro) == 3 && count($fimFiltro) == 3) {
                $inicioFiltro   = $inicioFiltro[2] . '-' . $inicioFiltro[1] . '-' . $inicioFiltro[0];
                $fimFiltro      = $fimFiltro[2] . '-' . $fimFiltro[1] . '-' . $fimFiltro[0];
                $whereFiltros[] = 'STR_TO_DATE(CONCAT(ano_referencia, "-", mes_referencia, "-01"), "%Y-%m-%d") BETWEEN "' . $inicioFiltro . '" AND "' . $fimFiltro . '"';
            }
        }

        $where = implode(' AND ', $whereFiltros);

        $config['base_url']          = base_url('financeiro/faturas/');
        $config['suffix']            = '&' . $query_string;
        $config['first_url']         = $config['base_url'] . '?' . $query_string;
        $config['total_rows']        = $this->fatura_model->count('faturas', $where, $idCartao);
        $config['per_page']          = 13;
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

        $data['parcelas'] = array(
            2  => '2 x',
            3  => '3 x',
            4  => '4 x',
            5  => '5 x',
            6  => '6 x',
            7  => '7 x',
            8  => '8 x',
            9  => '9 x',
            10 => '10 x',
            11 => '11 x',
            12 => '12 x',
        );

        $cartaoPrincipal = $this->cartoes_model->getCartaoPrincipalUsuario(getUserId());

        if ($idCartao) {
            $cartao = $this->cartoes_model->cartaoExistente($idCartao);

            if ($cartao->id_usuario != getUserId()) {
                if ($cartao->id_usuario_titular != getUserId()) {
                    $this->session->set_flashdata('erro', 'Cartão solicitado não encontrado');
                    redirect('financeiro/faturas');
                }
            }

            if (!$cartao->ativo) {
                $this->session->set_flashdata('erro', 'Cartão solicitado encontra-se inativo, reative o cartão para acessar as faturas');
                redirect('financeiro/faturas');
            }
        } else {
            $idCartao = $cartaoPrincipal->id_cartao ?? null;
        }

        $data['cartoes']             = null;
        $data['faturaAberta']        = null;
        $data['existe_configuracao'] = null;
        $data['saldoVencidas']       = null;
        $data['saldoQuitado']        = null;

        if ($idCartao) {
            $cartaoSelecionado = $this->cartoes_model->getDetalhesCartao($idCartao);

            $n_cartao                                 = explode(" ", trim(decriptar($cartaoSelecionado['numero'])));
            $final                                    = $n_cartao[3];
            $cartao_config                            = $cartaoSelecionado['apelido'] ? $cartaoSelecionado['apelido'] : $cartaoSelecionado['bandeira'];
            $data['cartaoSelecionado']                = $cartaoSelecionado;
            $data['cartaoSelecionado']['cartaoLabel'] = sprintf('%s - %s', $cartao_config, $final);


            $data['yearsList']           = $this->yearsList;
            $data['autoLinkInvoices']    = $this->fatura_model->getAutoLinkUser();
            $data['existe_configuracao'] = $this->fatura_model->existeConfiguracao($idCartao);
            $data['dia_vencimento']      = $this->fatura_model->getDiaVencimentoFatura($idCartao);
            $data['cartoes']             = $this->cartoes_model->getCartoesAtivosUsuario(getUserId());
            $data['saldoVencidas']       = $this->fatura_model->getSaldoFaturasVencidas($idCartao);
            $data['saldoPendente']       = $this->fatura_model->getSaldoFaturasPendentes($idCartao);
            $data['saldoQuitado']        = $this->fatura_model->getSaldoFaturasPagas($idCartao);
            $data['formasPagamento']     = $this->financeiro_model->getFormasPagamento();
            $data['faturaAberta']        = $this->fatura_model->getFaturaAbertaUsuario(getUserId(), $idCartao);
            $data['results']             = $this->fatura_model->get(
                'faturas',
                '*',
                $idCartao,
                $where,
                $limit,
                $config['total_rows'],
                $config['per_page'],
                $start,
                $order_by ?: 'desc'
            );
        }

        $data['menuFinanceiro'] = true;
        $data['view']           = 'faturas/gerenciar_faturas';

        $this->load->view('tema/topo', $data);
    }

    public function detalhes($id = null, $idCartao = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerenciar faturas.');
            redirect(base_url());
        }

        if ($this->input->post('id_fatura')) {
            $id_fatura = $this->input->post('id_fatura');
        } elseif ($this->input->get('fatura')) {
            $id_fatura = $this->input->get('fatura');
        } else {
            $id_fatura = $id;
        }

        $urlAtual = $this->input->post('urlAtual');
        $periodo  = $this->input->get('periodo');
        $terceiro = $this->input->get('terceiro');
        $where    = null;

        $this->load->library('pagination');

        $config['base_url']          = site_url() . 'faturas/';
        $config['total_rows']        = $this->fatura_model->count('faturas', 'status = 1 AND id_usuario = ' . getUserId(), $idCartao);
        $config['per_page']          = 0;
        $config['page_query_string'] = true;
        $config['next_link']         = 'Próxima';
        $config['prev_link']         = 'Anterior';
        $config['full_tag_open']     = '<div class="pagination alternate"><ul>';
        $config['full_tag_close']    = '</ul></div>';
        $config['num_tag_open']      = '<li>';
        $config['num_tag_close']     = '</li>';
        $config['cur_tag_open']      = '<li><a style="color: #2D335B"><b>';
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

        $data['parcelas'] = array(
            2  => '2 x',
            3  => '3 x',
            4  => '4 x',
            5  => '5 x',
            6  => '6 x',
            7  => '7 x',
            8  => '8 x',
            9  => '9 x',
            10 => '10 x',
            11 => '11 x',
            12 => '12 x',
        );

        if (!$id_fatura) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/faturas');
        }

        $faturaExistente = $this->fatura_model->getById($id_fatura);

        if (!$faturaExistente) {
            $this->session->set_flashdata('erro', 'Fatura solicitada não encontrada');
            redirect('financeiro/faturas');
        }

        $terceirosNotAllowed = ['nenhum', 'todos'];

        if (isset($terceiro) && $terceiro != null) {
            if (!in_array($terceiro, $terceirosNotAllowed)) {
                $where = sprintf("nome_cliente = '%s'", $terceiro);
            }

            if (in_array($terceiro, $terceirosNotAllowed)) {
                if ($terceiro == 'nenhum') {
                    $where = "compra_terceiros = 0";
                }

                if ($terceiro == 'todos') {
                    $where = "compra_terceiros = 1";
                }
            }
        }

        $dateFormatter = new \IntlDateFormatter(
            'pt_BR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
            'America/Sao_Paulo',
            \IntlDateFormatter::GREGORIAN,
            "MMM"
        );

        $dateFormatterExtended = new \IntlDateFormatter(
            'pt_BR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
            'America/Sao_Paulo',
            \IntlDateFormatter::GREGORIAN,
            "MMMM"
        );

        $fatura_selecionada = $this->fatura_model->getFatura($id_fatura);
        $detalhesCartao     = $this->cartoes_model->getDetalhesCartao($idCartao);

        if (($fatura_selecionada->id_usuario != getUserId() && $detalhesCartao['id_usuario'] != getUserId())
            || ($fatura_selecionada->id_cartao != $idCartao && $detalhesCartao['id_usuario_titular'] != getUserId())) {
            $this->session->set_flashdata('erro', 'Fatura solicitada não encontrada para este usuário');
            redirect('financeiro/faturas');
        }

        $detalhesFatura = $this->fatura_model->getDetalhesFatura($id_fatura);
        $dateObj        = DateTime::createFromFormat('!m', ($detalhesFatura->mes_referencia));
        $nomeMes        = str_replace('.', '', mb_strtoupper($dateFormatterExtended->format($dateObj)));

        $numCartao              = explode(" ", trim(decriptar($detalhesCartao['numero'])));
        $final                  = $numCartao[3];
        $cartaoAlternativeLabel = $detalhesCartao['bandeira'] . ' - FINAL ' . $final;

        $orderByLancamentosAssoc = [
            'data_compra' => 'desc',
            'id_assoc'    => 'desc',
        ];

        $orderByLancamentos = [
            'id_lancamento' => 'desc',
        ];

        $faturasLancaveis = $this->fatura_model->get(
            'faturas',
            '*',
            $idCartao,
            'fatura_aberta != 0',
        );

        $data['terceiros']          = $this->fatura_model->getAllTerceiros($idCartao, $fatura_selecionada->mes_referencia, $fatura_selecionada->ano_referencia);
        $data['selectedTerceiro']   = $terceiro;
        $data['fatura']             = $detalhesFatura;
        $data['id_fatura']          = $id_fatura;
        $data['id_cartao']          = $idCartao;
        $data['cartao']             = $detalhesCartao;
        $data['nomeMes']            = $nomeMes;
        $data['alternativeLabel']   = $cartaoAlternativeLabel;
        $data['mes_referencia']     = ($data['fatura']->mes_referencia);
        $data['ano_referencia']     = ($data['fatura']->ano_referencia);
        $data['status_fatura']      = ($data['fatura']->fatura_aberta);
        $data['id_usuario']         = ($data['fatura']->id_usuario);
        $data['fatura_paga']        = ($data['fatura']->fatura_paga);
        $data['faturasLancaveis']   = $this->getTranslateMonth($faturasLancaveis);
        $data['formasPagamento']    = $this->financeiro_model->getFormasPagamento();
        $data['lancamentoEditavel'] = $this->fatura_model->getLancamentoEditavel($data['mes_referencia'], $data['ano_referencia']);
        $data['subresults']         = $this->fatura_model->getLancamentos('lancamentos_faturas', '*', $fatura_selecionada->id_usuario, $where, $config['per_page'], $this->input->get('per_page'), $orderByLancamentos);
        $data['results']            = $this->fatura_model->getLancamentosAssoc('lancamentos_faturas_assoc', '*', $id_fatura, $where = null, $config['per_page'], $this->input->get('per_page'), $orderByLancamentosAssoc);
        $data['menuFinanceiro']     = true;
        $data['menuFaturas']        = true;

        $data['view'] = 'faturas/detalhes_fatura';
        $this->load->view('tema/topo', $data);
    }

    public function novoLancamento($id_fatura = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar novos lançamentos de faturas.');
            redirect(base_url());
        }

        if (!$_POST || $id_fatura == null) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('financeiro/faturas');
        }

        $urlAtual      = $this->input->post('urlAtual');
        $valor         = $this->input->post('valor');
        $valor_parcela = $this->input->post('valor_parcela');
        $observacoes   = $this->input->post('observacoes');

        if ($this->input->post('qnt_parcelas')) {
            $qnt_parcelas = $this->input->post('qnt_parcelas');
        } else {
            $qnt_parcelas = 1;
        }

        if ($this->input->post('compra_parcelada')) {
            $compra_parcelada = 1;
        } else {
            $compra_parcelada = 0;
        }

        if ($this->input->post('compra_terceiros')) {
            $compra_terceiros = 1;
        } else {
            $compra_terceiros = 0;
        }

        if ($this->input->post('estorno')) {
            $estorno = 1;
            $valor   = '-' . $valor;
        } else {
            $estorno = 0;
        }

        if ($this->input->post('data_compra')) {
            $data_compra = $this->input->post('data_compra');
            $data_compra = explode('/', $data_compra);
            $data_compra = $data_compra[2] . '-' . $data_compra[1] . '-' . $data_compra[0];
        } else {
            $data_compra = date('Y/m/d');
        }

        if (!validate_money($valor_parcela)) {
            $valor_parcela = str_replace(array('.', ','), array('', '.'), $valor_parcela);
        }

        if (!validate_money($valor)) {
            $valor = str_replace(array('.', ','), array('', '.'), $valor);
        }

        $faturaExistente = $this->fatura_model->getFaturaUsuario($id_fatura, getUserId());

        if ($faturaExistente) {
            $faturaAtual = $this->fatura_model->getFaturaAtual($id_fatura);
            $mes         = $faturaAtual->mes_referencia;
            $ano         = $faturaAtual->ano_referencia;

            // ARRAY LANCAMENTOS_FATURAS
            $data = array(
                'id_fatura'        => $faturaAtual->id_fatura,
                // 'id_cliente'        => $this->input->post('id_cliente') ?: null,
                'id_usuario'       => $faturaAtual->id_usuario,
                'descricao'        => padronizarString($this->input->post('descricao')),
                'observacoes'      => $observacoes ?? null,
                'nome_cliente'     => $this->input->post('nome_cliente') ? padronizarString($this->input->post('nome_cliente')) : null,
                'valor_total'      => $valor,
                'total_parcelas'   => $qnt_parcelas,
                'compra_parcelada' => $compra_parcelada,
                'compra_terceiros' => $compra_terceiros,
                'estorno'          => $estorno,
                'data_compra'      => $data_compra,
                'mes_referencia'   => $mes,
                'ano_referencia'   => $ano,
            );

            if ($this->fatura_model->add('lancamentos_faturas', $data)) {
                $last_id = $this->fatura_model->insert_id('lancamentos_faturas');

                // COMPRA PARCELADA
                if ($this->input->post('compra_parcelada') == 1) {
                    for ($x = 1; $x <= $qnt_parcelas; $x++) {

                        // CONSULTA SE EXISTE FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                        $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAtual->id_cartao, $mes, $ano);

                        // CASO NÃO EXISTA, O SISTEMA CRIA A FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                        if (!$faturaReferencia) {
                            $ultimaFatura = $this->fatura_model->getUltimaFatura($faturaAtual->id_cartao);

                            $vencimento = $ultimaFatura->vencimento;
                            $vencimento = explode('-', $vencimento);
                            $dia_venc   = $vencimento[2];
                            $mes_venc   = $vencimento[1] + 1;
                            $ano_venc   = $vencimento[0];

                            if ($mes_venc == 13) {
                                $mes_venc = 01;
                                $ano_venc++;
                            }
                            $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                            // ARRAY ABRIR NOVA FATURA
                            $data = array(
                                'id_usuario'     => $ultimaFatura->id_usuario,
                                'id_cartao'      => $ultimaFatura->id_cartao,
                                'mes_referencia' => $mes,
                                'ano_referencia' => $ano,
                                'vencimento'     => $vencimentoFormatado,
                                'fatura_aberta'  => 2,
                            );

                            if (!$this->fatura_model->abrirFatura($data)) {
                                $this->session->set_flashdata('erro', 'Erro ao tentar abrir nova fatura.');
                                redirect($urlAtual);
                            }
                            $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAtual->id_cartao, $mes, $ano);
                        }

                        // ARRAY LANCAMENTOS_FATURAS_ASSOC
                        $lancamentosFaturasAssocArray = array(
                            'id_lancamento'  => $last_id,
                            'id_fatura'      => $faturaReferencia->id_fatura,
                            'valor_parcela'  => $valor_parcela,
                            'valor_total'    => $valor,
                            'mes_referencia' => $mes,
                            'ano_referencia' => $ano,
                            'data_compra'    => $data_compra,
                            'n_parcela'      => $x,
                            'total_parcelas' => $qnt_parcelas,
                        );

                        // COMPRA DE TERCEIROS
                        if ($compra_terceiros == 1) {

                            $vencimento = $faturaReferencia->vencimento;
                            $vencimento = explode('-', $vencimento);
                            $dia_venc   = $vencimento[2];
                            $mes_venc   = $vencimento[1];
                            $ano_venc   = $vencimento[0];

                            if ($mes_venc == 13) {
                                $mes_venc = 01;
                                $ano_venc++;
                            }
                            $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                            if ($x > 9) {
                                $parcela_atual = $x;
                            } else {
                                $parcela_atual = '0' . $x;
                            }

                            if ($qnt_parcelas > 9) {
                                $total_parcelas = $qnt_parcelas;
                            } else {
                                $total_parcelas = '0' . $qnt_parcelas;
                            }

                            // MONTA ARRAY DE PENDENCIAS
                            $data2 = array(
                                'id_lancamento_fatura' => $last_id,
                                'id_usuario'           => getUserId(),
                                'id_cliente'           => $this->input->post('id_cliente'),
                                'descricao'            => padronizarString($this->input->post('descricao')) . ' - ' . $parcela_atual . '/' . $total_parcelas,
                                'tipo'                 => 1,
                                'valor'                => $valor_parcela,
                                'data_vencimento'      => $vencimentoFormatado,
                            );
                            // removendo adicao de compras parceladas de terceiros em Pendencias
                            //$this->pendencia_model->add('pendencias', $data2);
                        }

                        if ($this->fatura_model->add('lancamentos_faturas_assoc', $lancamentosFaturasAssocArray)) {
                            atualizaValorVinculoFaturas($faturaReferencia->id_fatura);
                            $this->session->set_flashdata('sucesso', 'Lançamento adicionado com sucesso');
                        } else {
                            $this->session->set_flashdata('erro', 'Erro ao tentar adicionar lançamentos_assoc!');
                            redirect($urlAtual);
                        }

                        $mes++;
                        if ($mes == 13) {
                            $mes = 01;
                            $ano++;
                        }
                    }
                    redirect($urlAtual);
                } else {
                    // COMPRA A VISTA
                    // ARRAY LANCAMENTOS_FATURA_ASSOC
                    $lancamentosFaturasAssocArray = array(
                        'id_lancamento'  => $last_id,
                        'id_fatura'      => $faturaAtual->id_fatura,
                        'valor_parcela'  => $valor,
                        'valor_total'    => $valor,
                        'mes_referencia' => $mes,
                        'ano_referencia' => $ano,
                        'data_compra'    => $data_compra,
                        'n_parcela'      => 1,
                        'total_parcelas' => 1,
                    );

                    // MONTA ARRAY DE PENDENCIA, NO CASO DE COMPRA DE TERCEIROS
                    if ($compra_terceiros == 1) {

                        $vencimento = $faturaAtual->vencimento;
                        $vencimento = explode('-', $vencimento);
                        $dia_venc   = $vencimento[2];
                        $mes_venc   = $vencimento[1];
                        $ano_venc   = $vencimento[0];

                        if ($mes_venc == 13) {
                            $mes_venc = 01;
                            $ano_venc++;
                        }
                        $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                        $data2 = array(
                            'id_lancamento_fatura' => $last_id,
                            'id_usuario'           => getUserId(),
                            'id_cliente'           => $this->input->post('id_cliente'),
                            'descricao'            => padronizarString($this->input->post('descricao')),
                            'tipo'                 => 1,
                            'valor'                => $valor,
                            'data_vencimento'      => $vencimentoFormatado,
                        );
                        // removendo adicao de compras a vista de terceiros em Pendencias
                        //$this->pendencia_model->add('pendencias', $data2);
                    }

                    if ($this->fatura_model->add('lancamentos_faturas_assoc', $lancamentosFaturasAssocArray)) {
                        atualizaValorVinculoFaturas($id_fatura);
                        $this->session->set_flashdata('sucesso', 'Lançamento adicionado com sucesso');
                    } else {
                        $this->session->set_flashdata('erro', 'Erro ao tentar adicionar lançamentos_assoc!');
                        redirect($urlAtual);
                    }
                }
                redirect($urlAtual);
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar criar novo lançamento de fatura.');
                redirect($urlAtual);
            }
        } else {
            $this->session->set_flashdata('erro', 'Não existem faturas abertas, não é possível lançar esta compra.');
            redirect('financeiro/faturas');
        }
    }

    public function editarLancamento($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar lançamentos de faturas.');
            redirect(base_url());
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('financeiro/faturas');
        }

        if ($_POST['id_fatura']) {
            $id_fatura = $_POST['id_fatura'];
        } else {
            $id_fatura = $id;
        }

        $urlAtual      = $this->input->post('urlAtual');
        $valor         = $this->input->post('valor');
        $valor_parcela = $this->input->post('valor_parcela');
        $id_lancamento = $this->input->post('id_lancamento');
        $observacoes   = $this->input->post('observacoes');
        $mapaVinculosTerceiros = $this->fatura_model->getMapaVinculosTerceiroPorCompra($id_lancamento, getUserId());

        if ($this->input->post('compra_parcelada') == 1) {
            $qnt_parcelas = $this->input->post('qnt_parcelas');
        } else {
            $qnt_parcelas = 1;
        }

        if ($this->input->post('estorno')) {
            $estorno = 1;
            $valor   = '-' . $valor;
        } else {
            $estorno = 0;
        }

        if ($this->input->post('compra_terceiros')) {
            $compra_terceiros = 1;
        } else {
            $compra_terceiros = null;
        }

        if ($this->input->post('data_compra')) {
            $data_compra = $this->input->post('data_compra');
            $data_compra = explode('/', $data_compra);
            $data_compra = $data_compra[2] . '-' . $data_compra[1] . '-' . $data_compra[0];
        } else {
            $data_compra = date('Y/m/d');
        }

        if (!validate_money($valor_parcela)) {
            $valor_parcela = str_replace(array('.', ','), array('', '.'), $valor_parcela);
        }

        if (!validate_money($valor)) {
            $valor = str_replace(array('.', ','), array('', '.'), $valor);
        }

        $faturaExistente = $this->fatura_model->getFaturaUsuario($id_fatura, getUserId());

        if ($faturaExistente) {
            $faturaAtual = $this->fatura_model->getFaturaAtual($id_fatura);
            $mes         = $faturaAtual->mes_referencia;
            $ano         = $faturaAtual->ano_referencia;

            // ARRAY LANCAMENTOS_FATURAS
            $data = array(
                'id_fatura'        => $faturaAtual->id_fatura,
                'id_usuario'       => getUserId(),
                'descricao'        => padronizarString($this->input->post('descricao')),
                'observacoes'      => $observacoes ?? null,
                'valor_total'      => $valor,
                'total_parcelas'   => $qnt_parcelas,
                'compra_parcelada' => $this->input->post('compra_parcelada'),
                'estorno'          => $estorno,
                'data_compra'      => $data_compra,
                'mes_referencia'   => $mes,
                'ano_referencia'   => $ano,
                // 'id_cliente' => $this->input->post('id_cliente'),
                'nome_cliente'     => $compra_terceiros ? padronizarString($this->input->post('nome_cliente')) : null,
                'compra_terceiros' => $compra_terceiros,
            );

            if ($this->fatura_model->edit('lancamentos_faturas', $data, 'id_lancamento', $id_lancamento)) {

                $this->fatura_model->delete_real('lancamentos_faturas_assoc', 'id_lancamento', $id_lancamento);
                $this->fatura_model->delete_real('pendencias', 'id_lancamento_fatura', $id_lancamento);

                // COMPRA PARCELADA
                if ($this->input->post('compra_parcelada') == 1) {
                    for ($x = 1; $x <= $qnt_parcelas; $x++) {

                        // CONSULTA SE EXISTE FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                        $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAtual->id_cartao, $mes, $ano);

                        // CASO NÃO EXISTA, O SISTEMA CRIA A FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                        if (!$faturaReferencia) {
                            $ultimaFatura = $this->fatura_model->getUltimaFatura($faturaAtual->id_cartao);

                            $vencimento = $ultimaFatura->vencimento;
                            $vencimento = explode('-', $vencimento);
                            $dia_venc   = $vencimento[2];
                            $mes_venc   = $vencimento[1] + 1;
                            $ano_venc   = $vencimento[0];

                            if ($mes_venc == 13) {
                                $mes_venc = 01;
                                $ano_venc++;
                            }
                            $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                            // ARRAY ABRIR NOVA FATURA
                            $data = array(
                                'id_usuario'     => $ultimaFatura->id_usuario,
                                'id_cartao'      => $ultimaFatura->id_cartao,
                                'mes_referencia' => $mes,
                                'ano_referencia' => $ano,
                                'vencimento'     => $vencimentoFormatado,
                                'fatura_aberta'  => 2,
                            );

                            if (!$this->fatura_model->abrirFatura($data)) {
                                $this->session->set_flashdata('erro', 'Erro ao tentar abrir nova fatura.');
                                redirect($urlAtual);
                            }
                            $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAtual->id_cartao, $mes, $ano);
                        }


                        // ARRAY LANCAMENTOS_FATURA_ASSOC
                        $dataLancFaturaAssoc = array(
                            'id_lancamento'  => $id_lancamento,
                            'id_fatura'      => $faturaReferencia->id_fatura,
                            'valor_parcela'  => $valor_parcela,
                            'valor_total'    => $valor,
                            'mes_referencia' => $mes,
                            'ano_referencia' => $ano,
                            'data_compra'    => $data_compra,
                            'n_parcela'      => $x,
                            'total_parcelas' => $qnt_parcelas,
                        );

                        // COMPRA DE TERCEIROS
                        if ($compra_terceiros == 1) {

                            $vencimento = $faturaReferencia->vencimento;
                            $vencimento = explode('-', $vencimento);
                            $dia_venc   = $vencimento[2];
                            $mes_venc   = $vencimento[1];
                            $ano_venc   = $vencimento[0];

                            if ($mes_venc == 13) {
                                $mes_venc = 01;
                                $ano_venc++;
                            }
                            $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                            if ($x > 9) {
                                $parcela_atual = $x;
                            } else {
                                $parcela_atual = '0' . $x;
                            }

                            if ($qnt_parcelas > 9) {
                                $total_parcelas = $qnt_parcelas;
                            } else {
                                $total_parcelas = '0' . $qnt_parcelas;
                            }

                            $data2 = array(
                                'id_lancamento_fatura' => $id_lancamento,
                                'id_usuario'           => getUserId(),
                                'id_cliente'           => $this->input->post('id_cliente'),
                                'descricao'            => padronizarString($this->input->post('descricao')) . ' - ' . $parcela_atual . '/' . $total_parcelas,
                                'tipo'                 => 1,
                                'valor'                => $valor_parcela,
                                'data_vencimento'      => $vencimentoFormatado,
                            );
                            // removendo edicao de compras parceladas de terceiros em Pendencias
                            //$this->pendencia_model->add('pendencias', $data2);
                        }

                        $mes++;
                        if ($mes == 13) {
                            $mes = 01;
                            $ano++;
                        }

                        if ($this->fatura_model->add('lancamentos_faturas_assoc', $dataLancFaturaAssoc)) {
                            atualizaValorVinculoFaturas($faturaReferencia->id_fatura);
                            $this->session->set_flashdata('sucesso', 'Lançamento alterado com sucesso');
                        } else {
                            $this->session->set_flashdata('erro', 'Erro ao tentar adicionar lançamentos_assoc!');
                            redirect($urlAtual);
                        }
                    }
                    $this->sincronizarVinculosTerceiroCompraEditada($id_lancamento, $mapaVinculosTerceiros, $urlAtual);
                    redirect($urlAtual);
                } else {
                    // COMPRA A VISTA
                    // ARRAY LANCAMENTOS_FATURA_ASSOC
                    $data1 = array(
                        'id_lancamento'  => $id_lancamento,
                        'id_fatura'      => $faturaAtual->id_fatura,
                        'valor_parcela'  => $valor,
                        'valor_total'    => $valor,
                        'mes_referencia' => $mes,
                        'ano_referencia' => $ano,
                        'data_compra'    => $data_compra,
                        'n_parcela'      => 1,
                        'total_parcelas' => 1,
                    );

                    // MONTA ARRAY DE PENDENCIA, NO CASO DE COMPRA DE TERCEIROS
                    if ($compra_terceiros == 1) {

                        $vencimento = $faturaAtual->vencimento;
                        $vencimento = explode('-', $vencimento);
                        $dia_venc   = $vencimento[2];
                        $mes_venc   = $vencimento[1];
                        $ano_venc   = $vencimento[0];

                        if ($mes_venc == 13) {
                            $mes_venc = 01;
                            $ano_venc++;
                        }
                        $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);
                    }

                    if ($this->fatura_model->add('lancamentos_faturas_assoc', $data1)) {
                        atualizaValorVinculoFaturas($id_fatura);
                        $this->sincronizarVinculosTerceiroCompraEditada($id_lancamento, $mapaVinculosTerceiros, $urlAtual);
                        $this->session->set_flashdata('sucesso', 'Lançamento alterado com sucesso');
                    } else {
                        $this->session->set_flashdata('erro', 'Erro ao tentar alterar lançamentos_assoc!');
                        redirect($urlAtual);
                    }
                }
                redirect($urlAtual);
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar alterar lançamento de fatura.');
                redirect($urlAtual);
            }
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Não existem faturas abertas, não é possível lançar esta compra.');
            redirect($urlAtual);
        }
        redirect($urlAtual);
    }

    public function copiarLancamento($idFatura = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para copiar lançamentos de faturas.');
            redirect(base_url());
        }

        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('financeiro/faturas');
        }

        if (!empty($_POST['id_fatura'])) {
            $idFatura = $this->input->post('id_fatura');
        }

        if (!$idFatura) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('financeiro/faturas');
        }

        $request                      = $this->input->post();
        $request['id_fatura']         = $idFatura;
        $request['para_outra_fatura'] = $this->input->post('para_outra_fatura');
        $data_compra                  = $this->input->post('data_compra');
        $urlAtual                     = $this->input->post('urlAtual');
        $valor                        = $this->input->post('valor');
        $valor_parcela                = $this->input->post('valor_parcela');
        $request['data_compra']       = date('Y-m-d');
        $request['qnt_parcelas']      = 1;
        $request['compra_parcelada']  = 0;
        $request['compra_terceiros']  = 0;
        $request['estorno']           = 0;

        if ($this->input->post('data_compra')) {
            $data_compra_exploded   = explode('/', $data_compra);
            $request['data_compra'] = sprintf('%s-%s-%s', $data_compra_exploded[2], $data_compra_exploded[1], $data_compra_exploded[0]);
        }

        if ($this->input->post('qnt_parcelas')) {
            $request['qnt_parcelas'] = $this->input->post('qnt_parcelas');
        }

        if ($this->input->post('compra_parcelada')) {
            $request['compra_parcelada'] = 1;
        }

        if ($this->input->post('compra_terceiros')) {
            $request['compra_terceiros'] = 1;
        }

        if ($this->input->post('estorno')) {
            $request['estorno'] = 1;
            $request['valor']   = '-' . $valor;
        }

        if (!validate_money($valor_parcela)) {
            $request['valor_parcela'] = str_replace(array('.', ','), array('', '.'), $valor_parcela);
        }

        if (!validate_money($request['valor'])) {
            $request['valor'] = str_replace(array('.', ','), array('', '.'), $request['valor']);
        }

        if (!$this->gravaCopia($request)) {
            $this->session->set_flashdata('erro', 'Ocorreu um erro ao tentar efetuar a cópia.');
            redirect($urlAtual);
        }

        $this->session->set_flashdata('sucesso', 'Lançamento copiado com sucesso');
        redirect($urlAtual);
    }

    private function gravaCopia($request): bool
    {
        $id                           = $request['id'];
        $idFatura                     = $request['id_fatura'];
        $faturaExistente              = $this->fatura_model->getFaturaUsuario($idFatura, getUserId());
        $faturaAlvo                   = $this->fatura_model->getFaturaAtual($idFatura);
        $faturaReferencia             = null;
        $lancamentosFaturasAssocArray = [];
        $data_compra                  = $request['data_compra'];
        $valor                        = $request['valor'];
        $valor_parcela                = $request['valor_parcela'];
        $observacoes                  = $request['observacoes'];
        $compra_parcelada             = $request['compra_parcelada'];
        $compra_terceiros             = $request['compra_terceiros'];
        $qnt_parcelas                 = $request['qnt_parcelas'];
        $estorno                      = $request['estorno'];

        if (!$faturaAlvo) return false;

        $mes_referencia = $faturaAlvo->mes_referencia;
        $ano_referencia = $faturaAlvo->ano_referencia;

        if (!$faturaExistente) return false;

        if (is_array($id) && count($id) > 0) {
            $id   = array_reverse($id);
            $data = [];

            foreach ($id as $value) {
                $lancamento = $this->fatura_model->getLancamentoFaturaById($value, getUserId());

                $data_compra_exploded = explode('-', $lancamento->data_compra);
                $data_compra          = sprintf('%s-%s-%s', $data_compra_exploded[0], $mes_referencia, $data_compra_exploded[2]);

                if ($lancamento) {
                    $data = [
                        'id_fatura'        => $faturaAlvo->id_fatura,
                        'id_usuario'       => $faturaAlvo->id_usuario,
                        'descricao'        => $lancamento->descricao,
                        'observacoes'      => $lancamento->observacoes,
                        'nome_cliente'     => $lancamento->nome_cliente,
                        'valor_total'      => $lancamento->valor_total,
                        'total_parcelas'   => $lancamento->total_parcelas,
                        'compra_parcelada' => $lancamento->compra_parcelada,
                        'compra_terceiros' => $lancamento->compra_terceiros,
                        'estorno'          => $lancamento->estorno,
                        'data_compra'      => $data_compra,
                        'mes_referencia'   => $mes_referencia,
                        'ano_referencia'   => $ano_referencia,
                    ];
                }

                if (!$this->fatura_model->add('lancamentos_faturas', $data)) return false;

                $last_id = $this->fatura_model->insert_id('lancamentos_faturas');

                $lancamentosFaturasAssocArray = [
                    'id_lancamento'  => $last_id,
                    'id_fatura'      => $faturaAlvo->id_fatura,
                    'valor_parcela'  => $lancamento->valor_total,
                    'valor_total'    => $lancamento->valor_total,
                    'mes_referencia' => $mes_referencia,
                    'ano_referencia' => $ano_referencia,
                    'data_compra'    => $data_compra,
                    'n_parcela'      => 1,
                    'total_parcelas' => 1,
                ];

                if (!$this->fatura_model->add('lancamentos_faturas_assoc', $lancamentosFaturasAssocArray)) {
                    return false;
                }
            }
            atualizaValorVinculoFaturas($idFatura);
            return true;
        }

        if ($request['para_outra_fatura']) {
            $data_compra_exploded = explode('-', $data_compra);
            $data_compra          = sprintf('%s-%s-%s', $data_compra_exploded[0], $mes_referencia, $data_compra_exploded[2]);
        }

        // ARRAY LANCAMENTOS_FATURAS
        $data = [
            'id_fatura'        => $faturaAlvo->id_fatura,
            'id_usuario'       => $faturaAlvo->id_usuario,
            'descricao'        => padronizarString($request['descricao']),
            'observacoes'      => $observacoes ?? null,
            'nome_cliente'     => $compra_terceiros ? padronizarString($request['nome_cliente']) : null,
            'valor_total'      => $valor,
            'total_parcelas'   => $qnt_parcelas,
            'compra_parcelada' => $compra_parcelada,
            'compra_terceiros' => $compra_terceiros,
            'estorno'          => $estorno,
            'data_compra'      => $data_compra,
            'mes_referencia'   => $mes_referencia,
            'ano_referencia'   => $ano_referencia,
        ];

        if (!$this->fatura_model->add('lancamentos_faturas', $data)) return false;

        $last_id = $this->fatura_model->insert_id('lancamentos_faturas');

        // COMPRA PARCELADA
        if ($compra_parcelada) {
            for ($x = 1; $x <= $qnt_parcelas; $x++) {
                // CONSULTA SE EXISTE FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAlvo->id_cartao, $mes_referencia, $ano_referencia);

                // CASO NÃO EXISTA, O SISTEMA CRIA A FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                if (!$faturaReferencia) {
                    $ultimaFatura = $this->fatura_model->getUltimaFatura($faturaAlvo->id_cartao);

                    $vencimento = $ultimaFatura->vencimento;
                    $vencimento = explode('-', $vencimento);
                    $dia_venc   = $vencimento[2];
                    $mes_venc   = $vencimento[1] + 1;
                    $ano_venc   = $vencimento[0];

                    if ($mes_venc == 13) {
                        $mes_venc = 01;
                        $ano_venc++;
                    }
                    $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                    // ARRAY ABRIR NOVA FATURA
                    $data = array(
                        'id_usuario'     => $ultimaFatura->id_usuario,
                        'id_cartao'      => $ultimaFatura->id_cartao,
                        'mes_referencia' => $mes_referencia,
                        'ano_referencia' => $ano_referencia,
                        'vencimento'     => $vencimentoFormatado,
                        'fatura_aberta'  => 2,
                    );

                    if (!$this->fatura_model->abrirFatura($data)) return false;

                    $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAlvo->id_cartao, $mes_referencia, $ano_referencia);
                }

                // ARRAY LANCAMENTOS_FATURAS_ASSOC
                $lancamentosFaturasAssocArray = [
                    'id_lancamento'  => $last_id,
                    'id_fatura'      => $faturaReferencia->id_fatura,
                    'valor_parcela'  => $valor_parcela,
                    'valor_total'    => $valor,
                    'mes_referencia' => $mes_referencia,
                    'ano_referencia' => $ano_referencia,
                    'data_compra'    => $data_compra,
                    'n_parcela'      => $x,
                    'total_parcelas' => $qnt_parcelas,
                ];

                if (!$this->fatura_model->add('lancamentos_faturas_assoc', $lancamentosFaturasAssocArray)) {
                    return false;
                }

                atualizaValorVinculoFaturas($faturaReferencia->id_fatura);
                $mes_referencia++;

                if ($mes_referencia == 13) {
                    $mes_referencia = 1;
                    $ano_referencia++;
                }
            }

            return true;
        }

        // COMPRA A VISTA
        // ARRAY LANCAMENTOS_FATURA_ASSOC
        $lancamentosFaturasAssocArray = [
            'id_lancamento'  => $last_id,
            'id_fatura'      => $faturaAlvo->id_fatura,
            'valor_parcela'  => $valor,
            'valor_total'    => $valor,
            'mes_referencia' => $mes_referencia,
            'ano_referencia' => $ano_referencia,
            'data_compra'    => $data_compra,
            'n_parcela'      => 1,
            'total_parcelas' => 1,
        ];

        if ($this->fatura_model->add('lancamentos_faturas_assoc', $lancamentosFaturasAssocArray)) {
            atualizaValorVinculoFaturas($idFatura);
            return true;
        }
        return false;
    }

    public function excluirLancamento($idLancamento = null, $parameter = false)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir lançamentos de faturas.');
            redirect(base_url());
        }

        if (!$parameter) {
            $urlAtual     = $this->input->post('urlAtual');
            $idLancamento = $this->input->post('id');
        }

        $data = array(
            'status' => 0,
        );

        $faturas = $this->fatura_model->getFaturaByLancamentosAssoc($idLancamento);
        $vinculosTerceiros = $this->fatura_model->getLancamentosTerceirosVinculadosPorCompra($idLancamento, getUserId());
        $idsLancamentosTerceiros = array_values(array_unique(array_column($vinculosTerceiros, 'id_lancamento')));

        foreach ($faturas as $fatura) {
            atualizaValorVinculoFaturas($fatura["id_fatura"]);
        }

        if ($this->fatura_model->delete('lancamentos_faturas', $data, 'id_lancamento', $idLancamento)) {
            $this->fatura_model->delete('lancamentos_faturas_assoc', $data, 'id_lancamento', $idLancamento);
            $this->pendencia_model->delete('pendencias', $data, 'id_lancamento_fatura', $idLancamento);
            $this->session->set_flashdata('sucesso', 'Lançamento excluído com sucesso');

            if (is_array($faturas) && $faturas) {
                foreach ($faturas as $fatura) {
                    atualizaValorVinculoFaturas($fatura["id_fatura"]);
                }
            }

            $this->fatura_model->sincronizarLancamentosTerceiros($idsLancamentosTerceiros, getUserId());

            if (!$parameter) {
                redirect($urlAtual);
            }
            return true;
        }

        $this->session->set_flashdata('erro', 'Erro ao tentar excluir lançamento de fatura.');

        if (!$parameter) {
            redirect($urlAtual);
        }
        return false;
    }

    private function sincronizarVinculosTerceiroCompraEditada($idLancamentoFatura, array $mapaVinculosTerceiros, $urlAtual)
    {
        if (!$mapaVinculosTerceiros) {
            return true;
        }

        $idsLancamentos = array_values(array_unique(array_column($mapaVinculosTerceiros, 'id_lancamento')));

        if (!$this->fatura_model->reconstruirVinculosTerceiroCompra($idLancamentoFatura, $mapaVinculosTerceiros, getUserId())) {
            $this->session->set_flashdata('erro', 'Erro ao tentar atualizar vínculo de terceiros');
            redirect($urlAtual);
        }

        if (!$this->fatura_model->sincronizarLancamentosTerceiros($idsLancamentos, getUserId())) {
            $this->session->set_flashdata('erro', 'Erro ao tentar atualizar lançamento vinculado do terceiro');
            redirect($urlAtual);
        }

        return true;
    }

    public function excluirSerieLancamentos()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir lançamentos de faturas.');
            redirect(base_url());
        }

        $urlAtual = $this->input->post('urlAtual');
        $id       = $this->input->post('id');
        $result   = false;

        if (is_array($id) && count($id) > 0) {
            foreach ($id as $value) {
                $result = $this->excluirLancamento($value, true);
            }

            if (!$result) {
                $this->session->set_flashdata('erro', 'Erro ao tentar excluir série de lançamentos');
                redirect($urlAtual);
            }
            $this->session->set_flashdata('sucesso', 'Lançamentos excluídos com sucesso');
            redirect($urlAtual);
        }
    }

    public function abrir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para abrir novas faturas.');
            redirect(base_url());
        }
        $urlAtual      = $_POST['urlAtual'];
        $mesReferencia = $_POST['mes_referencia'];
        $mes           = $mesReferencia + 1;
        $ano           = date('Y');
        $anoReferencia = date('Y');

        if ($mes == 13) {
            $mes = '01';
            $ano++;
        } else {
            if ($mes < 10) {
                $mes = '0' . $mes;
            }
        }
        $dia                 = $this->fatura_model->getDiaVencimentoFatura($_POST['id_cartao']);
        $vencimentoFormatado = $ano . '-' . $mes . '-' . $dia;

        $data = array(
            'id_usuario'     => getUserId(),
            'id_cartao'      => $_POST['id_cartao'],
            'mes_referencia' => $mesReferencia,
            'ano_referencia' => $anoReferencia,
            'vencimento'     => $vencimentoFormatado,
        );

        if ($this->fatura_model->abrirFatura($data)) {
            $this->session->set_flashdata('sucesso', 'Fatura aberta com sucesso');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar abrir nova fatura.');
            redirect($urlAtual);
        }
    }

    public function fechar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para fechar faturas.');
            redirect(base_url());
        }

        $urlAtual    = $this->input->post('urlAtual');
        $faturaAtual = $this->fatura_model->getFaturaAtual($_POST['id_fatura']);
        $mes         = $faturaAtual->mes_referencia;
        $ano         = $faturaAtual->ano_referencia;

        $mes++;
        if ($mes == 13) {
            $mes = 01;
            $ano++;
        }

        $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAtual->id_cartao, $mes, $ano);

        $data1 = array(
            'fatura_aberta' => 1,
        );

        if ($this->fatura_model->edit('faturas', $data1, 'id_fatura', $faturaReferencia->id_fatura)) {

            $data = array(
                'fatura_aberta' => 0,
                'fatura_paga'   => 2,
            );

            if ($this->fatura_model->edit('faturas', $data, 'id_fatura', $_POST['id_fatura'])) {
                $this->session->set_flashdata('sucesso', 'Fatura fechada com sucesso');
                redirect($urlAtual);
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar fechar a fatura.');
                redirect($urlAtual);
            }
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar abrir a próxima fatura.');
            redirect($urlAtual);
        }
    }

    public function reabrir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para reabrir faturas.');
            redirect(base_url());
        }

        $urlAtual    = $this->input->post('urlAtual');
        $faturaAtual = $this->fatura_model->getFaturaAtual($_POST['id_fatura']);

        $faturaFechadas = $this->fatura_model->fecharTodasFaturasAbertas($faturaAtual->id_cartao, getUserId());

        if ($faturaFechadas) {
            try {
                $update = [
                    'fatura_aberta' => 1,
                ];
                $this->fatura_model->edit('faturas', $update, 'id_fatura', $_POST['id_fatura']);
                $this->session->set_flashdata('sucesso', 'Fatura reaberta com sucesso');
                redirect($urlAtual);
            } catch (\Exception $e) {
                $this->session->set_flashdata('erro', 'Erro ao tentar reabrir a fatura');
                redirect($urlAtual);
            }
        }
        $this->session->set_flashdata('erro', 'Erro ao tentar reabrir a fatura');
        redirect($urlAtual);
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir faturas.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');

        if ($this->input->post('id_fatura')) {
            $id_fatura = $this->input->post('id_fatura');
        } else {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('financeiro/faturas/');
        }

        $data = array(
            'status' => 0,
        );

        if ($this->fatura_model->edit('faturas', $data, 'id_fatura', $id_fatura) == true) {
            $this->session->set_flashdata('sucesso', 'Fatura excluída com sucesso');
            desvinculaFatura($id_fatura);
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir a fatura.');
            redirect($urlAtual);
        }
    }

    public function pagar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para pagar faturas.');
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
                $data                 = [
                    'descricao'          => 'FATURA CARTAO DE CREDITO' . $apelido,
                    'valor'              => '-' . $valorTotalFatura,
                    'data_lancamento'    => $detalhesFatura->vencimento,
                    'data_pagamento'     => $detalhesFatura->data_pagamento,
                    'cliente_fornecedor' => $detalhesCartaoFatura->bandeira ? $detalhesCartaoFatura->bandeira . ' - FINAL ' . $final : null,
                    'forma_pgto'         => $detalhesFatura->forma_pgto ?? 5,
                    'baixado'            => 1,
                    'tipo'               => 2,
                ];
                $this->fatura_model->edit('lancamentos', $data, 'id_fatura', $_POST['id_fatura']);
            }
            $this->session->set_flashdata('sucesso', 'Fatura paga com sucesso');
            redirect($urlAtual);
        }

        $this->session->set_flashdata('erro', 'Erro ao tentar pagar a fatura.');
        redirect($urlAtual);
    }

    public function vincular()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para vincular faturas.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');
        $idFatura = $this->input->post('idFatura');

        $vinculoFatura = $this->fatura_model->getVinculoFatura($idFatura);
        if ($vinculoFatura) {
            $this->session->set_flashdata('erro', 'A fatura solicitada já possui vínculo ativo ao módulo de Lançamentos');
            redirect($urlAtual);
        }

        $data = array(
            'fatura_vinculada' => 1
        );

        if ($this->fatura_model->edit('faturas', $data, 'id_fatura', $idFatura)) {
            $detalhesFatura       = $this->fatura_model->getDetalhesFatura($idFatura);
            $valorTotalFatura     = $this->fatura_model->getValorTotalFatura($idFatura);
            $detalhesCartaoFatura = $this->cartoes_model->getCartao($detalhesFatura->id_cartao);
            $n_cartao             = explode(" ", trim(decriptar($detalhesCartaoFatura->numero)));
            $final                = $n_cartao[3];
            $apelido              = $detalhesCartaoFatura->apelido ? ' - ' . $detalhesCartaoFatura->apelido : null;

            $data = array(
                'id_usuario'         => getUserId(),
                'id_fatura'          => $idFatura,
                'descricao'          => 'FATURA CARTAO DE CREDITO' . $apelido,
                'cliente_fornecedor' => $detalhesCartaoFatura->bandeira ? $detalhesCartaoFatura->bandeira . ' - FINAL ' . $final : null,
                'valor'              => '-' . $valorTotalFatura,
                'data_lancamento'    => $detalhesFatura->vencimento ?? $detalhesFatura->data_pagamento,
                'data_pagamento'     => $detalhesFatura->data_pagamento ?? $detalhesFatura->vencimento,
                'forma_pgto'         => $detalhesFatura->forma_pgto ?? 5,
                'baixado'            => ($detalhesFatura->fatura_paga == 1),
                'tipo'               => 2
            );
            $this->financeiro_model->add('lancamentos', $data);
            $this->session->set_flashdata('sucesso', 'Fatura vinculada com sucesso');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar vincular a fatura');
            redirect($urlAtual);
        }
    }

    public function vinculoFaturas()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para vincular faturas.');
            redirect(base_url());
        }

        $urlAtual           = $this->input->post('urlAtual') ?? null;
        $mesReferencia      = $this->input->post('mesReferencia') ?? null;
        $anoReferencia      = $this->input->post('anoReferencia') ?? null;
        $desvincularFaturas = $this->input->post('desvincularFaturas') ?? null;

        if (!$_POST) {
            $todayDate     = date('Y-m-d');
            $todayArray    = explode('-', $todayDate);
            $mesReferencia = $todayArray[1];
            $anoReferencia = $todayArray[0];
        }

        if (!$mesReferencia || !$anoReferencia) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            ($urlAtual) ? redirect($urlAtual) : redirect('financeiro/faturas');
        }

        $cartoesAtivos = $this->cartoes_model->getCartoesAtivosUsuario(getUserId());

        foreach ($cartoesAtivos as $cartao) {
            $faturaReferencia = $this->fatura_model->getFaturaReferencia($cartao->id_cartao, $mesReferencia, $anoReferencia);

            if (!$faturaReferencia) {
                continue;
            }

            $vinculoFatura = $this->fatura_model->getVinculoFatura($faturaReferencia->id_fatura);

            if ($desvincularFaturas) {
                if ($vinculoFatura) {
                    desvinculaFatura($faturaReferencia->id_fatura);
                }
                continue;
            }

            if ($vinculoFatura) {
                atualizaValorVinculoFaturas($faturaReferencia->id_fatura);
            }
            vinculaFatura($faturaReferencia->id_fatura);
        }

        if ($_POST) {
            if ($desvincularFaturas) {
                $this->session->set_flashdata('sucesso', 'Faturas de cartões ativos desvinculadas com sucesso');
                redirect($urlAtual);
            }
            $this->session->set_flashdata('sucesso', 'Faturas de cartões ativos vinculadas com sucesso');
            redirect($urlAtual);
        }
    }

    public function desvincular()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para desvincular faturas.');
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
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar faturas.');
            redirect(base_url());
        }
        $urlAtual  = $this->input->post('urlAtual');
        $id_cartao = $this->input->post('id_cartao');
        $dia       = $this->input->post('dia_vencimento');
        $autoLink  = $this->input->post('invoiceAutoLink');

        if (!$id_cartao) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($urlAtual);
        }

        $data = array(
            'id_usuario'     => getUserId(),
            'id_cartao'      => $id_cartao,
            'dia_vencimento' => $dia,
        );

        if ($autoLink == 'on') {
            $this->fatura_model->setAutoLinkToAllUserActiveCards();
        }

        if (!$autoLink) {
            $this->fatura_model->unsetAutoLinkToAllUserActiveCards();
        }

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

                $data = array(
                    'id_usuario'        => $adicional->id_usuario,
                    'id_cartao'         => $adicional->id_cartao,
                    'id_cartao_titular' => $id_cartao,
                    'dia_vencimento'    => $dia,
                    'adicional'         => 1
                );
                $this->fatura_model->edit('configs_faturas', $data, 'id_cartao', $adicional->id_cartao);
            }
            $this->session->set_flashdata('sucesso', 'Configurações alteradas com sucesso');
            redirect($urlAtual);
        }

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

            $data = array(
                'id_usuario'        => $adicional->id_usuario,
                'id_cartao'         => $adicional->id_cartao,
                'id_cartao_titular' => $id_cartao,
                'dia_vencimento'    => $dia,
                'adicional'         => 1
            );
            $this->fatura_model->add('configs_faturas', $data);
        }
        $this->session->set_flashdata('sucesso', 'Configurações salvas com sucesso');

        redirect($urlAtual);
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

        if ($mes) {
            $data['month']     = translateMonth($mes);
            $data['nextMonth'] = translateMonth($mes + 1);
            $data['prevMonth'] = translateMonth($mes - 1);
        }

        $todayDate      = date('Y-m-d');
        $todayArray     = explode('-', $todayDate);
        $referenceMonth = $todayArray[1];
        $referenceYear  = $todayArray[0];
        $result         = [];
        $data['idCard'] = $idCartao;

        if ($mes) {
            $referenceMonth = $mes;
        }

        if ($ano) {
            $referenceYear = $ano;
        }

        $prevReferenceMonth = $referenceMonth - 1;
        $nextReferenceMonth = $referenceMonth + 1;
        $prevReferenceYear  = $referenceYear;
        $nextReferenceYear  = $referenceYear;

        if ($referenceMonth == 12) {
            $nextReferenceMonth = 1;
            $nextReferenceYear++;
        }

        if ($referenceMonth == 1) {
            $prevReferenceMonth = 12;
            $prevReferenceYear--;
        }

        $mesVencimento = $referenceMonth + 1;
        $anoVencimento = $referenceYear;

        if ($mesVencimento == 13) {
            $mesVencimento = '01';
            $anoVencimento++;
        }

        for ($m = 1; $m <= 12; $m++) {
            $currentMonth                                                       = buildStartEndDate(translateMonth($m, true, true), $ano);
            $data['monthList'][$currentMonth["referenceMonth"]]['name']         = translateMonth($m, true, true) . ' - ' . translateMonth($m, true);
            $data['monthList'][$currentMonth["referenceMonth"]]['notification'] = $this->fatura_model->getLancamentosPendentesTerceiros(getUserId(), $currentMonth['referenceMonth'], $currentMonth['referenceYear'], $nome);
        }

        $faturasTerceiros   = $this->fatura_model->getFaturasTerceiros(getUserId(), $nome, $referenceMonth, $referenceYear);
        $vinculoTerceiroPeriodo = $this->fatura_model->getVinculoTerceiroPeriodo(getUserId(), $nome, $referenceMonth, $referenceYear);
        $referenceMonthName = getExtendedMonthName($referenceMonth);
        $prevReferenceMonth = translateMonth($prevReferenceMonth, true, true);
        $nextReferenceMonth = translateMonth($nextReferenceMonth, true, true);
        $dueDateMonthName   = getExtendedMonthName($mesVencimento);

        if ($faturasTerceiros) {
            foreach ($faturasTerceiros as $fatura) {
                $lancamentosTerceiros = $this->fatura_model->getLancamentosTerceiros(getUserId(), $fatura['id_cartao'], $nome, $referenceMonth, $referenceYear);
                $monthName            = getExtendedMonthName($fatura['mes_referencia']);

                if (isMobileDevice()) {
                    $monthName = getExtendedMonthName($fatura['mes_referencia'], 'MMM');
                }

                $result[$lancamentosTerceiros[0]["id_fatura"]]                = $fatura;
                $result[$lancamentosTerceiros[0]["id_fatura"]]['cartao']      = $this->cartoes_model->getDetalhesCartao($fatura['id_cartao']);
                $result[$lancamentosTerceiros[0]["id_fatura"]]['reference']   = $monthName . ' / ' . $fatura['ano_referencia'];
                $result[$lancamentosTerceiros[0]["id_fatura"]]['lancamentos'] = $lancamentosTerceiros;
            }
        }

        $data['terceiros']          = $this->fatura_model->getAllTerceiros(null, null, $referenceYear);
        $data['results']            = $result;
        $data['name']               = $nome;
        $data['vinculoTerceiroPeriodo'] = $vinculoTerceiroPeriodo;
        $data['yearsList']          = $this->yearsList;
        $data['referencePeriod']    = sprintf('%s / %s', $referenceMonthName, $referenceYear);
        $data['dueDatePeriod']      = sprintf('%s / %s', $dueDateMonthName, $anoVencimento);
        $data['referenceMonth']     = $referenceMonth;
        $data['prevReferenceMonth'] = $prevReferenceMonth;
        $data['nextReferenceMonth'] = $nextReferenceMonth;
        $data['prevReferenceYear']  = $prevReferenceYear;
        $data['nextReferenceYear']  = $nextReferenceYear;
        $data['referenceYear']      = $referenceYear;
        $data['menuFinanceiro']     = true;
        $data['view']               = 'faturas/lancamentos_terceiros';
        $this->load->view('tema/topo', $data);
    }

    public function compraTerceiro($idLancamento = null)
    {
        if (!$idLancamento) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/faturas');
        }

        $compra = $this->fatura_model->getCompraTerceiro(getUserId(), (int) $idLancamento);

        if (!$compra) {
            $this->session->set_flashdata('erro', 'Compra solicitada não encontrada para este usuário');
            redirect('financeiro/faturas');
        }

        $parcelas = $this->fatura_model->getParcelasCompraTerceiro(getUserId(), (int) $idLancamento);

        if (!$parcelas) {
            $this->session->set_flashdata('erro', 'Parcelas da compra não encontradas');
            redirect('financeiro/faturas');
        }

        $voltarUrl = base_url('financeiro/faturas/terceiros') . '?' . http_build_query([
                'mesReferencia' => date('m'),
                'anoReferencia' => date('Y'),
                'cartao'        => $parcelas[0]['id_cartao'],
                'nome'          => $compra->nome_cliente,
            ]);

        $data['compra']          = $compra;
        $data['parcelas']        = $parcelas;
        $data['voltarUrl']       = $voltarUrl;
        $data['menuFinanceiro']  = true;
        $data['view']            = 'faturas/compra_terceiro';
        $this->load->view('tema/topo', $data);
    }

    public function marcarParcelaTerceiroPago()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar lançamentos de faturas.');
            redirect(base_url());
        }

        $urlAtual = $this->input->post('urlAtual') ?: base_url('financeiro/faturas');
        $idAssoc  = (int) $this->input->post('id_assoc');
        $acao     = $this->input->post('acao');

        if (!$idAssoc || !in_array($acao, ['pagar', 'remover'])) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($urlAtual);
        }

        $lancamento = $this->fatura_model->getLancamentoAssocTerceiroUsuario($idAssoc, getUserId());

        if (!$lancamento) {
            $this->session->set_flashdata('erro', 'Parcela solicitada não encontrada para este usuário');
            redirect($urlAtual);
        }

        $pago = ($acao == 'pagar');

        if (
            $this->fatura_model->setParcelaTerceiroPago($idAssoc, $pago) &&
            $this->fatura_model->sincronizarVinculoTerceiroPorParcela($idAssoc, getUserId())
        ) {
            $mensagem = $pago ? 'Parcela marcada como paga' : 'Pagamento da parcela removido';
            $this->session->set_flashdata('sucesso', $mensagem);
            redirect($urlAtual);
        }

        $this->session->set_flashdata('erro', 'Erro ao tentar atualizar pagamento da parcela');
        redirect($urlAtual);
    }

    public function marcarCompraTerceiroPago()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar lançamentos de faturas.');
            redirect(base_url());
        }

        $urlAtual = $this->input->post('urlAtual') ?: base_url('financeiro/faturas');
        $idAssoc  = (int) $this->input->post('id_assoc');
        $acao     = $this->input->post('acao');

        if (!$idAssoc || !in_array($acao, ['pagar', 'remover'])) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($urlAtual);
        }

        $lancamento = $this->fatura_model->getLancamentoAssocTerceiroUsuario($idAssoc, getUserId());

        if (!$lancamento) {
            $this->session->set_flashdata('erro', 'Parcela solicitada não encontrada para este usuário');
            redirect($urlAtual);
        }

        $pago = ($acao == 'pagar');

        if (
            $this->fatura_model->setCompraTerceiroPago($lancamento->id_lancamento, getUserId(), $pago) &&
            $this->fatura_model->sincronizarVinculosTerceiroPorCompra($lancamento->id_lancamento, getUserId())
        ) {
            $mensagem = $pago ? 'Compra marcada como paga' : 'Pagamento da compra removido';
            $this->session->set_flashdata('sucesso', $mensagem);
            redirect($urlAtual);
        }

        $this->session->set_flashdata('erro', 'Erro ao tentar atualizar pagamento da compra');
        redirect($urlAtual);
    }

    public function vincularTerceiroPeriodo()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para vincular compras de terceiros.');
            redirect(base_url());
        }

        $urlAtual      = $this->input->post('urlAtual') ?: base_url('financeiro/faturas');
        $nome          = $this->input->post('nome');
        $mesReferencia = $this->input->post('mesReferencia');
        $anoReferencia = $this->input->post('anoReferencia');

        if (!$nome || !is_string($nome) || is_numeric($nome) || !$mesReferencia || !$anoReferencia) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect($urlAtual);
        }

        if ($this->fatura_model->getVinculoTerceiroPeriodo(getUserId(), $nome, $mesReferencia, $anoReferencia)) {
            $this->session->set_flashdata('erro', 'Este período já possui vínculo ativo para o terceiro selecionado');
            redirect($urlAtual);
        }

        $parcelas = $this->fatura_model->getParcelasTerceiroPeriodoParaVinculo(getUserId(), $nome, $mesReferencia, $anoReferencia);

        if (!$parcelas) {
            $this->session->set_flashdata('erro', 'Não existem parcelas pendentes para vincular neste período');
            redirect($urlAtual);
        }

        $total       = 0;
        $vencimento  = $parcelas[0]['vencimento'];
        $idsAssoc    = [];
        $totaisCartao = [];

        foreach ($parcelas as $parcela) {
            $total += $parcela['valor_parcela'];
            $idsAssoc[] = $parcela['id_assoc'];

            if (strtotime($parcela['vencimento']) < strtotime($vencimento)) {
                $vencimento = $parcela['vencimento'];
            }

            $numeroCartao = decriptar($parcela['cartao_numero']);
            $partesCartao = explode(' ', trim($numeroCartao));
            $finalCartao  = end($partesCartao);
            $cartaoLabel  = $parcela['cartao_apelido'] ?: $parcela['cartao_bandeira'] . ' - FINAL ' . $finalCartao;

            if (!isset($totaisCartao[$cartaoLabel])) {
                $totaisCartao[$cartaoLabel] = 0;
            }

            $totaisCartao[$cartaoLabel] += $parcela['valor_parcela'];
        }

        $observacoes = [];

        foreach ($totaisCartao as $cartaoLabel => $valorCartao) {
            $observacoes[] = $cartaoLabel . ': R$ ' . number_format($valorCartao, 2, ',', '.');
        }

        $dataLancamento = [
            'id_usuario'         => getUserId(),
            'descricao'          => 'TOTAL DEVIDO NOS CARTOES',
            'observacoes'        => implode("\n", $observacoes),
            'valor'              => $total,
            'data_lancamento'    => $vencimento,
            'data_pagamento'     => $vencimento,
            'baixado'            => 0,
            'cliente_fornecedor' => padronizarString($nome),
            'forma_pgto'         => 6,
            'tipo'               => 1
        ];

        $this->db->trans_begin();

        $lancamentoCriado = $this->fatura_model->add('lancamentos', $dataLancamento);
        $idLancamento     = $lancamentoCriado ? $this->fatura_model->insert_id('lancamentos') : null;
        $vinculado        = $idLancamento ? $this->fatura_model->vincularLancamentoTerceiro($idLancamento, $idsAssoc) : false;

        if ($this->db->trans_status() && $lancamentoCriado && $vinculado) {
            $this->db->trans_commit();
            $this->session->set_flashdata('sucesso', 'Compras do terceiro vinculadas com sucesso');
            redirect($urlAtual);
        }

        $this->db->trans_rollback();
        $this->session->set_flashdata('erro', 'Erro ao tentar vincular compras do terceiro');
        redirect($urlAtual);
    }

    public function pesquisa()
    {
        if (!isset($_GET['busca'])) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('financeiro/faturas');
        }

        $termo = $_GET['busca'] ?? null;
        $start = $_GET['per_page'] ?? null;


        if (!is_string($termo) || is_numeric($termo)) {
            $this->session->set_flashdata('erro', 'O termo pesquisado é inválido');
            redirect('financeiro/faturas');
        }

        $query_string = null;
        $lastElement  = end($_GET);

        foreach ($_GET as $key => $value) {
            if ($key != 'per_page') {
                if ($value == $lastElement) {
                    $query_string .= $key . '=' . $value;
                } else {
                    $query_string .= $key . '=' . $value . '&';
                }
            }
        }

        $config['base_url']          = base_url('financeiro/faturas/pesquisa');
        $config['suffix']            = '&' . $query_string;
        $config['first_url']         = $config['base_url'] . '?' . $query_string;
        $config['total_rows']        = $this->fatura_model->countPesquisaLancamentosFaturas($termo);
        $config['per_page']          = 20;
        $config['page_query_string'] = true;
        $config['next_link']         = '<i class="fa-solid fa-forward"></i>';
        $config['prev_link']         = '<i class="fa-solid fa-backward"></i>';
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

        $searchResult = $this->fatura_model->pesquisaLancamentosFaturas(
            $termo,
            $limit = null,
            $config['total_rows'],
            $config['per_page'],
            $start
        );

        $data['results']        = $searchResult;
        $data['busca']          = $termo;
        $data['menuFinanceiro'] = true;
        $data['view']           = 'faturas/pesquisa';
        $this->load->view('tema/topo', $data);
    }

    public function autoCompleteTerceiros()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            echo $this->fatura_model->autoCompleteTerceiros($q, getUserId());
        }
    }

    public function autoCompleteDescricao()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            echo $this->fatura_model->autoCompleteDescricao($q, getUserId());
        }
    }

    public function ajaxDiaVencimentoFatura()
    {
        $id_cartao = $_POST['id_cartao'];
        echo json_encode($this->fatura_model->getDiaVencimentoFatura($id_cartao));
    }

    private function getTranslateMonth($request, $abbreviate = false)
    {
        if (is_array($request)) {
            foreach ($request as $item) {
                if (is_object($item) && isset($item->mes_referencia)) {
                    $item->mes_descricao = translateMonth($item->mes_referencia, $abbreviate);
                }

                if (is_array($item) && isset($item['id_forma_pgto'])) {
                    $item['mes_descricao'] = translateMonth($item['mes_descricao'], $abbreviate);
                }
            }
        }

        if (is_object($request) && isset($request->mes_referencia)) {
            $request->mes_descricao = translateMonth($request->mes_referencia, $abbreviate);
        }

        return $request;
    }

}
