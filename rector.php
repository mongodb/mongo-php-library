<?php

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassLike\RemoveAnnotationRector;
use Rector\Php70\Rector\StmtsAwareInterface\IfIssetToCoalescingRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
    ]);

    // Modernize code
    $rectorConfig->sets([LevelSetList::UP_TO_PHP_74]);

    // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified
    $rectorConfig->skip([
        // Do not use ternaries extensively
        IfIssetToCoalescingRector::class,
        // Not necessary in documentation examples
        JsonThrowOnErrorRector::class => [
            __DIR__ . '/tests/DocumentationExamplesTest.php',
        ],
    ]);
    // phpcs:enable

    // All classes are public API by default, unless marked with @internal.
    $rectorConfig->ruleWithConfiguration(RemoveAnnotationRector::class, ['api']);
};
