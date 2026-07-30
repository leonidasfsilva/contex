<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Lancamentos extends API_Frontend_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('financeiro_model');
        $this->load->library('ApiFrontendFormatter');
        $this->load->library('ApiFrontendLancamentoInput');
    }

    public function collection()
    {
        $method = strtoupper((string) $this->input->method(true));

        if ($method === 'GET') {
            return $this->index();
        }

        if ($method === 'POST') {
            return $this->create();
        }

        return $this->invalidMethod(array('GET', 'POST'), $method);
    }

    public function resource($id = null)
    {
        $method = strtoupper((string) $this->input->method(true));

        if ($method === 'GET') {
            return $this->show($id);
        }

        if ($method === 'PUT') {
            return $this->update($id);
        }

        if ($method === 'DELETE') {
            return $this->delete($id);
        }

        return $this->invalidMethod(array('GET', 'PUT', 'DELETE'), $method);
    }

    private function index()
    {
        $this->requireMethod('GET');
        $this->requirePermission('vLancamentos');

        $page = $this->positiveIntegerQuery('page', 1);
        $perPage = $this->positiveIntegerQuery('perPage', 30, 100);
        $offset = ($page - 1) * $perPage;
        $search = trim((string) $this->input->get('search', true));
        $sortDirection = strtolower((string) $this->input->get('sortDirection', true));

        if ($sortDirection === '') {
            $sortDirection = 'desc';
        }

        if (!in_array($sortDirection, array('asc', 'desc'), true)) {
            return $this->apiResponse->error(
                'INVALID_QUERY_PARAMETER',
                'Um ou mais parâmetros da consulta são inválidos.',
                400,
                array('fields' => array('sortDirection' => 'Use asc ou desc.'))
            );
        }

        $idUsuario = (int) $this->session->userdata('id');
        $total = $this->financeiro_model->countLancamentosApiFrontend($idUsuario, $search);
        $rows = $this->financeiro_model->getLancamentosApiFrontend(
            $idUsuario,
            $search,
            $perPage,
            $offset,
            $sortDirection
        );

        $results = array_map(array($this, 'formatLancamento'), $rows);
        $totalPages = $total === 0 ? 0 : (int) ceil($total / $perPage);

        return $this->apiResponse->collection(
            $results,
            array(
                'pagination' => array(
                    'page'       => $page,
                    'perPage'    => $perPage,
                    'total'      => (int) $total,
                    'totalPages' => $totalPages,
                ),
                'filters' => array(
                    'search' => $search === '' ? null : $search,
                ),
                'sort' => array(
                    array(
                        'field'     => 'transactionDate',
                        'direction' => $sortDirection,
                    ),
                    array(
                        'field'     => 'id',
                        'direction' => $sortDirection,
                    ),
                ),
            )
        );
    }

    private function show($id = null)
    {
        $this->requireMethod('GET');
        $this->requirePermission('vLancamentos');

        $lancamento = $this->findLancamento($id);

        return $this->apiResponse->success($this->formatLancamento($lancamento));
    }

    private function create()
    {
        $this->requireMethod('POST');
        $this->requirePermission('aLancamentos');
        $this->requireCsrf();

        $input = $this->jsonInput();
        $data = $this->validateAndMapInput($input);
        $data['id_usuario'] = (int) $this->session->userdata('id');

        $id = $this->financeiro_model->addLancamentoApiFrontend($data);

        if (!$id) {
            return $this->apiResponse->error(
                'TRANSACTION_CREATE_FAILED',
                'Não foi possível criar o lançamento.',
                500
            );
        }

        return $this->apiResponse->created(
            $this->formatLancamento($this->financeiro_model->getById($id, $data['id_usuario']))
        );
    }

    private function update($id = null)
    {
        $this->requireMethod('PUT');
        $this->requirePermission('eLancamentos');
        $this->requireCsrf();

        $idUsuario = (int) $this->session->userdata('id');
        $lancamento = $this->findLancamento($id);

        $data = $this->validateAndMapInput($this->jsonInput());

        if (!$this->financeiro_model->updateLancamentoApiFrontend($lancamento->id_lancamento, $idUsuario, $data)) {
            return $this->apiResponse->error(
                'TRANSACTION_UPDATE_FAILED',
                'Não foi possível atualizar o lançamento.',
                500
            );
        }

        sincronizaPagamentoTerceiroPorLancamento(
            $lancamento->id_lancamento,
            $idUsuario,
            $data['baixado'] === 1
        );

        $updated = $this->financeiro_model->getById($lancamento->id_lancamento, $idUsuario);

        if ($updated && $updated->id_fatura) {
            monitoraPagamentosFaturasVinculadas($updated->id_fatura);
        }

        return $this->apiResponse->success($this->formatLancamento($updated));
    }

    private function delete($id = null)
    {
        $this->requireMethod('DELETE');
        $this->requirePermission('dLancamentos');
        $this->requireCsrf();

        $idUsuario = (int) $this->session->userdata('id');
        $lancamento = $this->findLancamento($id);

        if (!$this->financeiro_model->deleteLancamentoApiFrontend($lancamento->id_lancamento, $idUsuario)) {
            return $this->apiResponse->error(
                'TRANSACTION_DELETE_FAILED',
                'Não foi possível excluir o lançamento.',
                500
            );
        }

        return $this->apiResponse->noContent();
    }

    private function findLancamento($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false || (int) $id < 1) {
            $this->terminateWithError(
                'INVALID_REQUEST',
                'O identificador do lançamento é inválido.',
                400
            );
        }

        $lancamento = $this->financeiro_model->getById(
            (int) $id,
            (int) $this->session->userdata('id')
        );

        if (!$lancamento) {
            $this->terminateWithError(
                'TRANSACTION_NOT_FOUND',
                'Lançamento não encontrado.',
                404
            );
        }

        return $lancamento;
    }

    private function validateAndMapInput($input)
    {
        $validation = $this->apifrontendlancamentoinput->validate($input);

        if (!$validation['valid']) {
            $this->terminateWithError(
                'VALIDATION_ERROR',
                'Verifique os campos informados.',
                422,
                array('fields' => $validation['fields'])
            );
        }

        $validated = $validation['data'];
        $signedAmount = $validated['type'] === 2
            ? '-' . $validated['amount']
            : $validated['amount'];

        return array(
            'descricao'          => padronizarString($validated['description']),
            'observacoes'        => $validated['notes'] === null ? null : sanitizarString($validated['notes']),
            'valor'              => $signedAmount,
            'data_lancamento'    => $validated['transactionDate'],
            'data_pagamento'     => $validated['paymentDate'],
            'baixado'            => $validated['paid'] ? 1 : 0,
            'oculto'             => $validated['hidden'] ? 1 : 0,
            'cliente_fornecedor' => padronizarString($validated['provider']),
            'forma_pgto'         => $validated['paymentMethodId'],
            'tipo'               => $validated['type'],
        );
    }

    private function invalidMethod($expected, $received)
    {
        return $this->apiResponse->error(
            'INVALID_REQUEST',
            'A requisição enviada não corresponde ao contrato deste recurso.',
            400,
            array(
                'expectedMethods' => array_values($expected),
                'receivedMethod'  => $received,
            )
        );
    }

    private function formatLancamento($row)
    {
        return array(
            'id'              => (int) $row->id_lancamento,
            'description'     => (string) $row->descricao,
            'amount'          => ApiFrontendFormatter::decimal($row->valor),
            'transactionDate' => ApiFrontendFormatter::date($row->data_lancamento),
            'paymentDate'     => ApiFrontendFormatter::date($row->data_pagamento ?? null),
            'paid'            => ApiFrontendFormatter::boolean($row->baixado),
            'provider'        => $row->cliente_fornecedor === null ? null : (string) $row->cliente_fornecedor,
            'paymentMethodId' => $row->forma_pgto === null || $row->forma_pgto === '' ? null : (int) $row->forma_pgto,
            'type'            => (int) $row->tipo,
            'notes'           => $row->observacoes === null || $row->observacoes === '' ? null : (string) $row->observacoes,
            'hidden'          => ApiFrontendFormatter::boolean($row->oculto),
        );
    }
}
