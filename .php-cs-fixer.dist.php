<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->exclude(['Resources', 'var']);

return (new PhpCsFixer\Config())
    ->setCacheFile('.build/cache/.php-cs-fixer.cache')
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP81Migration' => true,
        '@PSR12' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
                '=' => 'align_single_space_minimal',
            ],
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'do',
                'exit',
                'if',
                'return',
                'switch',
                'try',
                'yield',
            ],
        ],
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'ordered_class_elements' => true,
        'phpdoc_order' => true,
        'phpdoc_types_order' => true,
        'single_quote' => true,
        'visibility_required' => [
            'elements' => [
                'const',
                'property',
                'method',
            ],
        ],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'phpdoc_to_return_type' => true,
        'void_return' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ]);
