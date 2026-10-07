<?php

use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Expr\Cast\Int_;
use Rector\Config\RectorConfig;
use Rector\Php70\Rector\StmtsAwareInterface\IfIssetToCoalescingRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\PHPUnit\PHPUnit60\Rector\ClassMethod\AddDoesNotPerformAssertionToNonAssertingTestRector;
use Rector\Renaming\Rector\Cast\RenameCastRector;
use Rector\Renaming\ValueObject\RenameCast;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
    ])
    // Error with StaticCallOnNonStaticToInstanceCallRector
    // https://github.com/rectorphp/rector/issues/9608
    ->withSkipPath(__DIR__ . '/tests/Builder/BuilderEncoderTest.php')
    ->withPhpSets(php80: true)
    ->withComposerBased(phpunit: true)
    // Fix PHP 8.5 deprecations
    ->withConfiguredRule(
        RenameCastRector::class,
        [
            new RenameCast(Int_::class, Int_::KIND_INTEGER, Int_::KIND_INT),
            new RenameCast(Bool_::class, Bool_::KIND_BOOLEAN, Bool_::KIND_BOOL),
            new RenameCast(Double::class, Double::KIND_DOUBLE, Double::KIND_FLOAT),
        ],
    )
    // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified
    ->withSkip([
        RemoveExtraParametersRector::class,
        // Do not use ternaries extensively
        IfIssetToCoalescingRector::class,
        ChangeSwitchToMatchRector::class => [
            __DIR__ . '/tests/SpecTests/Operation.php',
        ],
        AddDoesNotPerformAssertionToNonAssertingTestRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
        StringableForToStringRector::class => [
            __DIR__ . '/src/Model/IndexInput.php',
        ],
    ])
    // phpcs:enable
    ->withImportNames(importNames: false, removeUnusedImports: true);
