<?php

$finder = PhpCsFixer\Finder::create()
    ->notPath('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'binary_operator_spaces' => [
            'operators' => ['=>' => null],
        ],
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'linebreak_after_opening_tag' => true,
        'not_operator_with_successor_space' => true,
        'ordered_imports' => [
            'sortAlgorithm' => 'length',
        ],
        'phpdoc_no_empty_return' => false,
        'phpdoc_order' => true,
        'yoda_style' => false,
        'list_syntax' => [
            'syntax' => 'short',
        ],
    ])
    ->setFinder($finder);
