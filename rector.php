<?php

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassLike\RemoveAnnotationRector;
use Rector\Php70\Rector\StmtsAwareInterface\IfIssetToCoalescingRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
    ])
    ->withPhpSets(php74: true)
    ->withComposerBased(phpunit: true)
    ->withRules([
        ChangeSwitchToMatchRector::class,
    ])
    // All classes are public API by default, unless marked with @internal.
    ->withConfiguredRule(RemoveAnnotationRector::class, ['api'])
    // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified
    ->withSkip([
        RemoveExtraParametersRector::class,
        // Do not use ternaries extensively
        IfIssetToCoalescingRector::class,
        ChangeSwitchToMatchRector::class => [
            __DIR__ . '/tests/SpecTests/Operation.php',
        ],
    ])
    // phpcs:enable
    ->withImportNames(importNames: false, removeUnusedImports: true);
