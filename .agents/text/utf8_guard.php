#!/usr/bin/env php
<?php

declare(strict_types=1);

function fail(string $message): never
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}

function normalizeExternalText(string $value, string $field): string
{
    if (preg_match('//u', $value) !== 1) {
        $converted = iconv('Windows-1252', 'UTF-8', $value);
        if ($converted === false || preg_match('//u', $converted) !== 1) {
            fail("{$field}: nao foi possivel converter o texto para UTF-8 sem perda.");
        }
        $value = $converted;
    }

    if (class_exists('Normalizer')) {
        $normalized = Normalizer::normalize($value, Normalizer::FORM_C);
        if ($normalized === false) {
            fail("{$field}: nao foi possivel normalizar o texto em Unicode NFC.");
        }
        $value = $normalized;
    }

    if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value) === 1) {
        fail("{$field}: o texto contem caracteres de controle proibidos.");
    }

    if (str_contains($value, "\u{FFFD}")) {
        fail("{$field}: o texto contem o caractere de substituicao Unicode U+FFFD.");
    }

    if (preg_match('/(?:Ã[\x{0080}-\x{00BF}]|Â[\x{0080}-\x{00BF}]|â(?:€|€™|€œ|€|€“|€”|€¦)|ðŸ)/u', $value) === 1) {
        fail("{$field}: o texto contem assinatura de mojibake.");
    }

    if (preg_match('/\p{L}\?{2,}\p{L}/u', $value) === 1) {
        fail("{$field}: o texto aparenta perda de caracteres representada por ?? entre letras.");
    }

    if (str_contains($value, '\\n')) {
        fail("{$field}: o texto contem \\n literal; use quebras de linha reais.");
    }

    return $value;
}

function readFileStrict(string $path): string
{
    $value = @file_get_contents($path);
    if ($value === false) {
        fail("Nao foi possivel ler o arquivo: {$path}");
    }

    return $value;
}

$command = $argv[1] ?? '';

switch ($command) {
    case 'normalize-file':
        $source = $argv[2] ?? '';
        $target = $argv[3] ?? '';
        $field = $argv[4] ?? $source;
        if ($source === '' || $target === '') {
            fail('Uso: utf8_guard.php normalize-file SOURCE TARGET [FIELD]');
        }
        $value = normalizeExternalText(readFileStrict($source), $field);
        if (file_put_contents($target, $value) === false) {
            fail("Nao foi possivel gravar o arquivo normalizado: {$target}");
        }
        break;

    case 'validate-file':
        $source = $argv[2] ?? '';
        $field = $argv[3] ?? $source;
        if ($source === '') {
            fail('Uso: utf8_guard.php validate-file SOURCE [FIELD]');
        }
        normalizeExternalText(readFileStrict($source), $field);
        break;

    case 'normalize-stdin':
        $field = $argv[2] ?? 'stdin';
        echo normalizeExternalText(stream_get_contents(STDIN), $field);
        break;

    default:
        fail('Comando invalido. Use normalize-file, validate-file ou normalize-stdin.');
}
