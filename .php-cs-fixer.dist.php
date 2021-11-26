<?php

declare(strict_types=1);

$composer = json_decode(file_get_contents(__DIR__.'/composer.json'), true);

$projectName = $composer['name'];

$license = file_get_contents(__DIR__.'/LICENSE');

$headerComment = <<<COMMENT
This file is part of the {$projectName} package.

{$license}
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->notPath('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'binary_operator_spaces' => [
            'operators' => ['=>' => null],
        ],
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'not_operator_with_successor_space' => true,
        'header_comment' => [
            'header' => $headerComment,
            'separate' => 'both',
            'location' => 'after_open',
            'comment_type' => 'PHPDoc',
        ],
        'linebreak_after_opening_tag' => true,
        'mb_str_functions' => true,
        'no_php4_constructor' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'length',
        ],
        'php_unit_strict' => true,
        'phpdoc_no_empty_return' => false,
        'phpdoc_order' => true,
        'semicolon_after_instruction' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'yoda_style' => false,
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'native_function_invocation'=> false,
        'native_constant_invocation' => false,
        'declare_strict_types' => true,
        'phpdoc_to_comment' => false,
    ])
    ->setFinder($finder);
