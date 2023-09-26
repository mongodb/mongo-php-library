<?php

namespace MongoDB\CodeGenerator\Config;

use MongoDB\CodeGenerator\FactoryClassGenerator;
use MongoDB\CodeGenerator\ValueClassGenerator;

$src = __DIR__ . '/../../src';
$tests = __DIR__ . '/../../tests';

return [
    // Aggregation Pipeline Stages
    [
        'configFile' => __DIR__ . '/stages.yaml',
        'generatorClass' => ValueClassGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Stage',
        'filePath' => $src . '/Builder/Stage',
        'classNameSuffix' => 'Stage',
    ],
    [
        'configFile' => __DIR__ . '/stages.yaml',
        'generatorClass' => FactoryClassGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Stage',
        'filePath' => $src . '/Builder/Stage',
        'classNameSuffix' => 'Stage',
    ],

    // Aggregation Pipeline Operators
    [
        'configFile' => __DIR__ . '/pipeline-operators.yaml',
        'generatorClass' => ValueClassGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Aggregation',
        'filePath' => $src . '/Builder/Aggregation',
        'classNameSuffix' => 'Aggregation',
    ],
    [
        'configFile' => __DIR__ . '/pipeline-operators.yaml',
        'generatorClass' => FactoryClassGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Aggregation',
        'filePath' => $src . '/Builder/Aggregation',
        'classNameSuffix' => 'Aggregation',
    ],

    // Query Operators
    [
        'configFile' => __DIR__ . '/query-operators.yaml',
        'generatorClass' => ValueClassGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Query',
        'filePath' => $src . '/Builder/Query',
        'classNameSuffix' => 'Query',
    ],
    [
        'configFile' => __DIR__ . '/query-operators.yaml',
        'generatorClass' => FactoryClassGenerator::class,
        'namespace' => 'MongoDB\\Builder\\Query',
        'filePath' => $src . '/Builder/Query',
        'classNameSuffix' => 'Query',
    ],
];
