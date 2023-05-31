<?php

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassLike\RemoveAnnotationRector;
use Rector\Php56\Rector\FunctionLike\AddDefaultValueForUndefinedVariableRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
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

    $rectorConfig->skip([
        // Falsely detect unassigned variables in code paths stopped by PHPUnit\Framework\Assert::markTestSkipped()
        AddDefaultValueForUndefinedVariableRector::class => [
            __DIR__ . '/tests/',
        ],
        // @see https://github.com/phpstan/phpstan-src/pull/2429
        RemoveExtraParametersRector::class => [
            __DIR__ . '/src/Operation/',
        ],
    ]);

    // All classes are public API by default, unless marked with @internal.
    $rectorConfig->ruleWithConfiguration(RemoveAnnotationRector::class, ['api']);
};
