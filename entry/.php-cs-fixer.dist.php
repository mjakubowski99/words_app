<?php

use PhpCsFixer\Finder;

$rules = [
    '@PSR1' => true,
    '@PSR2' => true,
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    '@DoctrineAnnotation' => true,

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:ordered_imports
    'ordered_imports' => [
        'sort_algorithm' => 'length',
    ],

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:ordered_class_elements
    'ordered_class_elements' => false,

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:single_line_comment_style
    'single_line_comment_style' => [
        'comment_types' => [
            'hash',
        ],
    ],

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:php_unit_method_casing
    'php_unit_method_casing' => false,

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:single_quote
    'single_quote' => [
        'strings_containing_single_quote_chars' => false,
    ],
    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:return_assignment
    'return_assignment' => false,

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:phpdoc_add_missing_param_annotation
    'phpdoc_add_missing_param_annotation' => false,

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:php_unit_internal_class
    'php_unit_internal_class' => false,

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:php_unit_test_class_requires_covers
    'php_unit_test_class_requires_covers' => false,

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:phpdoc_separation
    'phpdoc_separation' => false,

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:yoda_style
    'yoda_style' => [
        'always_move_variable' => false,
        'equal' => false,
        'identical' => false,
        'less_and_greater' => false,
    ],

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:concat_space
    'concat_space' => [
        'spacing' => 'one',
    ],

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:multiline_whitespace_before_semicolons
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'no_multi_line',
    ],

    // https://mlocati.github.io/php-cs-fixer-configurator/#version:3.13|fixer:mb_str_functions
    'mb_str_functions' => true,

    'declare_strict_types' => true,
];

$project_path = getcwd();
$domains_path = $project_path . '/../';

$finder = Finder::create()
    ->in([
        $project_path . '/app',
        $project_path . '/config',
        $project_path . '/database',
        $project_path . '/resources',
        $project_path . '/routes',
        $project_path . '/tests',

        $domains_path . '/Auth',
        $domains_path . '/User',
        $domains_path . '/UseCases',
        $domains_path . '/Shared',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();
$config
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile('.php-cs-fixer.cache')
    ->setFinder($finder);

return $config;
