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

if (! class_exists(PHPUnit\Framework\Error\Warning::class)) {
    class_alias(PHPUnit_Framework_Error_Warning::class, PHPUnit\Framework\Error\Warning::class);
}

if (! class_exists(PHPUnit\Framework\Constraint\Constraint::class)) {
    class_alias(PHPUnit_Framework_Constraint::class, PHPUnit\Framework\Constraint\Constraint::class);
}

if (! class_exists(PHPUnit\Framework\ExpectationFailedException::class)) {
    class_alias(PHPUnit_Framework_ExpectationFailedException::class, PHPUnit\Framework\ExpectationFailedException::class);
}
