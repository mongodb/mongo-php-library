<?php

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassLike\RemoveAnnotationRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
    ]);

    // Modernize code
    $rectorConfig->sets([LevelSetList::UP_TO_PHP_72]);

    // All classes are public API by default, unless marked with @internal.
    $rectorConfig->ruleWithConfiguration(RemoveAnnotationRector::class, ['api']);

};
