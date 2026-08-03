<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LancamentosPresenter
{
    public function collection(array $context)
    {
        $rows = (array) ($context['results'] ?? array());
        $allRows = (array) ($context['aggregateResults'] ?? $rows);

        return array(
            'data' => array(
                'results' => array_values(array_map(array($this, 'item'), $rows)),
                'summary' => $this->summary($allRows),
                'paymentMethods' => $this->paymentMethods($context['formasPagamento'] ?? array()),
                'period' => array(
                    'month' => isset($context['referenceMonth']) ? (int) $context['referenceMonth'] : null,
                    'year' => isset($context['referenceYear']) ? (int) $context['referenceYear'] : null,
                    'years' => array_values(array_map('intval', $context['yearsList'] ?? array())),
                    'label' => trim((string) ($context['month'] ?? '') . ' / ' . (string) ($context['referenceYear'] ?? ''), ' /'),
                    'previous' => array(
                        'month' => $context['prevReferenceMonthNumber'] ?? null,
                        'year' => $context['prevReferenceYear'] ?? null,
                    ),
                    'next' => array(
                        'month' => $context['nextReferenceMonthNumber'] ?? null,
                        'year' => $context['nextReferenceYear'] ?? null,
                    ),
                ),
            ),
            'meta' => array(
                'pagination' => array(
                    'page' => (int) ($context['currentPage'] ?? 1),
                    'perPage' => (int) ($context['perPage'] ?? 30),
                    'total' => (int) ($context['totalRows'] ?? count($rows)),
                    'totalPages' => (int) ($context['totalPages'] ?? (count($rows) ? 1 : 0)),
                ),
                'filters' => $context['filters'] ?? array(),
                'sort' => array(
                    array('field' => 'transactionDate', 'direction' => 'desc'),
                    array('field' => 'id', 'direction' => 'desc'),
                ),
            ),
        );
    }

    public function item($row)
    {
        return array(
            'id' => (int) $row->id_lancamento,
            'description' => (string) $row->descricao,
            'amount' => ApiFrontendFormatter::decimal($row->valor),
            'transactionDate' => ApiFrontendFormatter::date($row->data_lancamento),
            'paymentDate' => ApiFrontendFormatter::date($row->data_pagamento ?? null),
            'paid' => ApiFrontendFormatter::boolean($row->baixado),
            'provider' => $row->cliente_fornecedor === null ? null : (string) $row->cliente_fornecedor,
            'paymentMethodId' => $row->forma_pgto === null || $row->forma_pgto === '' ? null : (int) $row->forma_pgto,
            'type' => (int) $row->tipo,
            'notes' => empty($row->observacoes) ? null : (string) $row->observacoes,
            'invoiceId' => empty($row->id_fatura) ? null : (int) $row->id_fatura,
            'expenseId' => empty($row->id_despesa) ? null : (int) $row->id_despesa,
            'hidden' => ApiFrontendFormatter::boolean($row->oculto),
        );
    }

    private function summary(array $rows)
    {
        $summary = array(
            'pendingIncome' => 0.0,
            'paidIncome' => 0.0,
            'pendingExpense' => 0.0,
            'paidExpense' => 0.0,
            'visibleTotal' => 0.0,
            'hiddenTotal' => 0.0,
            'total' => 0.0,
        );

        foreach ($rows as $row) {
            $amount = (float) $row->valor;
            $summary['total'] += $amount;

            if (!empty($row->oculto)) {
                $summary['hiddenTotal'] += $amount;
                continue;
            }

            $summary['visibleTotal'] += $amount;
            $key = $amount >= 0
                ? (!empty($row->baixado) ? 'paidIncome' : 'pendingIncome')
                : (!empty($row->baixado) ? 'paidExpense' : 'pendingExpense');
            $summary[$key] += $amount;
        }

        return array_map(array($this, 'decimal'), $summary);
    }

    private function paymentMethods($rows)
    {
        return array_values(array_map(function ($row) {
            return array('id' => (int) $row->id_forma, 'name' => (string) $row->nome);
        }, (array) $rows));
    }

    private function decimal($value)
    {
        return number_format((float) $value, 2, '.', '');
    }
}
