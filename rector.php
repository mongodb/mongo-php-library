<?php

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\ClassConstFetch\RenameClassConstFetchRector;
use Rector\Renaming\ValueObject\RenameClassConstFetch;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
        __FILE__,
    ]);

    // ReadPreference::RP_* constants are deprecated in favor of the string constants
    $rectorConfig->ruleWithConfiguration(RenameClassConstFetchRector::class, [
        new RenameClassConstFetch('MongoDB\Driver\ReadPreference', 'RP_PRIMARY', 'PRIMARY'),
        new RenameClassConstFetch('MongoDB\Driver\ReadPreference', 'RP_PRIMARY_PREFERRED', 'PRIMARY_PREFERRED'),
        new RenameClassConstFetch('MongoDB\Driver\ReadPreference', 'RP_SECONDARY', 'SECONDARY'),
        new RenameClassConstFetch('MongoDB\Driver\ReadPreference', 'RP_SECONDARY_PREFERRED', 'SECONDARY_PREFERRED'),
        new RenameClassConstFetch('MongoDB\Driver\ReadPreference', 'RP_NEAREST', 'NEAREST'),
    ]);

    // define sets of rules
    //    $rectorConfig->sets([
    //        LevelSetList::UP_TO_PHP_72
    //    ]);
};
