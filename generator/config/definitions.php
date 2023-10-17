<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Config;

use MongoDB\CodeGenerator\OperatorClassGenerator;
use MongoDB\CodeGenerator\OperatorFactoryGenerator;

return [
    // Aggregation Pipeline Stages
    [
        'configFiles' => __DIR__ . '/stage',
        'namespace' => 'MongoDB\\Builder\\Stage',
        'classNameSuffix' => 'Stage',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],

    // Aggregation Pipeline Accumulator and Window Operators
    [
        'configFiles' => __DIR__ . '/accumulator',
        'namespace' => 'MongoDB\\Builder\\Accumulator',
        'classNameSuffix' => 'Accumulator',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],

    // Aggregation Pipeline Expression
    [
        'configFiles' => __DIR__ . '/expression',
        'namespace' => 'MongoDB\\Builder\\Expression',
        'classNameSuffix' => 'Operator',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],

    // Query Operators
    [
        'configFiles' => __DIR__ . '/query',
        'namespace' => 'MongoDB\\Builder\\Query',
        'classNameSuffix' => 'Operator',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],

    // Projection Operators
    [
        'configFiles' => __DIR__ . '/projection',
        'namespace' => 'MongoDB\\Builder\\Projection',
        'classNameSuffix' => 'Operator',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],
];
