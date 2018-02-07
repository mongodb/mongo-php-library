<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Dependencies were installed with Composer and this is the main project
    $loader = require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../../autoload.php')) {
    // We're installed as a dependency in another project's `vendor` directory
    $loader = require_once __DIR__ . '/../../../../autoload.php';
} else {
    throw new Exception('Can\'t find autoload.php. Did you install dependencies with Composer?');
}

if ( ! class_exists('PHPUnit\Framework\Error\Warning')) {
    class_alias('PHPUnit_Framework_Error_Warning', 'PHPUnit\Framework\Error\Warning');
}
