<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('analyzes')
    ->exclude('bin')
    ->exclude('config')
    ->exclude('migrations')
    ->exclude('public')
    ->exclude('templates')
    ->exclude('var')
    ->exclude('vendor')
    ->notPath('rector.php')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        '@PHP70Migration:risky' => true,
        '@PHP71Migration:risky' => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer:risky' => true
    ])
    ->setFinder($finder)
;
