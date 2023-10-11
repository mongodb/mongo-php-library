<?php

// Target namespace for the generated files, allows to use ::class notation without use statements

namespace MongoDB\Builder\Expression;

use MongoDB\BSON;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Type;
use MongoDB\CodeGenerator\Definition\Generate;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_merge;
use function array_unique;
use function array_values;
use function ucfirst;

$bsonTypes = [
    // BSON types
    // @see https://www.mongodb.com/docs/manual/reference/bson-types/
    // Ignore deprecated types and min/max keys which are not actual types
    'double' => ['int', BSON\Int64::class, 'float'],
    'string' => ['string'],
    'object' => ['array', stdClass::class, BSON\Document::class, BSON\Serializable::class],
    'array' => ['array', BSONArray::class, BSON\PackedArray::class],
    'binData' => ['string', BSON\Binary::class],
    'objectId' => [BSON\ObjectId::class],
    'bool' => ['bool'],
    'date' => [BSON\UTCDateTime::class],
    'null' => ['null'],
    'regex' => [BSON\Regex::class],
    'javascript' => ['string'],
    'int' => ['int'],
    'timestamp' => ['int', BSON\Timestamp::class],
    'long' => ['int', BSON\Int64::class, ResolvesToInt::class],
    'decimal' => ['int', BSON\Int64::class, 'float', BSON\Decimal128::class],
];

// "any" accepts all the BSON types. No generic "object".
$bsonTypes['any'] = array_unique(array_merge(...array_values($bsonTypes)));

// "number" accepts all the numeric types
$bsonTypes['number'] = array_unique(array_merge($bsonTypes['int'], $bsonTypes['double'], $bsonTypes['long'], $bsonTypes['decimal']));

$expressions = [];
$resolvesToInterfaces = [];
foreach ($bsonTypes as $name => $acceptedTypes) {
    $expressions[$name] = ['acceptedTypes' => $acceptedTypes];

    $resolvesTo = 'resolvesTo' . ucfirst($name);
    $resolvesToInterface = __NAMESPACE__ . '\\' . ucfirst($resolvesTo);
    $expressions[$resolvesTo] = [
        'generate' => Generate::PhpInterface,
        'implements' => [Type\ExpressionInterface::class],
        'returnType' => $resolvesToInterface,
        'acceptedTypes' => $acceptedTypes,
    ];

    if ($name !== 'any') {
        $expressions[$name . 'FieldPath'] = [
            'generate' => Generate::PhpClass,
            'extends' => FieldPath::class,
            'implements' => [$resolvesToInterface],
            'acceptedTypes' => ['string'],
        ];
        $resolvesToInterfaces[] = $resolvesToInterface;
    }
}

$expressions['resolvesToLong']['implements'] = [ResolvesToInt::class];
$expressions['resolvesToInt']['implements'] = [ResolvesToNumber::class];
$expressions['resolvesToDecimal']['implements'] = [ResolvesToDouble::class];
$expressions['resolvesToDouble']['implements'] = [ResolvesToNumber::class];
$expressions['resolvesToAny']['implements'] = $resolvesToInterfaces;

return $expressions + [
    'expression' => [
        'returnType' => Type\ExpressionInterface::class,
        'acceptedTypes' => [Type\ExpressionInterface::class, ...$bsonTypes['any']],
    ],
    'fieldQuery' => [
        'returnType' => Type\FieldQueryInterface::class,
        'acceptedTypes' => [Type\FieldQueryInterface::class, ...$bsonTypes['any']],
    ],
    'query' => [
        'returnType' => Type\QueryInterface::class,
        'acceptedTypes' => [Type\QueryInterface::class, ...$bsonTypes['object']],
    ],
    'projection' => [
        'returnType' => Type\ProjectionInterface::class,
        'acceptedTypes' => [Type\ProjectionInterface::class, ...$bsonTypes['object']],
    ],
    'accumulator' => [
        'returnType' => Type\AccumulatorInterface::class,
        'acceptedTypes' => [Type\AccumulatorInterface::class, ...$bsonTypes['object']],
    ],
    'window' => [
        'returnType' => Type\WindowInterface::class,
        'acceptedTypes' => [Type\WindowInterface::class, ...$bsonTypes['object']],
    ],
    'stage' => [
        'returnType' => Type\StageInterface::class,
        'acceptedTypes' => [Type\StageInterface::class, ...$bsonTypes['object']],
    ],
    'pipeline' => [
        'acceptedTypes' => [Pipeline::class, ...$bsonTypes['array']],
    ],
    'fieldPath' => [
        'generate' => Generate::PhpClass,
        'implements' => [Type\ExpressionInterface::class],
        'acceptedTypes' => ['string'],
    ],
    'variable' => [
        'generate' => Generate::PhpClass,
        'implements' => [ResolvesToAny::class],
        'acceptedTypes' => ['string'],
    ],
    'geometry' => [
        'returnType' => Type\GeometryInterface::class,
        'acceptedTypes' => [Type\GeometryInterface::class, ...$bsonTypes['object']],
    ],

    // @todo add enum values
    'Granularity' => [
        'acceptedTypes' => [...$bsonTypes['string']],
    ],
    'FullDocument' => [
        'acceptedTypes' => [...$bsonTypes['string']],
    ],
    'FullDocumentBeforeChange' => [
        'acceptedTypes' => [...$bsonTypes['string']],
    ],
    'AccumulatorPercentile' => [
        'acceptedTypes' => [...$bsonTypes['string']],
    ],
    'WhenMatched' => [
        'acceptedTypes' => [...$bsonTypes['string']],
    ],
    'WhenNotMatched' => [
        'acceptedTypes' => [...$bsonTypes['string']],
    ],

    // @todo create specific model classes factories
    'OutCollection' => [
        'acceptedTypes' => [...$bsonTypes['object']],
    ],
    'CollStats' => [
        'acceptedTypes' => [...$bsonTypes['object']],
    ],
    'Range' => [
        'acceptedTypes' => [...$bsonTypes['object']],
    ],
    'FillOut' => [
        'acceptedTypes' => [...$bsonTypes['object']],
    ],
    'SortSpec' => [
        'acceptedTypes' => [...$bsonTypes['object']],
    ],
    'Window' => [
        'acceptedTypes' => [...$bsonTypes['object']],
    ],
    'GeoPoint' => [
        'acceptedTypes' => [...$bsonTypes['object']],
    ],
];
