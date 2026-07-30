<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/application/libraries/ApiFrontendFormatter.php';

final class ApiFrontendFormatterTest extends TestCase
{
    public function testDecimalValuesRemainStrings(): void
    {
        self::assertSame('1250.90', ApiFrontendFormatter::decimal('1250.9'));
        self::assertSame('-125.90', ApiFrontendFormatter::decimal('-125.90'));
        self::assertNull(ApiFrontendFormatter::decimal(null));
    }

    public function testDatesUseInternationalFormat(): void
    {
        self::assertSame('2026-07-30', ApiFrontendFormatter::date('2026-07-30'));
        self::assertNull(ApiFrontendFormatter::date(null));
        self::assertNull(ApiFrontendFormatter::date('0000-00-00'));
    }

    public function testDateTimesUseIso8601WithTimezone(): void
    {
        $dateTime = ApiFrontendFormatter::dateTime('2026-07-30 14:35:00');

        self::assertMatchesRegularExpression(
            '/^2026-07-30T14:35:00[+-]\d{2}:\d{2}$/',
            $dateTime
        );
    }

    public function testBooleansAndNullUseNativeJsonTypes(): void
    {
        $encoded = json_encode(
            array(
                'paid'  => ApiFrontendFormatter::boolean(1),
                'hidden' => ApiFrontendFormatter::boolean(0),
                'notes' => null,
                'id'    => 123,
            ),
            JSON_THROW_ON_ERROR
        );
        $decoded = json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);

        self::assertTrue($decoded['paid']);
        self::assertFalse($decoded['hidden']);
        self::assertNull($decoded['notes']);
        self::assertSame(123, $decoded['id']);
    }
}
