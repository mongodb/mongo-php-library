<?php

namespace MongoDB\CodeGenerator\Config;

use MongoDB\CodeGenerator\OperatorClassGenerator;
use MongoDB\CodeGenerator\OperatorFactoryGenerator;

return [
    // Aggregation Pipeline Stages
    [
        'configFile' => __DIR__ . '/stages.yaml',
        'namespace' => 'MongoDB\\Builder\\Stage',
        'classNameSuffix' => 'Stage',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],

    // Aggregation Pipeline Operators
    [
        'configFile' => __DIR__ . '/pipeline-operators.yaml',
        'namespace' => 'MongoDB\\Builder\\Aggregation',
        'classNameSuffix' => 'Aggregation',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],

    // Query Operators
    [
        'configFile' => __DIR__ . '/query-operators.yaml',
        'namespace' => 'MongoDB\\Builder\\Query',
        'classNameSuffix' => 'Query',
        'generators' => [
            OperatorClassGenerator::class,
            OperatorFactoryGenerator::class,
        ],
    ],
];
