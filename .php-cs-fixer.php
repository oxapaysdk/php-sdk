<?php

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        // keep imports from short to long (by length)
        'ordered_imports' => ['sort_algorithm' => 'length', 'imports_order' => ['class', 'function', 'const']],
        'no_unused_imports' => true,
        'single_import_per_statement' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
        'no_trailing_whitespace' => true,
        'no_extra_blank_lines' => true,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'phpdoc_no_empty_return' => false,
        'blank_line_before_statement' => ['statements' => ['return']],
        'return_type_declaration' => ['space_before' => 'none'],
        'declare_strict_types' => false
    ]);
