<?php

$finder = (new PhpCsFixer\Finder())
    ->in(['./src', './tests']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        'declare_strict_types' => true
    ])
    ->setFinder($finder);