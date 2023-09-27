<?php

namespace MongoDB\CodeGenerator\Config;

use MongoDB\CodeGenerator\OperatorClassGenerator;
use MongoDB\CodeGenerator\OperatorFactoryGenerator;

return [
    // Aggregation Pipeline Stages
    [
        'configFile' => __DIR__ . '/stages.yaml',
        'generatorClass' => OperatorClassGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Stage',
        'classNameSuffix' => 'Stage',
    ],
    [
        'configFile' => __DIR__ . '/stages.yaml',
        'generatorClass' => OperatorFactoryGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Stage',
        'classNameSuffix' => 'Stage',
    ],

    // Aggregation Pipeline Operators
    [
        'configFile' => __DIR__ . '/pipeline-operators.yaml',
        'generatorClass' => OperatorClassGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Aggregation',
        'classNameSuffix' => 'Aggregation',
    ],
    [
        'configFile' => __DIR__ . '/pipeline-operators.yaml',
        'generatorClass' => OperatorFactoryGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Aggregation',
        'classNameSuffix' => 'Aggregation',
    ],

    // Query Operators
    [
        'configFile' => __DIR__ . '/query-operators.yaml',
        'generatorClass' => OperatorClassGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Query',
        'classNameSuffix' => 'Query',
    ],
    [
        'configFile' => __DIR__ . '/query-operators.yaml',
        'generatorClass' => OperatorFactoryGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Query',
        'classNameSuffix' => 'Query',
    ],
];
