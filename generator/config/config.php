<?php

use MongoDB\Aggregation\Operator;
use MongoDB\Query\Operator as QueryOperator;
use MongoDB\Aggregation\Stage;
use MongoDB\CodeGenerator\FactoryClassGenerator;
use MongoDB\CodeGenerator\ValueClassGenerator;

$src = __DIR__ . '/../../src';

return [
    // Stages
    [
        'configFile' => __DIR__ . '/stages.yaml',
        'generatorClass' => ValueClassGenerator::class,
        'namespace' => MongoDB\Aggregation\Stage::class,
        'filePath' => $src . '/Aggregation/Stage/',
        'interfaces' => [Stage::class],
        'classNameSuffix' => 'Stage',
    ],
    /*
    // Stage codec
    [
        // Stage converters
        'configFile' => __DIR__ . '/stages.yaml',
        'generatorClass' => ConverterClassGenerator::class,
        'namespace' => Converter\Stage::class,
        'filePath' => $src . '/Aggregation/Converter/Stage/',
        'parentClass' => AbstractConverter::class,
        'classNameSuffix' => 'StageConverter',
        'supportingNamespace' => Stage::class,
        'supportingClassNameSuffix' => 'Stage',
        'libraryNamespace' => Converter::class,
        'libraryClassName' => 'StageConverter',
    ],
    */
    // Stage factory
    [
        'configFile' => __DIR__ . '/stages.yaml',
        'generatorClass' => FactoryClassGenerator::class,
        'namespace' => Stage::class,
        'filePath' => $src . '/Aggregation/',
        'classNameSuffix' => 'Stage',
    ],

    // Pipeline operators
    [
        'configFile' => __DIR__ . '/pipeline-operators.yaml',
        'generatorClass' => ValueClassGenerator::class,
        'namespace' => Operator::class,
        'filePath' => $src . '/Aggregation/Operator/',
        'classNameSuffix' => 'Operator',
    ],
    /*
    [
        'configFile' => __DIR__ . '/pipeline-operators.yaml',
        'generatorClass' => ConverterClassGenerator::class,
        'namespace' => Converter\PipelineOperator::class,
        'filePath' => $src . '/Aggregation/Converter/PipelineOperator/',
        'parentClass' => AbstractConverter::class,
        'classNameSuffix' => 'PipelineOperatorConverter',
        'supportingNamespace' => PipelineOperator::class,
        'supportingClassNameSuffix' => 'PipelineOperator',
        'libraryNamespace' => Converter::class,
        'libraryClassName' => 'PipelineOperatorConverter',
    ],
    */
    [
        // Factory
        'configFile' => __DIR__ . '/pipeline-operators.yaml',
        'generatorClass' => FactoryClassGenerator::class,
        'namespace' => Operator::class,
        'filePath' => $src . '/Aggregation/',
        'classNameSuffix' => 'Operator',
    ],

    // Query operators
    [
        'configFile' => __DIR__ . '/query-operators.yaml',
        'generatorClass' => ValueClassGenerator::class,
        'namespace' => QueryOperator::class,
        'filePath' => $src . '/Query/Operator/',
        'classNameSuffix' => 'Operator',
    ],
    /*
    [
        'configFile' => __DIR__ . '/query-operators.yaml',
        'generatorClass' => ConverterClassGenerator::class,
        'namespace' => Converter\QueryOperator::class,
        'filePath' => $src . '/Aggregation/Converter/QueryOperator/',
        'parentClass' => AbstractConverter::class,
        'classNameSuffix' => 'QueryOperatorConverter',
        'supportingNamespace' => QueryOperator::class,
        'supportingClassNameSuffix' => 'QueryOperator',
        'libraryNamespace' => Converter::class,
        'libraryClassName' => 'QueryOperatorConverter',
    ],
    */
    [
        // Factory
        'configFile' => __DIR__ . '/query-operators.yaml',
        'generatorClass' => FactoryClassGenerator::class,
        'namespace' => QueryOperator::class,
        'filePath' => $src . '/Query/',
        'classNameSuffix' => 'Operator',
    ],
];
