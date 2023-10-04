<?php

namespace MongoDB\CodeGenerator\Config;

use MongoDB\CodeGenerator\OperatorClassGenerator;
use MongoDB\CodeGenerator\OperatorFactoryGenerator;

return [
    // Aggregation Pipeline Stages
    [
        'configFiles' => __DIR__ . '/aggregation-stages',
        'namespace' => 'MongoDB\\Builder\\Stage',
        'classNameSuffix' => 'Stage',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],

    // Aggregation Pipeline Operators
    [
        'configFiles' => __DIR__ . '/aggregation-operators',
        'namespace' => 'MongoDB\\Builder\\Aggregation',
        'classNameSuffix' => 'Aggregation',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],

    // Query Operators
    [
        'configFiles' => __DIR__ . '/query-operators',
        'namespace' => 'MongoDB\\Builder\\Query',
        'classNameSuffix' => 'Query',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],
];
