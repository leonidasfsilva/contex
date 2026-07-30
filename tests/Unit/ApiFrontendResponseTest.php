<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/application/libraries/ApiFrontendResponse.php';

final class ApiFrontendResponseTest extends TestCase
{
    public function testSuccessPayloadIncludesDataAndMeta(): void
    {
        $payload = ApiFrontendResponse::successPayload(
            array('results' => array()),
            array('pagination' => array('page' => 1, 'perPage' => 30, 'total' => 0, 'totalPages' => 0))
        );

        self::assertTrue($payload['success']);
        self::assertSame(array(), $payload['data']['results']);
        self::assertSame(0, $payload['meta']['pagination']['total']);
    }

    public function testSuccessPayloadOmitsMetaWhenItDoesNotApply(): void
    {
        $payload = ApiFrontendResponse::successPayload(array('id' => 123));

        self::assertArrayNotHasKey('meta', $payload);
    }

    public function testErrorPayloadUsesStableCodeAndOptionalDetails(): void
    {
        $payload = ApiFrontendResponse::errorPayload(
            'INVALID_REQUEST',
            'Requisição inválida.',
            array('fields' => array('page' => 'Valor inválido.'))
        );

        self::assertFalse($payload['success']);
        self::assertSame('INVALID_REQUEST', $payload['error']['code']);
        self::assertSame('Valor inválido.', $payload['error']['details']['fields']['page']);
    }

    public function testCrudStatusPayloadsFollowTheSameEnvelope(): void
    {
        $created = ApiFrontendResponse::successPayload(array('id' => 123));
        $notFound = ApiFrontendResponse::errorPayload(
            'TRANSACTION_NOT_FOUND',
            'Lançamento não encontrado.'
        );
        $validation = ApiFrontendResponse::errorPayload(
            'VALIDATION_ERROR',
            'Verifique os campos informados.',
            array('fields' => array('provider' => 'Valor inválido.'))
        );

        self::assertSame(123, $created['data']['id']);
        self::assertSame('TRANSACTION_NOT_FOUND', $notFound['error']['code']);
        self::assertArrayHasKey('provider', $validation['error']['details']['fields']);
    }
}
