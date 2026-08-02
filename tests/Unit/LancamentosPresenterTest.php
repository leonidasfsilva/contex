<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/application/libraries/ApiFrontendFormatter.php';
require_once dirname(__DIR__, 2) . '/application/libraries/LancamentosPresenter.php';

final class LancamentosPresenterTest extends TestCase
{
    public function testCollectionIncludesPeriodPaginationAndWholePeriodSummary(): void
    {
        $presenter = new LancamentosPresenter();
        $expense = $this->row(1, '-100.00', 2, 0, 0);
        $income = $this->row(2, '250.00', 1, 1, 0);
        $hidden = $this->row(3, '-20.00', 2, 1, 1);

        $payload = $presenter->collection(array(
            'results' => array($expense),
            'aggregateResults' => array($expense, $income, $hidden),
            'referenceMonth' => 7,
            'referenceYear' => 2026,
            'month' => 'JULHO',
            'prevReferenceMonthNumber' => 6,
            'nextReferenceMonthNumber' => 8,
            'prevReferenceYear' => 2026,
            'nextReferenceYear' => 2026,
            'currentPage' => 1,
            'perPage' => 30,
            'totalRows' => 61,
            'totalPages' => 3,
        ));

        self::assertSame('JULHO / 2026', $payload['data']['period']['label']);
        self::assertSame('-100.00', $payload['data']['summary']['pendingExpense']);
        self::assertSame('250.00', $payload['data']['summary']['paidIncome']);
        self::assertSame('-20.00', $payload['data']['summary']['hiddenTotal']);
        self::assertSame('130.00', $payload['data']['summary']['total']);
        self::assertSame(3, $payload['meta']['pagination']['totalPages']);
    }

    public function testItemIncludesNotesInvoiceAndExpenseIndicators(): void
    {
        $presenter = new LancamentosPresenter();
        $row = $this->row(4, '-90.00', 2, 0, 0);
        $row->observacoes = "Linha 1\nLinha 2";
        $row->id_fatura = 12;
        $row->id_despesa = 34;

        $item = $presenter->item($row);

        self::assertSame("Linha 1\nLinha 2", $item['notes']);
        self::assertSame(12, $item['invoiceId']);
        self::assertSame(34, $item['expenseId']);
    }

    private function row(int $id, string $amount, int $type, int $paid, int $hidden): object
    {
        return (object) array(
            'id_lancamento' => $id,
            'descricao' => 'Teste',
            'valor' => $amount,
            'data_lancamento' => '2026-07-10',
            'data_pagamento' => '2026-07-10',
            'baixado' => $paid,
            'cliente_fornecedor' => null,
            'forma_pgto' => 2,
            'tipo' => $type,
            'observacoes' => null,
            'id_fatura' => null,
            'id_despesa' => null,
            'oculto' => $hidden,
        );
    }
}
