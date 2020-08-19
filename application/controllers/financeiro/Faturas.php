<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Faturas extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
        $this->load->library('pagination');
    }

    public function index()
    {
        $this->faturas();
    }

    //MODULO DE FATURAS
    public function faturas()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar faturas.');
            redirect(base_url());
        }
        $where = '';
        $periodo = $this->input->get('periodo');
        $cliente = $this->input->get('id_cliente');

        $config['base_url'] = site_url() . 'financeiro/faturas/?periodo=' . $periodo;
        $config['total_rows'] = $this->fatura_model->count('faturas', 'status = 1 AND id_usuario = ' . id_usuario());
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

        $this->data['parcelas'] = array(
            2 => '2 x',
            3 => '3 x',
            4 => '4 x',
            5 => '5 x',
            6 => '6 x',
            7 => '7 x',
            8 => '8 x',
            9 => '9 x',
            10 => '10 x',
            11 => '11 x',
            12 => '12 x',
        );

        $primeiro_cartao = $this->cartoes_model->getPrimeiroCartaoUsuario(id_usuario());

        if ($_GET['id_cartao']) {
            $id_cartao = $_GET['id_cartao'];
            $cartao = $this->cartoes_model->cartaoExistente($id_cartao);

            if ($cartao->id_usuario != id_usuario()) {
                if ($cartao->id_usuario_titular != id_usuario()) {
                    $this->session->set_flashdata('erro', 'Cartão solicitado não encontrado');
                    redirect('financeiro/faturas');
                }
            }
        } else {
            $id_cartao = $primeiro_cartao->id_cartao;
        }

        $this->data['cartao_selecionado'] = $id_cartao;
        $this->data['cartoes'] = $this->cartoes_model->getCartoesUsuario(id_usuario());
        $this->data['saldoVencidas'] = $this->fatura_model->getSaldoFaturasVencidas(id_usuario(), $id_cartao);
        $this->data['saldoPendente'] = $this->fatura_model->getSaldoFaturasPendentes(id_usuario(), $id_cartao);
        $this->data['saldoQuitado'] = $this->fatura_model->getSaldoFaturasPagas(id_usuario(), $id_cartao);
        $this->data['formasPagamento'] = $this->financeiro_model->getFormasPagamento();
        $this->data['faturaAberta'] = $this->fatura_model->getFaturaAbertaUsuario(id_usuario(), $id_cartao);
        $this->data['results'] = $this->fatura_model->get('faturas', '*', $where, id_usuario(), $id_cartao, $config['per_page'], $this->input->get('per_page'));
        $this->data['menuFinanceiro'] = true;
        $this->data['view'] = 'faturas/gerenciar_faturas';
        $this->load->view('tema/topo', $this->data);
    }

    public function detalhes($id = null, $id_cartao = null)
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

        $periodo = $this->input->get('periodo');
        $cliente = $this->input->get('cliente');

        $this->load->library('pagination');

        $config['base_url'] = site_url() . 'faturas/';
        $config['total_rows'] = $this->fatura_model->count('faturas', 'status = 1 AND id_usuario = ' . id_usuario());
        $config['per_page'] = 0;
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

        $data['parcelas'] = array(
            2 => '2 x',
            3 => '3 x',
            4 => '4 x',
            5 => '5 x',
            6 => '6 x',
            7 => '7 x',
            8 => '8 x',
            9 => '9 x',
            10 => '10 x',
            11 => '11 x',
            12 => '12 x',
        );

        if ($id_fatura == null) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('financeiro/faturas');
        }

        if (isset($cliente) && $cliente != null) {
            $limit = null;
            if (!isset($where)) {
                $where = 'id_cliente = ' . $cliente;
            } else {
                $where .= ' AND id_cliente = ' . $cliente;
            }
        }

        $faturaExistente = $this->fatura_model->getById($id_fatura);

        if ($faturaExistente) {
            $fatura_selecionada = $this->fatura_model->getFatura($id_fatura);
            $cartao = $this->cartoes_model->getDetalhesCartao($id_cartao);

            if (($fatura_selecionada->id_usuario == id_usuario() && $cartao->id_usuario == id_usuario()) || ($fatura_selecionada->id_cartao == $id_cartao && $cartao->id_usuario_titular == id_usuario())) {
                $data['clientes'] = $this->pendencia_model->getClientes(id_usuario());
                $data['selected_cliente'] = $cliente;
                $data['fatura'] = $this->fatura_model->getDetalhesFatura($id_fatura);
                $data['id_fatura'] = $id_fatura;
                $data['id_cartao'] = $id_cartao;
                $data['mes_referencia'] = ($data['fatura']->mes_referencia);
                $data['ano_referencia'] = ($data['fatura']->ano_referencia);
                $data['status_fatura'] = ($data['fatura']->fatura_aberta);
                $data['id_usuario'] = ($data['fatura']->id_usuario);
                $data['formasPagamento'] = $this->financeiro_model->getFormasPagamento();
                $data['fatura_paga'] = ($data['fatura']->fatura_paga);
                $data['lancamentoEditavel'] = $this->fatura_model->getLancamentoEditavel($data['mes_referencia'], $data['ano_referencia']);
                $data['subresults'] = $this->fatura_model->getLancamentos('lancamentos_faturas', '*', $fatura_selecionada->id_usuario, $where, $config['per_page'], $this->input->get('per_page'));
                $data['results'] = $this->fatura_model->getLancamentosAssoc('lancamentos_faturas_assoc', '*', $id_fatura, $where = null, $config['per_page'], $this->input->get('per_page'));
                $data['menuFinanceiro'] = true;
                $data['menuFaturas'] = true;

                $data['view'] = 'faturas/detalhes_fatura';
                $this->load->view('tema/topo', $data);
            } else {
                $this->session->set_flashdata('erro', 'Fatura solicitada não encontrada para este usuário.');
                redirect('financeiro/faturas');
            }
        } else {
            $this->session->set_flashdata('erro', 'Fatura solicitada não encontrada.');
            redirect('financeiro/faturas');
        }

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

        $urlAtual = $this->input->post('urlAtual');
        $valor = $this->input->post('valor');
        $valor_parcela = $this->input->post('valor_parcela');

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
            $valor = '-' . $valor;
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

        $faturaExistente = $this->fatura_model->getFaturaUsuario($id_fatura);

        if ($faturaExistente) {
            $faturaAtual = $this->fatura_model->getFaturaAtual($id_fatura);
            $mes = $faturaAtual->mes_referencia;
            $ano = $faturaAtual->ano_referencia;

            //ARRAY LANCAMENTOS_FATURAS
            $data = array(
                'id_fatura' => $faturaAtual->id_fatura,
                'id_cliente' => $this->input->post('id_cliente') ?: null,
                'id_usuario' => $faturaAtual->id_usuario,
                'descricao' => padronizarString($this->input->post('descricao')),
                'nome_cliente' => $this->input->post('nome_cliente') ? padronizarString($this->input->post('nome_cliente')) : null,
                'valor_total' => $valor,
                'total_parcelas' => $qnt_parcelas,
                'compra_parcelada' => $compra_parcelada,
                'compra_terceiros' => $compra_terceiros,
                'estorno' => $estorno,
                'data_compra' => $data_compra,
                'mes_referencia' => $mes,
                'ano_referencia' => $ano,
            );

            if ($this->fatura_model->add('lancamentos_faturas', $data)) {
                $last_id = $this->fatura_model->insert_id('lancamentos_faturas');

                //COMPRA PARCELADA
                if ($this->input->post('compra_parcelada') == 1) {
                    for ($x = 1; $x <= $qnt_parcelas; $x++) {

                        //CONSULTA SE EXISTE FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                        $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAtual->id_cartao, $mes, $ano);

                        //CASO NÃO EXISTA, O SISTEMA CRIA A FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                        if (!$faturaReferencia) {
                            $ultimaFatura = $this->fatura_model->getUltimaFatura($faturaAtual->id_cartao);

                            $vencimento = $ultimaFatura->vencimento;
                            $vencimento = explode('-', $vencimento);
                            $dia_venc = $vencimento[2];
                            $mes_venc = $vencimento[1] + 1;
                            $ano_venc = $vencimento[0];

                            if ($mes_venc == 13) {
                                $mes_venc = 01;
                                $ano_venc++;
                            }
                            $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                            //ARRAY ABRIR NOVA FATURA
                            $data = array(
                                'id_usuario' => $ultimaFatura->id_usuario,
                                'id_cartao' => $ultimaFatura->id_cartao,
                                'mes_referencia' => $mes,
                                'ano_referencia' => $ano,
                                'vencimento' => $vencimentoFormatado,
                                'fatura_aberta' => 2,
                            );

                            if (!$this->fatura_model->abrirFatura($data)) {
                                $this->session->set_flashdata('erro', 'Erro ao tentar abrir nova fatura.');
                                redirect($urlAtual);
                            }
                        }

                        $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAtual->id_cartao, $mes, $ano);

                        //ARRAY LANCAMENTOS_FATURAS_ASSOC
                        $data1 = array(
                            'id_lancamento' => $last_id,
                            'id_fatura' => $faturaReferencia->id_fatura,
                            'valor_parcela' => $valor_parcela,
                            'valor_total' => $valor,
                            'mes_referencia' => $mes,
                            'ano_referencia' => $ano,
                            'data_compra' => $data_compra,
                            'n_parcela' => $x,
                            'total_parcelas' => $qnt_parcelas,
                        );

                        // COMPRA DE TERCEIROS
                        if ($compra_terceiros == 1) {

                            $vencimento = $faturaReferencia->vencimento;
                            $vencimento = explode('-', $vencimento);
                            $dia_venc = $vencimento[2];
                            $mes_venc = $vencimento[1];
                            $ano_venc = $vencimento[0];

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

                            //MONTA ARRAY DE PENDENCIAS
                            $data2 = array(
                                'id_lancamento_fatura' => $last_id,
                                // VERIFICAR O FUNCIONAMENTO DESTA REGRA DE NEGOCIO: id_usuario()
                                'id_usuario' => id_usuario(),
                                'id_cliente' => $this->input->post('id_cliente'),
                                'descricao' => padronizarString($this->input->post('descricao')) . ' - ' . $parcela_atual . '/' . $total_parcelas,
                                'tipo' => 1,
                                'valor' => $valor_parcela,
                                'data_vencimento' => $vencimentoFormatado,
                            );
                            $this->pendencia_model->add('pendencias', $data2);
                        }

                        $mes++;
                        if ($mes == 13) {
                            $mes = 01;
                            $ano++;
                        }

                        if ($this->fatura_model->add('lancamentos_faturas_assoc', $data1)) {
                            $this->session->set_flashdata('sucesso', 'Lançamento adicionado com sucesso!');
                        } else {
                            $this->session->set_flashdata('erro', 'Erro ao tentar adicionar lançamentos_assoc!');
                            redirect($urlAtual);
                        }
                    }
                    redirect($urlAtual);
                } else {
                    //COMPRA A VISTA
                    //ARRAY LANCAMENTOS_FATURA_ASSOC
                    $data1 = array(
                        'id_lancamento' => $last_id,
                        'id_fatura' => $faturaAtual->id_fatura,
                        'valor_parcela' => $valor,
                        'valor_total' => $valor,
                        'mes_referencia' => $mes,
                        'ano_referencia' => $ano,
                        'data_compra' => $data_compra,
                        'n_parcela' => 1,
                        'total_parcelas' => 1,
                    );

                    //MONTA ARRAY DE PENDENCIA, NO CASO DE COMPRA DE TERCEIROS
                    if ($compra_terceiros == 1) {

                        $vencimento = $faturaAtual->vencimento;
                        $vencimento = explode('-', $vencimento);
                        $dia_venc = $vencimento[2];
                        $mes_venc = $vencimento[1];
                        $ano_venc = $vencimento[0];

                        if ($mes_venc == 13) {
                            $mes_venc = 01;
                            $ano_venc++;
                        }
                        $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                        $data2 = array(
                            'id_lancamento_fatura' => $last_id,
                            'id_usuario' => id_usuario(),
                            'id_cliente' => $this->input->post('id_cliente'),
                            'descricao' => padronizarString($this->input->post('descricao')),
                            'tipo' => 1,
                            'valor' => $valor,
                            'data_vencimento' => $vencimentoFormatado,
                        );
                        $this->pendencia_model->add('pendencias', $data2);
                    }

                    if ($this->fatura_model->add('lancamentos_faturas_assoc', $data1) == true) {
                        $this->session->set_flashdata('sucesso', 'Lançamento adicionado com sucesso!');
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
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar novos lançamentos de faturas.');
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

        $urlAtual = $this->input->post('urlAtual');
        $valor = $this->input->post('valor');
        $valor_parcela = $this->input->post('valor_parcela');
        $id_lancamento = $this->input->post('id_lancamento');

        if ($this->input->post('compra_parcelada') == 1) {
            $qnt_parcelas = $this->input->post('qnt_parcelas');
        } else {
            $qnt_parcelas = 1;
        }

        if ($this->input->post('estorno')) {
            $estorno = 1;
            $valor = '-' . $valor;
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

        $faturaExistente = $this->fatura_model->getFaturaUsuario($id_fatura);

        if ($faturaExistente) {
            $faturaAtual = $this->fatura_model->getFaturaAtual($id_fatura);
            $mes = $faturaAtual->mes_referencia;
            $ano = $faturaAtual->ano_referencia;

            //ARRAY LANCAMENTOS_FATURAS
            $data = array(
                'id_fatura' => $faturaAtual->id_fatura,
                'id_usuario' => id_usuario(),
                'descricao' => padronizarString($this->input->post('descricao')),
                'valor_total' => $valor,
                'total_parcelas' => $qnt_parcelas,
                'compra_parcelada' => $this->input->post('compra_parcelada'),
                'estorno' => $estorno,
                'data_compra' => $data_compra,
                'mes_referencia' => $mes,
                'ano_referencia' => $ano,
                'id_cliente' => $this->input->post('id_cliente'),
                'nome_cliente' => $this->input->post('nome_cliente') ? padronizarString($this->input->post('nome_cliente')) : null,
                'compra_terceiros' => $compra_terceiros,
            );

            if ($this->fatura_model->edit('lancamentos_faturas', $data, 'id_lancamento', $id_lancamento)) {

                $this->fatura_model->delete_real('lancamentos_faturas_assoc', 'id_lancamento', $id_lancamento);
                $this->fatura_model->delete_real('pendencias', 'id_lancamento_fatura', $id_lancamento);

                //COMPRA PARCELADA
                if ($this->input->post('compra_parcelada') == 1) {
                    for ($x = 1; $x <= $qnt_parcelas; $x++) {

                        //CONSULTA SE EXISTE FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                        $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAtual->id_cartao, $mes, $ano);

                        //CASO NÃO EXISTA, O SISTEMA CRIA A FATURA REFERENTE AO MES DE LANÇAMENTO DA PARCELA
                        if (!$faturaReferencia) {
                            $ultimaFatura = $this->fatura_model->getUltimaFatura($faturaAtual->id_cartao);

                            $vencimento = $ultimaFatura->vencimento;
                            $vencimento = explode('-', $vencimento);
                            $dia_venc = $vencimento[2];
                            $mes_venc = $vencimento[1] + 1;
                            $ano_venc = $vencimento[0];

                            if ($mes_venc == 13) {
                                $mes_venc = 01;
                                $ano_venc++;
                            }
                            $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                            //ARRAY ABRIR NOVA FATURA
                            $data = array(
                                'id_usuario' => $ultimaFatura->id_usuario,
                                'id_cartao' => $ultimaFatura->id_cartao,
                                'mes_referencia' => $mes,
                                'ano_referencia' => $ano,
                                'vencimento' => $vencimentoFormatado,
                                'fatura_aberta' => 2,
                            );

                            if (!$this->fatura_model->abrirFatura($data)) {
                                $this->session->set_flashdata('erro', 'Erro ao tentar abrir nova fatura.');
                                redirect($urlAtual);
                            }
                        }

                        $faturaReferencia = $this->fatura_model->getFaturaReferencia($faturaAtual->id_cartao, $mes, $ano);

                        //ARRAY LANCAMENTOS_FATURA_ASSOC
                        $data1 = array(
                            'id_lancamento' => $id_lancamento,
                            'id_fatura' => $faturaReferencia->id_fatura,
                            'valor_parcela' => $valor_parcela,
                            'valor_total' => $valor,
                            'mes_referencia' => $mes,
                            'ano_referencia' => $ano,
                            'data_compra' => $data_compra,
                            'n_parcela' => $x,
                            'total_parcelas' => $qnt_parcelas,
                        );

                        // COMPRA DE TERCEIROS
                        if ($compra_terceiros == 1) {

                            $vencimento = $faturaReferencia->vencimento;
                            $vencimento = explode('-', $vencimento);
                            $dia_venc = $vencimento[2];
                            $mes_venc = $vencimento[1];
                            $ano_venc = $vencimento[0];

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
                                'id_usuario' => id_usuario(),
                                'id_cliente' => $this->input->post('id_cliente'),
                                'descricao' => padronizarString($this->input->post('descricao')) . ' - ' . $parcela_atual . '/' . $total_parcelas,
                                'tipo' => 1,
                                'valor' => $valor_parcela,
                                'data_vencimento' => $vencimentoFormatado,
                            );
                            $this->pendencia_model->add('pendencias', $data2);
                        }

                        $mes++;
                        if ($mes == 13) {
                            $mes = 01;
                            $ano++;
                        }

                        if ($this->fatura_model->add('lancamentos_faturas_assoc', $data1)) {
                            $this->session->set_flashdata('sucesso', 'Lançamento alterado com sucesso!');
                        } else {
                            $this->session->set_flashdata('erro', 'Erro ao tentar adicionar lançamentos_assoc!');
                            redirect($urlAtual);
                        }
                    }
                    redirect($urlAtual);
                } else {
                    //COMPRA A VISTA
                    //ARRAY LANCAMENTOS_FATURA_ASSOC
                    $data1 = array(
                        'id_lancamento' => $id_lancamento,
                        'id_fatura' => $faturaAtual->id_fatura,
                        'valor_parcela' => $valor,
                        'valor_total' => $valor,
                        'mes_referencia' => $mes,
                        'ano_referencia' => $ano,
                        'data_compra' => $data_compra,
                        'n_parcela' => 1,
                        'total_parcelas' => 1,
                    );

                    //MONTA ARRAY DE PENDENCIA, NO CASO DE COMPRA DE TERCEIROS
                    if ($compra_terceiros == 1) {

                        $vencimento = $faturaAtual->vencimento;
                        $vencimento = explode('-', $vencimento);
                        $dia_venc = $vencimento[2];
                        $mes_venc = $vencimento[1];
                        $ano_venc = $vencimento[0];

                        if ($mes_venc == 13) {
                            $mes_venc = 01;
                            $ano_venc++;
                        }
                        $vencimentoFormatado = ($ano_venc . '-' . $mes_venc . '-' . $dia_venc);

                        $data2 = array(
                            'id_lancamento_fatura' => $id_lancamento,
                            'id_usuario' => id_usuario(),
                            'id_cliente' => $this->input->post('id_cliente'),
                            'descricao' => padronizarString($this->input->post('descricao')),
                            'tipo' => 1,
                            'valor' => $valor,
                            'data_vencimento' => $vencimentoFormatado,
                        );
                        $this->pendencia_model->add('pendencias', $data2);
                    }

                    if ($this->fatura_model->add('lancamentos_faturas_assoc', $data1) == true) {
                        $this->session->set_flashdata('sucesso', 'Lançamento alterado com sucesso!');
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

    public function excluirLancamento()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir lançamentos de faturas.');
            redirect(base_url());
        }

        $urlAtual = $this->input->post('urlAtual');
        $id_lancamento = $this->input->post('id_lancamento');

        $data = array(
            'status' => 0,
        );

        if ($this->fatura_model->delete('lancamentos_faturas', $data, 'id_lancamento', $id_lancamento) == true) {

            $this->fatura_model->delete('lancamentos_faturas_assoc', $data, 'id_lancamento', $id_lancamento);
            $this->pendencia_model->delete('pendencias', $data, 'id_lancamento_fatura', $id_lancamento);

            $this->session->set_flashdata('sucesso', 'Lançamento excluído com sucesso!');
            redirect($urlAtual);

        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir lançamento de fatura.');
            redirect($urlAtual);
        }

    }

    public function abrir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para abrir novas faturas.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');
        $vencimento = $this->input->post('vencimento_fatura');

        try {
            $vencimento = explode('/', $vencimento);
            $vencimentoFormatado = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];

            $mes = ($vencimento[1] - 1);
            $ano = $vencimento[2];

            if ($mes == 0) {
                $mes = 12;
                $ano--;
            }
        } catch (Exception $e) {
            $vencimento = date('Y/m/d');
        }

        $data = array(
            'id_usuario' => id_usuario(),
            'id_cartao' => $_POST['id_cartao'],
            'mes_referencia' => $mes,
            'ano_referencia' => $ano,
            'vencimento' => $vencimentoFormatado,
        );

        if ($this->fatura_model->abrirFatura($data)) {
            $this->session->set_flashdata('sucesso', 'Fatura aberta com sucesso!');
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

        $urlAtual = $this->input->post('urlAtual');

        $faturaAtual = $this->fatura_model->getFaturaAtual($_POST['id_fatura']);
        $mes = $faturaAtual->mes_referencia;
        $ano = $faturaAtual->ano_referencia;

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
                'fatura_paga' => 2,
            );

            if ($this->fatura_model->edit('faturas', $data, 'id_fatura', $_POST['id_fatura'])) {
                $this->session->set_flashdata('sucesso', 'Fatura fechada com sucesso!');
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
            $this->session->set_flashdata('sucesso', 'Fatura excluída com sucesso!');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir a fatura.');
            redirect($urlAtual);
        }
    }

    public function pagar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para fechar faturas.');
            redirect(base_url());
        }
        $urlAtual = $this->input->post('urlAtual');

        if ($_REQUEST['data_pagamento']) {
            $data_pagamento = $_REQUEST['data_pagamento'];
        } else {
            $data_pagamento = date('d/m/Y');
        }

        try {
            $data_pagamento = explode('/', $data_pagamento);
            $data_pagamento = $data_pagamento[2] . '-' . $data_pagamento[1] . '-' . $data_pagamento[0];
        } catch (Exception $e) {
            $data_pagamento = date('Y/m/d');
        }

        $data = array(
            'fatura_paga' => 1,
            'data_pagamento' => $data_pagamento,
        );

        if ($this->fatura_model->edit('faturas', $data, 'id_fatura', $_POST['id_fatura'])) {
            if ($_POST['registrar']) {
                $valor_total = $this->fatura_model->getValorTotalFatura($_POST['id_fatura']);
                $data = array(
                    'id_usuario' => id_usuario(),
                    'descricao' => 'FATURA CARTAO DE CREDITO',
                    'valor' => '-' . $valor_total,
                    'data_lancamento' => $data_pagamento,
                    'data_pagamento' => $data_pagamento,
                    'cliente_fornecedor' => 'CARTAO DE CREDITO',
                    'forma_pgto' => $_POST['forma_pagamento'],
                    'baixado' => 1,
                    'tipo' => 2,
                );
                $this->financeiro_model->add('lancamentos', $data);
            }
            $this->session->set_flashdata('sucesso', 'Fatura paga com sucesso!');
            redirect($urlAtual);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar pagar a fatura.');
            redirect($urlAtual);
        }
    }

    public function pesquisaLancamentos()
    {
        $termo = $this->input->post('termo');
        $data['total'] = $this->faturas_model->getTotal(id_usuario());
        $data['formasPagamento'] = $this->faturas_model->getFormasPagamento();
        $data['results'] = $this->faturas_model->pesquisa($termo, id_usuario());
        $data['view'] = 'financeiro/lancamentos';
        $this->load->view('tema/topo', $data);
    }

    public function autoCompleteCliente()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->fatura_model->autoCompleteCliente($q, id_usuario());
        }
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
