<?php

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassLike\RemoveAnnotationRector;
use Rector\Php70\Rector\StmtsAwareInterface\IfIssetToCoalescingRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
    ]);

    // Modernize code
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_74,
        PHPUnitSetList::PHPUNIT_100,
    ]);

    $rectorConfig->rule(ChangeSwitchToMatchRector::class);
    $rectorConfig->rule(StaticDataProviderClassMethodRector::class);

    // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified
    $rectorConfig->skip([
        // Do not use ternaries extensively
        IfIssetToCoalescingRector::class,
        ChangeSwitchToMatchRector::class => [
            __DIR__ . '/tests/SpecTests/Operation.php',
        ],
    ]);
    // phpcs:enable

    // All classes are public API by default, unless marked with @internal.
    $rectorConfig->ruleWithConfiguration(RemoveAnnotationRector::class, ['api']);
};
