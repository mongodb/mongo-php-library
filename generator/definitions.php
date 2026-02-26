<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Config;

use MongoDB\CodeGenerator\FluentStageFactoryGenerator;
use MongoDB\CodeGenerator\OperatorClassGenerator;
use MongoDB\CodeGenerator\OperatorFactoryGenerator;
use MongoDB\CodeGenerator\OperatorTestGenerator;

return [
    // Aggregation Pipeline Stages
    [
        'configFiles' => __DIR__ . '/mql-specifications/definitions/stage',
        'namespace' => 'MongoDB\\Builder\\Stage',
        'classNameSuffix' => 'Stage',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
            OperatorTestGenerator::class,
            FluentStageFactoryGenerator::class,
        ],
    ],

    // Aggregation Pipeline Accumulator and Window Operators
    [
        'configFiles' => __DIR__ . '/mql-specifications/definitions/accumulator',
        'namespace' => 'MongoDB\\Builder\\Accumulator',
        'classNameSuffix' => 'Accumulator',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
            OperatorTestGenerator::class,
        ],
    ],

    // Aggregation Pipeline Expression
    [
        'configFiles' => __DIR__ . '/mql-specifications/definitions/expression',
        'namespace' => 'MongoDB\\Builder\\Expression',
        'classNameSuffix' => 'Operator',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
            OperatorTestGenerator::class,
        ],
    ],

    // Query Operators
    [
        'configFiles' => __DIR__ . '/mql-specifications/definitions/query',
        'namespace' => 'MongoDB\\Builder\\Query',
        'classNameSuffix' => 'Operator',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
            OperatorTestGenerator::class,
        ],
    ],

    // Search Operators
    [
        'configFiles' => __DIR__ . '/mql-specifications/definitions/search',
        'namespace' => 'MongoDB\\Builder\\Search',
        'classNameSuffix' => 'Operator',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
            OperatorTestGenerator::class,
        ],
    ],
];
