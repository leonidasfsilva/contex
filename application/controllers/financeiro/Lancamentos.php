<?php

use Mpdf\Tag\Tr;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Lancamentos extends CI_Controller
{

    protected $global_url;
    protected $defaultMonthUser;
    protected $defaultYearUser;
    protected $defaultYearsList;
    protected $status;
    protected $tipo;
    protected $periodo;
    protected $inicio;
    protected $fim;
    protected $start;
    protected $perPage;
    protected $totalRows;
    protected $referenceMonth;
    protected $referenceYear;
    protected $preReferenceMonth;
    protected $nextReferenceMonth;
    protected $prevReferenceYear;
    protected $nextReferenceYear;
    protected $where;
    protected $limit;
    protected $orderBy;
    protected $queryString;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('FrontendRequestContext');
        $this->load->library('FrontendResponder');
        $this->load->library('LancamentosPresenter');
        $this->load->library('ApiFrontendFormatter');
        $this->load->library('ApiFrontendLancamentoInput');

        if ($this->frontendrequestcontext->expectsJson()) {
            $this->frontendrequestcontext->normalizeCollectionQuery();
        }

        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            if ($this->frontendrequestcontext->expectsJson()) {
                $this->frontendresponder->error(
                    'SESSION_INVALID',
                    'Sessão inválida ou expirada.',
                    401,
                    'mxcode/login'
                );
                $this->output->_display();
                exit;
            }
            redirect('mxcode/login');
        }

        $this->load->model('financeiro_model', '', true);
        $this->load->model('pendencia_model', '', true);
        $this->load->model('fatura_model', '', true);
        $this->load->model('clientes_model', '', true);
        $this->data['menuFinanceiro'] = 'Lancamentos';
        $this->global_url             = base_url('financeiro/lancamentos');
        $this->defaultMonthUser       = $this->configs_model->getMesPadraoUsuario(getUserId()) ?? null;
        $this->defaultYearUser        = $this->configs_model->getAnoPadraoUsuario(getUserId()) ?? null;
        $this->defaultYearsList       = getYearsList();

        $this->status             = $_GET['status'] ?? null;
        $this->tipo               = $_GET['tipo'] ?? null;
        $this->periodo            = $_GET['periodo'] ?? null;
        $this->inicio             = $_GET['dataInicial'] ?? null;
        $this->fim                = $_GET['dataFinal'] ?? null;
        $this->start              = $_GET['per_page'] ?? null;
        $this->referenceMonth     = $_GET['mesReferencia'] ?? null;
        $this->referenceYear      = $_GET['anoReferencia'] ?? null;
        $this->preReferenceMonth  = null;
        $this->nextReferenceMonth = null;
        $this->prevReferenceYear  = null;
        $this->nextReferenceYear  = null;
        $this->where              = null;
        $this->perPage            = null;
        $this->totalRows          = null;
        $this->limit              = null;
        $this->orderBy            = null;
        $this->queryString        = null;

        reconciliarFinanceiro(getUserId(), 'lancamentos');
    }

    public function index()
    {
        $this->lancamentos();
    }

    public function recurso($id = null)
    {
        if (!$this->frontendrequestcontext->expectsJson()) {
            return $this->frontendresponder->error('INVALID_REQUEST', 'Método não permitido.', 400, $this->global_url);
        }

        $method = $this->frontendrequestcontext->method();

        if ($method === 'GET') {
            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamentos')) {
                return $this->frontendresponder->error('PERMISSION_DENIED', 'Você não tem permissão para visualizar lançamentos.', 403, base_url());
            }

            $lancamento = $this->financeiro_model->getById($id, getUserId());

            if (!$lancamento) {
                return $this->frontendresponder->error('TRANSACTION_NOT_FOUND', 'Lançamento não encontrado.', 404, $this->global_url);
            }

            return $this->apifrontendresponse->success($this->lancamentospresenter->item($lancamento));
        }

        if ($method === 'PUT') {
            return $this->editar($id);
        }

        if ($method === 'DELETE') {
            return $this->excluir($id);
        }

        return $this->frontendresponder->error('INVALID_REQUEST', 'Método não permitido.', 400, $this->global_url);
    }

    // MODULO DE LANCAMENTOS
    public function lancamentos()
    {
        if ($this->frontendrequestcontext->expectsJson() && $this->frontendrequestcontext->method() === 'POST') {
            $input = $this->frontendrequestcontext->jsonInput();
            return isset($input['type']) && (int) $input['type'] === 1
                ? $this->entrada()
                : $this->saida();
        }

        if ($this->frontendrequestcontext->expectsJson() && $this->frontendrequestcontext->method() !== 'GET') {
            return $this->frontendresponder->error('INVALID_REQUEST', 'Método não permitido.', 400, $this->global_url);
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamentos')) {
            return $this->frontendresponder->error(
                'PERMISSION_DENIED',
                'Você não tem permissão para visualizar lançamentos.',
                403,
                base_url()
            );
        }

        $params['search'] = empty($this->input->get('search', TRUE)) ? null : $this->input->get('search', TRUE);
        if ($this->frontendrequestcontext->expectsJson()) {
            $params['per_page'] = (int) ($this->input->get('api_per_page', true) ?: 30);
        }
        $this->getPreBuildFunctions($params);
        $this->buildPagination();

        $this->data['yearsList']          = $this->defaultYearsList;
        $this->data['referenceMonth']     = $this->referenceMonth;
        $this->data['referenceYear']      = $this->referenceYear;
        $this->data['defaultMonth']       = $this->defaultMonthUser;
        $this->data['defaultYear']        = $this->defaultYearUser;
        $this->data['prevReferenceMonth'] = translateMonth($this->prevReferenceMonth ?? null, true, true);
        $this->data['nextReferenceMonth'] = translateMonth($this->nextReferenceMonth ?? null, true, true);
        $this->data['prevReferenceYear']  = $this->prevReferenceYear ?? null;
        $this->data['nextReferenceYear']  = $this->nextReferenceYear ?? null;
        $this->data['total_provisorio']   = $this->financeiro_model->getTotalProvisorio(getUserId());
        $this->data['saidas_pendentes']   = $this->financeiro_model->getSaidasPendentes(getUserId());
        $this->data['entradas_pendentes'] = $this->financeiro_model->getEntradasPendentes(getUserId());
        $this->data['total']              = $this->financeiro_model->getTotal(getUserId());
        $this->data['formasPagamento']    = $this->financeiro_model->getFormasPagamento();
        $this->data['results']            = $this->financeiro_model->get(
            'lancamentos',
            '*',
            getUserId(),
            $this->where,
            $this->limit,
            $this->totalRows,
            $this->perPage,
            $this->start,
            $this->orderBy ?: 'desc'
        );

        $this->data['hiddenItems'] = $this->financeiro_model->getHiddenItems(
            'lancamentos',
            '*',
            getUserId(),
            $this->where,
            $this->orderBy ?: 'desc'
        );

        if ($this->frontendrequestcontext->expectsJson()) {
            $this->prepareJsonCollectionContext($params['search']);
        }

        return $this->frontendresponder->view(
            'financeiro/lancamentos',
            $this->data,
            $this->lancamentospresenter->collection($this->data)
        );
    }

    public function entrada()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamentos')) {
            return $this->frontendresponder->error('PERMISSION_DENIED', 'Você não tem permissão para adicionar lançamentos.', 403, base_url());
        }

        if ($this->frontendrequestcontext->expectsJson()) {
            if (!$this->validateJsonLancamentoInput()) return;
            $this->frontendrequestcontext->applyLancamentoJsonToPost();
        }

        if ($this->input->post('urlAtual') != null) {
            $urlAtual = $this->input->post('urlAtual');
        } else {
            $urlAtual = $this->global_url;
        }

        $vencimentoRequest = $this->input->post('vencimento') ?? null;
        $vencimento        = date('Y-m-d');
        $recebimento       = $this->input->post('recebimento');
        $observacoes       = sanitizarString($this->input->post('observacoes')) ?? null;
        $queryString       = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);

        if ($vencimentoRequest) {
            $vencimento = explode('/', $vencimentoRequest);
            $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
        }

        if (!empty($queryString)) {
            parse_str($queryString, $parameters);
            // TODO check for key existence

            if ($parameters['periodo'] == 'mensal' && !$vencimentoRequest) {
                try {
                    $vencimento = explode('-', $vencimento);
                    $vencimento = $parameters['anoReferencia'] . '-' . $parameters['mesReferencia'] . '-' . $vencimento[2];
                } catch (Exception $e) {

                }
            }
        }

        if ($this->defaultMonthUser && !$queryString) {
            try {
                $vencimento = explode('-', $vencimento);
                $vencimento = $vencimento[0] . '-' . $this->defaultMonthUser . '-' . $vencimento[2];
            } catch (Exception $e) {

            }
        }

        if ($recebimento) {
            $recebimento = explode('/', $recebimento);
            $recebimento = $recebimento[2] . '-' . $recebimento[1] . '-' . $recebimento[0];
        }

        $valor = $this->input->post('valor');

        if (!validate_money($valor)) {
            $valor = str_replace(array('.', ','), array('', '.'), $valor);
        }

        $data = array(
            'descricao'          => padronizarString($this->input->post('descricao')),
            'observacoes'        => $observacoes,
            'valor'              => $valor,
            'id_usuario'         => getUserId(),
            'data_lancamento'    => $vencimento,
            'data_pagamento'     => $recebimento != null ? $recebimento : $vencimento,
            'baixado'            => $this->input->post('pago') ?? 0,
            'oculto'             => $this->input->post('oculto') ?? null,
            'cliente_fornecedor' => padronizarString($this->input->post('fornecedor')),
            'forma_pgto'         => ($this->input->post('formaPgto') ?: 6),
            'tipo'               => 1
        );

        if ($this->financeiro_model->add('lancamentos', $data) == true) {
            $created = $this->financeiro_model->getById($this->db->insert_id(), getUserId());
            return $this->frontendresponder->success(
                'Entrada registrada com sucesso',
                $urlAtual,
                $created ? $this->lancamentospresenter->item($created) : array(),
                201
            );
        }

        return $this->frontendresponder->error('TRANSACTION_CREATE_FAILED', 'Ocorreu um erro ao tentar registrar entrada.', 500, $urlAtual);
    }

    public function saida()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamentos')) {
            return $this->frontendresponder->error('PERMISSION_DENIED', 'Você não tem permissão para adicionar lançamentos.', 403, base_url());
        }

        if ($this->frontendrequestcontext->expectsJson()) {
            if (!$this->validateJsonLancamentoInput()) return;
            $this->frontendrequestcontext->applyLancamentoJsonToPost();
        }

        if ($this->input->post('urlAtual') != null) {
            $urlAtual = $this->input->post('urlAtual');
        } else {
            $urlAtual = $this->global_url;
        }

        $vencimentoRequest = $this->input->post('vencimento') ?? null;
        $vencimento        = date('Y-m-d');
        $pagamento         = $this->input->post('pagamento');
        $observacoes       = sanitizarString($this->input->post('observacoes')) ?? null;
        $queryString       = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);

        if ($vencimentoRequest) {
            $vencimento = explode('/', $vencimentoRequest);
            $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
        }

        if (!empty($queryString)) {
            parse_str($queryString, $parameters);
            // TODO check for key existence

            if ($parameters['periodo'] == 'mensal' && !$vencimentoRequest) {
                try {
                    $vencimento = explode('-', $vencimento);
                    $vencimento = $parameters['anoReferencia'] . '-' . $parameters['mesReferencia'] . '-' . $vencimento[2];
                } catch (Exception $e) {

                }
            }
        }

        if ($this->defaultMonthUser && !$queryString) {
            try {
                $vencimento = explode('-', $vencimento);
                $vencimento = $vencimento[0] . '-' . $this->defaultMonthUser . '-' . $vencimento[2];
            } catch (Exception $e) {

            }
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
            'descricao'          => padronizarString($this->input->post('descricao')),
            'observacoes'        => $observacoes,
            'valor'              => $valor,
            'id_usuario'         => getUserId(),
            'data_lancamento'    => $vencimento,
            'data_pagamento'     => $pagamento != null ? $pagamento : $vencimento,
            'baixado'            => $this->input->post('pago') ?? 0,
            'oculto'             => $this->input->post('oculto') ?? null,
            'cliente_fornecedor' => padronizarString($this->input->post('fornecedor')),
            'forma_pgto'         => ($this->input->post('formaPgto') ?: 3),
            'tipo'               => 2
        );

        if ($this->financeiro_model->add('lancamentos', $data) == true) {
            $created = $this->financeiro_model->getById($this->db->insert_id(), getUserId());
            return $this->frontendresponder->success(
                'Saída registrada com sucesso',
                $urlAtual,
                $created ? $this->lancamentospresenter->item($created) : array(),
                201
            );
        }

        return $this->frontendresponder->error('TRANSACTION_CREATE_FAILED', 'Ocorreu um erro ao tentar registrar saída.', 500, $urlAtual);
    }

    public function editar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamentos')) {
            return $this->frontendresponder->error('PERMISSION_DENIED', 'Você não tem permissão para editar lançamentos.', 403, base_url());
        }

        if ($this->frontendrequestcontext->expectsJson()) {
            if (!$this->validateJsonLancamentoInput()) return;
            $this->frontendrequestcontext->applyLancamentoJsonToPost($id);
        }

        if (!$this->input->post()) {
            return $this->frontendresponder->error('INVALID_REQUEST', 'Método não permitido.', 400, $this->global_url);
        }

        if ($this->input->post('urlAtual') != null) {
            $urlAtual = $this->input->post('urlAtual');
        } else {
            $urlAtual = $this->global_url;
        }

        $vencimento  = $this->input->post('vencimento');
        $pagamento   = $this->input->post('pagamento');
        $observacoes = sanitizarString($this->input->post('observacoes')) ?? null;

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

        $tipo  = ($this->input->post('tipo'));
        $valor = $this->input->post('valor');

        if ($tipo == 2) {
            $valor = '-' . $valor;
        }

        $valor = str_replace(array('.', ','), array('', '.'), $valor);

        $data = array(
            'descricao'          => padronizarString($this->input->post('descricao')),
            'observacoes'        => $observacoes,
            'valor'              => $valor,
            'data_lancamento'    => $vencimento,
            'data_pagamento'     => $pagamento != null ? $pagamento : $vencimento,
            'baixado'            => $this->input->post('pago') ?? 0,
            'oculto'             => $this->input->post('oculto') ?? null,
            'cliente_fornecedor' => padronizarString($this->input->post('fornecedor')),
            'forma_pgto'         => ($this->input->post('formaPgto')),
            'tipo'               => $tipo
        );

        $idLancamento = $this->input->post('id');

        if ($this->financeiro_model->edit('lancamentos', $data, 'id_lancamento', $idLancamento)) {
            sincronizaPagamentoTerceiroPorLancamento($idLancamento, getUserId(), $data['baixado'] == 1);

            $lancamento = $this->financeiro_model->getById($idLancamento, getUserId());

            if ($lancamento && $lancamento->id_fatura) {
                monitoraPagamentosFaturasVinculadas($lancamento->id_fatura);
            }
            return $this->frontendresponder->success(
                'Lançamento alterado com sucesso',
                $urlAtual,
                $lancamento ? $this->lancamentospresenter->item($lancamento) : array()
            );
        }

        return $this->frontendresponder->error('TRANSACTION_UPDATE_FAILED', 'Ocorreu um erro ao tentar alterar o registro.', 500, $urlAtual);
    }

    public function copiar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamentos')) {
            return $this->frontendresponder->error('PERMISSION_DENIED', 'Você não tem permissão para copiar lançamentos.', 403, base_url());
        }

        if ($this->frontendrequestcontext->expectsJson()) {
            if (!$this->validateJsonLancamentoInput()) return;
            $this->frontendrequestcontext->applyLancamentoJsonToPost($id);
        }

        if (!$this->input->post()) {
            return $this->frontendresponder->error('INVALID_REQUEST', 'Método não permitido.', 400, $this->global_url);
        }

        if ($this->input->post('urlAtual') != null) {
            $urlAtual = $this->input->post('urlAtual');
        } else {
            $urlAtual = $this->global_url;
        }

        $id          = $this->input->post('id');
        $vencimento  = $this->input->post('vencimento');
        $pagamento   = $this->input->post('pagamento');
        $observacoes = sanitizarString($this->input->post('observacoes')) ?? null;

        if ($vencimento != null) {
            $vencimento = explode('/', $vencimento);
            $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];
        } else {
            $vencimento = date('Y-m-d');
        }

        if (is_array($id) && count($id) > 0) {
            $id   = array_reverse($id);
            $data = [];

            foreach ($id as $value) {
                $lancamento = $this->financeiro_model->getById($value, getUserId());

                if ($lancamento) {
                    $data = [
                        'descricao'          => $lancamento->descricao,
                        'observacoes'        => $lancamento->observacoes,
                        'valor'              => $lancamento->valor,
                        'data_lancamento'    => $vencimento,
                        'data_pagamento'     => $lancamento->data_pagamento ?? $vencimento,
                        'baixado'            => $lancamento->baixado,
                        'oculto'             => $lancamento->oculto ?? null,
                        'cliente_fornecedor' => $lancamento->cliente_fornecedor,
                        'forma_pgto'         => $lancamento->forma_pgto,
                        'tipo'               => $lancamento->tipo,
                        'id_usuario'         => getUserId()
                    ];
                }

                if (!$this->financeiro_model->add('lancamentos', $data)) {
                    return $this->frontendresponder->error('TRANSACTION_COPY_FAILED', 'Ocorreu um erro ao tentar copiar a série de lançamentos.', 500, $urlAtual);
                }
            }
            return $this->frontendresponder->success('Série de lançamentos copiada com sucesso', $urlAtual);
        }

        if ($pagamento != null) {
            $pagamento = explode('/', $pagamento);
            $pagamento = $pagamento[2] . '-' . $pagamento[1] . '-' . $pagamento[0];
        }

        $tipo  = ($this->input->post('tipo'));
        $valor = $this->input->post('valor');

        if ($tipo == 2) {
            $valor = '-' . $valor;
        }

        $valor = str_replace(array('.', ','), array('', '.'), $valor);

        $data = array(
            'descricao'          => padronizarString($this->input->post('descricao')),
            'observacoes'        => $observacoes,
            'valor'              => $valor,
            'data_lancamento'    => $vencimento,
            'data_pagamento'     => $pagamento != null ? $pagamento : $vencimento,
            'baixado'            => $this->input->post('pago') ?? 0,
            'oculto'             => $this->input->post('oculto') ?? null,
            'cliente_fornecedor' => padronizarString($this->input->post('fornecedor')),
            'forma_pgto'         => ($this->input->post('formaPgto')),
            'tipo'               => $tipo,
            'id_usuario'         => getUserId()
        );

        if (!$this->financeiro_model->add('lancamentos', $data)) {
            return $this->frontendresponder->error('TRANSACTION_COPY_FAILED', 'Ocorreu um erro ao tentar copiar o registro.', 500, $urlAtual);
        }

        $created = $this->financeiro_model->getById($this->db->insert_id(), getUserId());
        return $this->frontendresponder->success(
            'Lançamento copiado com sucesso',
            $urlAtual,
            $created ? $this->lancamentospresenter->item($created) : array(),
            201
        );
    }

    public function excluir($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamentos')) {
            return $this->frontendresponder->error('PERMISSION_DENIED', 'Você não tem permissão para excluir lançamentos.', 403, $this->global_url);
        }

        $this->frontendrequestcontext->applyIdsToPost($id);

        if ($this->frontendrequestcontext->expectsJson() && !$this->validateJsonWriteRequest(false)) {
            return;
        }
        $id = $this->input->post('id');

        if ($this->input->post('urlAtual') != null) {
            $urlAtual = $this->input->post('urlAtual');
        } else {
            $urlAtual = $this->global_url;
        }

        if (!$id) {
            return $this->frontendresponder->error('INVALID_REQUEST', 'Método não permitido.', 400, $this->global_url);
        }

        $data = array(
            'status' => 0
        );

        if (is_array($id) && count($id) > 0) {
            foreach ($id as $value) {
                $this->financeiro_model->delete('lancamentos', $data, 'id_lancamento', $value);
            }

            return $this->frontendresponder->noContent('Série de lançamentos excluída com sucesso', $urlAtual);
        }

        if ($this->financeiro_model->delete('lancamentos', $data, 'id_lancamento', $id) == true) {
            return $this->frontendresponder->noContent('Lançamento excluído com sucesso', $urlAtual);
        }

        return $this->frontendresponder->error('TRANSACTION_DELETE_FAILED', 'Erro ao tentar excluir lançamento.', 500, $urlAtual);
    }

    public function _pesquisa()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if ($this->input->get('search') == null) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect($this->global_url);
        }
        $search             = $this->input->get('search', TRUE);
        $params['base_url'] = base_url('financeiro/lancamentos/pesquisa');
        $params['per_page'] = 20;
        $params['search']   = $search;

        $this->getPreBuildFunctions($params);
        $this->buildPagination($params);
        $this->load->library('pagination');
        $this->data['yearsList']          = $this->defaultYearsList;
        $this->data['referenceMonth']     = $this->referenceMonth;
        $this->data['referenceYear']      = $this->referenceYear;
        $this->data['defaultMonth']       = $this->defaultMonthUser;
        $this->data['prevReferenceMonth'] = translateMonth($this->prevReferenceMonth ?? null, true, true);
        $this->data['nextReferenceMonth'] = translateMonth($this->nextReferenceMonth ?? null, true, true);
        $this->data['prevReferenceYear']  = $this->prevReferenceYear ?? null;
        $this->data['nextReferenceYear']  = $this->nextReferenceYear ?? null;
        $this->data['total']              = $this->financeiro_model->getTotal(getUserId());
        $this->data['formasPagamento']    = $this->financeiro_model->getFormasPagamento();
        $this->data['results']            = $this->financeiro_model->get(
            'lancamentos',
            '*',
            getUserId(),
            $this->where,
            $this->limit,
            $config['total_rows'] ?? null,
            $config['per_page'] ?? null,
            $this->perPage,
            $this->orderBy ?: 'desc',
        );

        $this->data['hiddenItems'] = $this->financeiro_model->getHiddenItems(
            'lancamentos',
            '*',
            getUserId(),
            $this->where,
            $this->orderBy ?: 'desc',
        );

        $this->data['view'] = 'financeiro/lancamentos';
        $this->load->view('tema/topo', $this->data);
    }

    public function setMesAnoPadrao()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamentos')) {
            return $this->frontendresponder->error('PERMISSION_DENIED', 'Você não tem permissão para configurar o módulo de Lançamentos.', 403, base_url());
        }

        if ($this->frontendrequestcontext->expectsJson()) {
            if (!$this->validateJsonWriteRequest()) return;
            $input = $this->frontendrequestcontext->jsonInput();
            $_POST = array(
                'mesPadrao' => $input['month'] ?? null,
                'anoPadrao' => $input['year'] ?? null,
            );
        }

        if (!$this->input->post()) {
            return $this->frontendresponder->error('INVALID_REQUEST', 'Método não permitido.', 400, $this->global_url);
        }

        if ($this->input->post('urlAtual') != null) {
            $urlAtual = $this->input->post('urlAtual');
        } else {
            $urlAtual = $this->global_url;
        }

        $mes = $this->input->post('mesPadrao');
        $ano = $this->input->post('anoPadrao');

        $data = array(
            'mes_padrao' => !empty($mes) ? $mes : null,
            'ano_padrao' => !empty($ano) ? $ano : null,
            'id_usuario' => getUserId()
        );

        $this->configs_model->unsetMesPadraoUsuario(getUserId());

        if (!$this->configs_model->add('configs_lancamentos', $data)) {
            return $this->frontendresponder->error('SETTINGS_UPDATE_FAILED', 'Ocorreu um erro ao tentar salvar as configurações.', 500, $urlAtual);
        }

        return $this->frontendresponder->success('Configurações salvas com sucesso', $urlAtual, array(
            'month' => $data['mes_padrao'],
            'year' => $data['ano_padrao'],
        ));
    }

    protected function filterHiddenEntries(array $array)
    {
        $hiddenItems = null;

        foreach ($array as $key => $item) {
            if ($item->oculto == 1) {
                $hiddenItems[] = $item;
                unset($array[$key]);
            }
        }

        $this->data['hiddenItems'] = $hiddenItems;
        return $array;
    }

    protected function prepareJsonCollectionContext($search = null)
    {
        $offset = (int) ($this->start ?: 0);
        $this->data['aggregateResults'] = $this->financeiro_model->get(
            'lancamentos',
            '*',
            getUserId(),
            $this->where,
            null,
            0,
            0,
            0,
            $this->orderBy ?: 'desc'
        );
        $this->data['perPage'] = (int) ($this->perPage ?: 30);
        $this->data['totalRows'] = (int) $this->totalRows;
        $this->data['currentPage'] = (int) floor($offset / max(1, $this->data['perPage'])) + 1;
        $this->data['totalPages'] = $this->totalRows === 0
            ? 0
            : (int) ceil($this->totalRows / $this->data['perPage']);
        $this->data['prevReferenceMonthNumber'] = $this->prevReferenceMonth;
        $this->data['nextReferenceMonthNumber'] = $this->nextReferenceMonth;
        $this->data['filters'] = array(
            'search' => $search ?: null,
            'period' => $this->periodo ?: 'mensal',
            'month' => $this->referenceMonth ? (int) $this->referenceMonth : null,
            'year' => $this->referenceYear ? (int) $this->referenceYear : null,
            'status' => $this->status ?: null,
            'type' => $this->tipo ?: null,
        );
    }

    protected function validateJsonLancamentoInput()
    {
        if (!$this->frontendrequestcontext->expectsJson()) {
            return true;
        }

        if (!$this->validateJsonWriteRequest()) {
            return false;
        }

        $validation = $this->apifrontendlancamentoinput->validate(
            $this->frontendrequestcontext->jsonInput()
        );

        if (!$validation['valid']) {
            $this->frontendresponder->error(
                'VALIDATION_ERROR',
                'Verifique os campos informados.',
                422,
                $this->global_url,
                array('fields' => $validation['fields'])
            );
            return false;
        }

        return true;
    }

    protected function validateJsonWriteRequest($requireBody = true)
    {
        if (!$this->frontendrequestcontext->expectsJson()) {
            return true;
        }

        $this->load->library('ApiCsrf', null, 'api_csrf');
        $token = $this->input->get_request_header('X-CSRF-TOKEN', true);

        if (!$this->api_csrf->isValid($token)) {
            $this->frontendresponder->error('CSRF_TOKEN_INVALID', 'Token CSRF ausente ou inválido.', 403, $this->global_url);
            return false;
        }

        if ($requireBody && !$this->frontendrequestcontext->jsonIsValid()) {
            $this->frontendresponder->error('INVALID_JSON', 'O corpo JSON enviado é inválido.', 400, $this->global_url);
            return false;
        }

        return true;
    }

    public function autoCompleteDescricao()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            return $this->financeiro_model->autoCompleteDescricao($q, getUserId());
        }
    }

    public function autoCompleteFornecedor()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            return $this->financeiro_model->autoCompleteFornecedor($q, getUserId());
        }
    }

    // MODULO DE TESTES
    public function getTeste($id = null)
    {

        $pendencia = $this->pendencia_model->getById($id);
        $cliente   = $this->clientes_model->getById($pendencia->id_cliente);

        print_array($pendencia);
        print_array($cliente);
        echo 'TESTE OK!';
    }

    // MODULO DE RETORNO DE FILTROS POR PERIODO
    protected function getThisYear()
    {
        $dias     = date("z");
        $primeiro = date("Y-m-d", strtotime("-" . ($dias) . " day"));
        $ultimo   = date("Y-m-d", strtotime("+" . (364 - $dias) . " day"));

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
        $mes        = date('m');
        $ano        = date('Y');
        $qtdDiasMes = date('t');
        $inicia     = $ano . "-" . $mes . "-01";
        $ate        = $ano . "-" . $mes . "-" . $qtdDiasMes;

        return array($inicia, $ate);
    }

    protected function getPreBuildFunctions($params = null)
    {
        $this->orderBy = [
            'data_lancamento' => 'desc',
            'id_lancamento'   => 'desc',
        ];

        if ($params) {
            if (isset($params['search'])) {
                $this->where = sprintf(
                    "(descricao LIKE '%%%1\$s%%' || cliente_fornecedor LIKE '%%%1\$s%%' || observacoes LIKE '%%%1\$s%%')",
                    $params['search']
                );
                // if (!$this->start)
                $this->periodo = 'todos';
            }
        }

        switch ($this->periodo) {
            case 'todos':
                $startEndDate = buildStartEndDate($this->defaultMonthUser, ($this->referenceYear) ?: null);
                // $this->referenceMonth = $startEndDate['referenceMonth'];

                if (!isset($this->referenceYear) && !$this->referenceYear) {
                    $this->referenceYear = $startEndDate['referenceYear'];
                }
                break;
            case '3dias':
                $semana      = $this->getLastThreeDays();
                $this->where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '5dias':
                $semana      = $this->getLastFiveDays();
                $this->where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '7dias':
                $semana      = $this->getLastSevenDays();
                $this->where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '15dias':
                $semana      = $this->getLastFifteenDays();
                $this->where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '30dias':
                $semana      = $this->getLastTirthyDays();
                $this->where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '60dias':
                $semana      = $this->getLastSixtyDays();
                $this->where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case '90dias':
                $semana      = $this->getLastNinetyDays();
                $this->where = 'data_lancamento BETWEEN "' . $semana[0] . '" AND "' . $semana[1] . '"';
                break;
            case 'especifico':
                if (isset($this->inicio) && isset($this->fim) && $this->inicio != null && $this->fim != null) {
                    $this->inicio = explode('/', $this->inicio);
                    $this->inicio = $this->inicio[2] . '-' . $this->inicio[1] . '-' . $this->inicio[0];

                    $this->fim = explode('/', $this->fim);
                    $this->fim = $this->fim[2] . '-' . $this->fim[1] . '-' . $this->fim[0];

                    if (!isset($this->where)) {
                        $this->where = 'data_lancamento BETWEEN "' . $this->inicio . '" AND "' . $this->fim . '"';
                    } else {
                        $this->where .= ' AND data_lancamento BETWEEN "' . $this->inicio . '" AND "' . $this->fim . '"';
                    }
                }
                break;
            case 'mensal':
                if (isset($this->referenceMonth) && $this->referenceMonth) {
                    $startEndDate = buildStartEndDate($this->referenceMonth, ($this->referenceYear) ?: null);

                    if (!isset($this->referenceYear) && !$this->referenceYear) {
                        $this->referenceYear = $startEndDate['referenceYear'];
                    }

                    if (!isset($this->where)) {
                        $this->where = "data_lancamento BETWEEN '{$startEndDate['startDate']}' AND '{$startEndDate['endDate']}'";
                    } else {
                        $this->where .= " AND data_lancamento BETWEEN '{$startEndDate['startDate']}' AND '{$startEndDate['endDate']}'";
                    }
                }
                break;
            default:
                $startEndDate         = buildStartEndDate($this->defaultMonthUser, ($this->defaultYearUser) ?: null);
                $this->referenceMonth = $startEndDate['referenceMonth'];

                if (!isset($this->referenceYear) && !$this->referenceYear) {
                    $this->referenceYear = $startEndDate['referenceYear'];
                }

                if (!isset($this->where)) {
                    $this->where = "data_lancamento BETWEEN '{$startEndDate['startDate']}' AND '{$startEndDate['endDate']}'";
                } else {
                    $this->where .= " AND data_lancamento BETWEEN '{$startEndDate['startDate']}' AND '{$startEndDate['endDate']}'";
                }

                if ($this->financeiro_model->countLancamentos(getUserId(), $this->where) > 20) {
                    // $limit = 20;
                    // $start = $this->financeiro_model->countLancamentos(getUserId()) - $limit;
                }
                break;
        }

        if ($this->status) {
            if ($this->status == 'pendente') {
                if (!isset($this->where)) {
                    $this->where = 'baixado = 0';
                } else {
                    $this->where .= ' AND baixado = 0';
                }
            }

            if ($this->status == 'efetivado') {
                if (!isset($this->where)) {
                    $this->where = 'baixado = 1';
                } else {
                    $this->where .= ' AND baixado = 1';
                }
            }
        }

        if ($this->tipo) {
            if ($this->tipo == 'entrada') {
                if (!isset($this->where)) {
                    $this->where = 'tipo = 1';
                } else {
                    $this->where .= ' AND tipo = 1';
                }
            } elseif ($this->tipo == 'saida') {
                if (!isset($this->where)) {
                    $this->where = 'tipo = 2';
                } else {
                    $this->where .= ' AND tipo = 2';
                }
            }
        }

        if ($this->referenceMonth) {
            $this->data['month']      = translateMonth($this->referenceMonth);
            $this->data['nextMonth']  = translateMonth($this->referenceMonth + 1);
            $this->data['prevMonth']  = translateMonth($this->referenceMonth - 1);
            $this->prevReferenceMonth = $this->referenceMonth - 1;
            $this->nextReferenceMonth = $this->referenceMonth + 1;
            $this->prevReferenceYear  = $this->referenceYear;
            $this->nextReferenceYear  = $this->referenceYear;
        }

        if ($this->referenceMonth == 12) {
            $this->nextReferenceMonth = 1;
            $this->nextReferenceYear++;
        };

        if ($this->referenceMonth == 1) {
            $this->prevReferenceMonth = 12;
            $this->prevReferenceYear--;
        }

        for ($m = 1; $m <= 12; $m++) {
            $currentMonth                                                             = buildStartEndDate(translateMonth($m, true, true), $this->referenceYear);
            $this->data['monthList'][$currentMonth["referenceMonth"]]['name']         = translateMonth($m, true, true) . ' - ' . translateMonth($m, true);
            $this->data['monthList'][$currentMonth["referenceMonth"]]['notification'] = $this->financeiro_model->getLancamentosPendentes(getUserId(), $currentMonth['startDate'], $currentMonth['endDate']);
        }
    }

    protected function buildPagination($params = null)
    {
        $lastElement = end($_GET);

        foreach ($_GET as $key => $value) {
            if (!in_array($key, array('per_page', 'api_per_page', 'page', 'perPage', 'sortDirection'), true)) {
                if ($value == $lastElement) {
                    $this->queryString .= $key . '=' . $value;
                    continue;
                }
                $this->queryString .= $key . '=' . $value . '&';
            }
        }

        $config['base_url']          = base_url(uri_string());
        $config['suffix']            = '&' . $this->queryString;
        $config['first_url']         = sprintf('%s?%s', $config['base_url'], $this->queryString);
        $config['total_rows']        = $this->financeiro_model->countLancamentos(getUserId(), $this->where ?? null);
        $config['per_page']          = $params['per_page'] ?? 30;
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
        $this->perPage               = $config['per_page'];
        $this->totalRows             = $config['total_rows'];

        $this->pagination->initialize($config);
    }
}
