<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/application')
    ->in(__DIR__ . '/scripts')
    ->name('*.php')
    ->notPath('helpers/mpdf/vendor')
    ->notPath('helpers/dompdf/vendor')
    ->notPath('cache')
    ->notPath('logs');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        'binary_operator_spaces' => [
            'operators' => [
                '=' => 'align_single_space_minimal',
                '=>' => 'align_single_space_minimal',
            ],
        ],
    ])
    ->setFinder($finder);
