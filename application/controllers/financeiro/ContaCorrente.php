<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ContaCorrente extends CI_Controller
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
        $where = '';
        $periodo = $this->input->get('periodo');
        $situacao = $this->input->get('situacao');

        // busca todos os lançamentos
        if ($periodo == 'todos') {

            if ($situacao == 'previsto') {
                $where = 'data_lancamento > "' . date('Y-m-d') . '" AND baixado = 0';
            } else {
                if ($situacao == 'atrasado') {
                    $where = 'data_lancamento < "' . date('Y-m-d') . '" AND baixado = 0';
                } else {
                    if ($situacao == 'realizado') {
                        $where = 'baixado = 1';
                    }

                    if ($situacao == 'pendente') {
                        $where = 'baixado = 0';
                    }
                }
            }
        }
        else {

            // busca lançamentos do dia
            if ($periodo == '7dias') {
                $semana = $this->getLastSevenDays();

                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

            } else if ($periodo == null) {
                $limit = 5;
            } else {

                // busca lançamentos da semana
                if ($periodo == '5dias') {
                    $semana = $this->getLastFiveDays();

                    if (!isset($situacao) || $situacao == 'todos') {

                        $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                    } else {
                        if ($situacao == 'previsto') {
                            $where = 'data_lancamento BETWEEN "' . date('Y-m-d') . '" AND "' . $semana[1] . '" AND baixado = "0"';
                        } else {
                            if ($situacao == 'atrasado') {
                                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . date('Y-m-d') . '" AND baixado = "0"';
                            } else {
                                if ($situacao == 'realizado') {
                                    $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "1"';
                                } else {
                                    $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                }
                            }
                        }
                    }

                } else {

                    // busca lançamentos da semana
                    if ($periodo == '3dias') {
                        $semana = $this->getLastThreeDays();

                        if (!isset($situacao) || $situacao == 'todos') {

                            $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                        } else {
                            if ($situacao == 'previsto') {
                                $where = 'data_lancamento BETWEEN "' . date('Y-m-d') . '" AND "' . $semana[1] . '" AND baixado = "0"';
                            } else {
                                if ($situacao == 'atrasado') {
                                    $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . date('Y-m-d') . '" AND baixado = "0"';
                                } else {
                                    if ($situacao == 'realizado') {
                                        $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "1"';
                                    } else {
                                        $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                    }
                                }
                            }
                        }

                    } else {
                        // busca lançamentos da semana
                        if ($periodo == '15dias') {
                            $semana = $this->getLastFifteenDays();

                            if (!isset($situacao) || $situacao == 'todos') {

                                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                            } else {
                                if ($situacao == 'previsto') {
                                    $where = 'data_lancamento BETWEEN "' . date('Y-m-d') . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                } else {
                                    if ($situacao == 'atrasado') {
                                        $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . date('Y-m-d') . '" AND baixado = "0"';
                                    } else {
                                        if ($situacao == 'realizado') {
                                            $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "1"';
                                        } else {
                                            $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                        }
                                    }
                                }
                            }

                        } else {

                            // busca lançamentos da semana
                            if ($periodo == '30dias') {
                                $semana = $this->getLastTirthyDays();

                                if (!isset($situacao) || $situacao == 'todos') {

                                    $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                                } else {
                                    if ($situacao == 'previsto') {
                                        $where = 'data_lancamento BETWEEN "' . date('Y-m-d') . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                    } else {
                                        if ($situacao == 'atrasado') {
                                            $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . date('Y-m-d') . '" AND baixado = "0"';
                                        } else {
                                            if ($situacao == 'realizado') {
                                                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "1"';
                                            } else {
                                                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                            }
                                        }
                                    }
                                }

                            } else {

                                // busca lançamentos da semana
                                if ($periodo == '60dias') {
                                    $semana = $this->getLastSixtyDays();

                                    if (!isset($situacao) || $situacao == 'todos') {

                                        $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                                    } else {
                                        if ($situacao == 'previsto') {
                                            $where = 'data_lancamento BETWEEN "' . date('Y-m-d') . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                        } else {
                                            if ($situacao == 'atrasado') {
                                                $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . date('Y-m-d') . '" AND baixado = "0"';
                                            } else {
                                                if ($situacao == 'realizado') {
                                                    $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "1"';
                                                } else {
                                                    $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                                }
                                            }
                                        }
                                    }

                                } else {

                                    // busca lançamentos da semana
                                    if ($periodo == '90dias') {
                                        $semana = $this->getLastNinetyDays();

                                        if (!isset($situacao) || $situacao == 'todos') {

                                            $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';

                                        } else {
                                            if ($situacao == 'previsto') {
                                                $where = 'data_lancamento BETWEEN "' . date('Y-m-d') . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                            } else {
                                                if ($situacao == 'atrasado') {
                                                    $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . date('Y-m-d') . '" AND baixado = "0"';
                                                } else {
                                                    if ($situacao == 'realizado') {
                                                        $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "1"';
                                                    } else {
                                                        $where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '" AND baixado = "0"';
                                                    }
                                                }
                                            }
                                        }

                                    } else {

                                        // busca lançamento do mês


                                        if ($periodo == 'mes') {

                                            $mes = $this->getThisMonth();

                                            if (!isset($situacao) || $situacao == 'todos') {

                                                $where = 'data_lancamento BETWEEN "' . $mes[0] . '" AND "' . $mes[1] . '"';

                                            } else {
                                                if ($situacao == 'previsto') {
                                                    $where = 'data_lancamento BETWEEN "' . date('Y-m-d') . '" AND "' . $mes[1] . '" AND baixado = "0"';
                                                } else {
                                                    if ($situacao == 'atrasado') {
                                                        $where = 'data_lancamento BETWEEN "' . $mes[0] . '" AND "' . date('Y-m-d') . '" AND baixado = "0"';
                                                    } else {
                                                        if ($situacao == 'realizado') {
                                                            $where = 'data_lancamento BETWEEN "' . $mes[0] . '" AND "' . $mes[1] . '" AND baixado = "1"';
                                                        } else {
                                                            $where = 'data_lancamento BETWEEN "' . $mes[0] . '" AND "' . $mes[1] . '" AND baixado = "0"';
                                                        }

                                                    }
                                                }
                                            }
                                        } // busca lançamentos do ano
                                        else {
                                            $ano = $this->getThisYear();

                                            if (!isset($situacao) || $situacao == 'todos') {

                                                $where = 'data_lancamento BETWEEN "' . $ano[0] . '" AND "' . $ano[1] . '"';

                                            } else {
                                                if ($situacao == 'previsto') {
                                                    $where = 'data_lancamento BETWEEN "' . date('Y-m-d') . '" AND "' . $ano[1] . '" AND baixado = "0"';
                                                } else {
                                                    if ($situacao == 'atrasado') {
                                                        $where = 'data_lancamento BETWEEN "' . $ano[0] . '" AND "' . date('Y-m-d') . '" AND baixado = "0"';
                                                    } else {
                                                        if ($situacao == 'realizado') {
                                                            $where = 'data_lancamento BETWEEN "' . $ano[0] . '" AND "' . $ano[1] . '" AND baixado = "1"';
                                                        } else {
                                                            $where = 'data_lancamento BETWEEN "' . $ano[0] . '" AND "' . $ano[1] . '" AND baixado = "0"';
                                                        }

                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->load->library('pagination');

        $config['base_url'] = site_url() . 'financeiro/lancamentos/?periodo=' . $periodo . '&situacao=' . $situacao;
        $config['total_rows'] = $this->financeiro_model->count('lancamentos', 'status = 1 AND id_usuario = ' . id_usuario());
        $config['per_page'] = 100;
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
            'idLancamentos,descricao,valor,data_lancamento,data_pagamento,baixado,cliente_fornecedor,tipo,forma_pgto',
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
            'forma_pgto' => ($this->input->post('formaPgto')),
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
            'forma_pgto' => ($this->input->post('formaPgto')),
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
//        print_array($_REQUEST);

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

            if ($this->financeiro_model->edit('lancamentos', $data, 'idLancamentos', $this->input->post('id')) == true) {
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

            if ($this->financeiro_model->delete('lancamentos', $data, 'idLancamentos', $id) == true) {
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
