<?php

use MongoDB\CodeGenerator\ValueClassGenerator;

$src = __DIR__ . '/../../src';

return [
    'stages' => [
        [
            // Stage expression classes
            'configFile' => __DIR__ . '/stages.yaml',
            'namespace' => Stage::class,
            'filePath' => $src . '/Aggregation/Stage/',
            'interfaces' => [Stage::class],
            'classNameSuffix' => 'Stage',
        ],
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
        [
            // Factory
            'configFile' => __DIR__ . '/stages.yaml',
            'generatorClass' => FactoryClassGenerator::class,
            'className' => 'StageFactory',
            'namespace' => Factory::class,
            'filePath' => $src . '/Aggregation/Factory/',
            'supportingNamespace' => Stage::class,
            'supportingClassNameSuffix' => 'Stage',
        ],
    ],
    'pipeline-operators' => [
        [
            'configFile' => __DIR__ . '/pipeline-operators.yaml',
            'generatorClass' => ValueClassGenerator::class,
            'namespace' => MongoDB\Aggregation\PipelineOperator::class,
            'filePath' => $src . '/Aggregation/PipelineOperator/',
            'classNameSuffix' => 'PipelineOperator',
        ],
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
        [
            // Factory
            'configFile' => __DIR__ . '/pipeline-operators.yaml',
            'generatorClass' => FactoryClassGenerator::class,
            'className' => 'PipelineOperatorFactory',
            'namespace' => Factory::class,
            'filePath' => $src . '/Aggregation/Factory/',
            'supportingNamespace' => PipelineOperator::class,
            'supportingClassNameSuffix' => 'PipelineOperator',
        ],
    ],
    'query-operators' => [
        [
            'configFile' => __DIR__ . '/query-operators.yaml',
            // These are simple value holders, overwriting is explicitly wanted
            'namespace' => QueryOperator::class,
            'filePath' => $src . '/Aggregation/QueryOperator/',
            'classNameSuffix' => 'QueryOperator',
        ],
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
        [
            // Factory
            'configFile' => __DIR__ . '/query-operators.yaml',
            'generatorClass' => FactoryClassGenerator::class,
            'className' => 'QueryOperatorFactory',
            'namespace' => Factory::class,
            'filePath' => $src . '/Aggregation/Factory/',
            'supportingNamespace' => QueryOperator::class,
            'supportingClassNameSuffix' => 'QueryOperator',
        ],
    ],
];
