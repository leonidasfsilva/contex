<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/application/libraries/ApiFrontendLancamentoInput.php';

final class ApiFrontendLancamentoInputTest extends TestCase
{
    private ApiFrontendLancamentoInput $validator;

    protected function setUp(): void
    {
        $this->validator = new ApiFrontendLancamentoInput();
    }

    public function testValidPayloadIsNormalized(): void
    {
        $result = $this->validator->validate(array(
            'description'     => 'Compra no mercado',
            'amount'          => '125.9',
            'transactionDate' => '2026-07-30',
            'paymentDate'     => '2026-07-31',
            'paid'            => true,
            'provider'        => 'Mercado Exemplo',
            'paymentMethodId' => 2,
            'type'            => 2,
            'notes'           => '',
            'hidden'          => false,
        ));

        self::assertTrue($result['valid']);
        self::assertSame('125.90', $result['data']['amount']);
        self::assertSame('Mercado Exemplo', $result['data']['provider']);
        self::assertNull($result['data']['notes']);
        self::assertTrue($result['data']['paid']);
        self::assertFalse($result['data']['hidden']);
    }

    public function testPaymentDateDefaultsToTransactionDate(): void
    {
        $result = $this->validator->validate($this->validPayload(array('paymentDate' => null)));

        self::assertTrue($result['valid']);
        self::assertSame('2026-07-30', $result['data']['paymentDate']);
    }

    public function testInvalidPayloadReturnsFieldErrors(): void
    {
        $result = $this->validator->validate(array(
            'description'     => '',
            'amount'          => '-10.00',
            'transactionDate' => '30/07/2026',
            'paymentMethodId' => 0,
            'type'            => 3,
            'paid'            => 'true',
            'hidden'          => 0,
        ));

        self::assertFalse($result['valid']);
        self::assertArrayHasKey('description', $result['fields']);
        self::assertArrayHasKey('amount', $result['fields']);
        self::assertArrayHasKey('transactionDate', $result['fields']);
        self::assertArrayHasKey('paymentMethodId', $result['fields']);
        self::assertArrayHasKey('type', $result['fields']);
        self::assertArrayHasKey('paid', $result['fields']);
        self::assertArrayHasKey('hidden', $result['fields']);
    }

    public function testAmountAcceptsAtMostTwoDecimalPlaces(): void
    {
        $result = $this->validator->validate($this->validPayload(array('amount' => '10.999')));

        self::assertFalse($result['valid']);
        self::assertArrayHasKey('amount', $result['fields']);
    }

    private function validPayload(array $overrides = array()): array
    {
        return array_merge(array(
            'description'     => 'Compra no mercado',
            'amount'          => '125.90',
            'transactionDate' => '2026-07-30',
            'paymentDate'     => '2026-07-30',
            'paid'            => false,
            'provider'        => 'Mercado Exemplo',
            'paymentMethodId' => 2,
            'type'            => 2,
            'notes'           => null,
            'hidden'          => false,
        ), $overrides);
    }
}
