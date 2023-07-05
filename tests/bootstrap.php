<?php

use MongoDB\Tests\Comparator\Int64Comparator;
use MongoDB\Tests\TestCase;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// Register custom comparators
ComparatorFactory::getInstance()->register(new Int64Comparator());

/* Ugly workaround for event system changes in PHPUnit
 * PHPUnit 10 introduces a new event system, and at the same time removes the
 * event system present in previous versions. This makes it near impossible to
 * support PHPUnit 8.5 (required for PHP 7.2) and PHPUnit 9 (PHP < 8.1)
 * alongside PHPUnit 10. For this reason, we use this bootstrap file to print
 * test configuration summary.
 * @todo PHP_VERSION_ID >= 80100: Remove this and use an extension
 */
TestCase::printConfiguration();
