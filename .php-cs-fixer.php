<?php

$finder = (new PhpCsFixer\Finder())
    ->in(['./src', './tests']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        'declare_strict_types' => true,
        'php_unit_test_class_requires_covers' => false
    ])
    ->setFinder($finder);