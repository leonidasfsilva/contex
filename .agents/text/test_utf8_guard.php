<?php

declare(strict_types=1);

$guard = __DIR__ . '/utf8_guard.php';
$fixtures = [
    ['ok', "Autenticação, sessão e validação\n### Resumo\n`api_session`"],
    ['ok-windows-1252', iconv('UTF-8', 'Windows-1252', 'Descrição e validação')],
    ['reject-loss', 'autentica??o'],
    ['reject-heading-loss', 'Valida??o'],
    ['reject-mojibake', 'autenticaÃ§Ã£o'],
    ['reject-replacement', "inválido \u{FFFD}"],
    ['reject-control', "texto\x01invalido"],
];

$failures = [];
foreach ($fixtures as [$name, $value]) {
    $source = tempnam(sys_get_temp_dir(), 'utf8-source-');
    $target = tempnam(sys_get_temp_dir(), 'utf8-target-');
    file_put_contents($source, $value);
    $command = sprintf(
        '%s %s normalize-file %s %s teste 2>&1',
        escapeshellarg(PHP_BINARY),
        escapeshellarg($guard),
        escapeshellarg($source),
        escapeshellarg($target)
    );
    exec($command, $output, $status);
    $expectedSuccess = str_starts_with($name, 'ok');
    if (($status === 0) !== $expectedSuccess) {
        $failures[] = $name . ': ' . implode(' ', $output);
    }
    @unlink($source);
    @unlink($target);
    $output = [];
}

if ($failures !== []) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo "Guard UTF-8: todos os cenarios passaram." . PHP_EOL;
