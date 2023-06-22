<?php

use MongoDB\Tests\Comparator\Int64Comparator;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// Register custom comparators
ComparatorFactory::getInstance()->register(new Int64Comparator());
