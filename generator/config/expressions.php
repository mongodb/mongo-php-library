<?php

declare(strict_types=1);

// Target namespace for the generated files, allows to use ::class notation without use statements

namespace MongoDB\Builder\Expression;

use MongoDB\BSON;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Type;
use MongoDB\CodeGenerator\Definition\PhpObject;
use MongoDB\Model\BSONArray;
use stdClass;

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
    'javascript' => ['string', BSON\Javascript::class],
    'int' => ['int'],
    'timestamp' => ['int', BSON\Timestamp::class],
    'long' => ['int', BSON\Int64::class],
    'decimal' => ['int', BSON\Int64::class, 'float', BSON\Decimal128::class],
];

// "any" accepts all the BSON types. No generic "object" or "mixed"
$bsonTypes['any'] = ['bool', 'int', 'float', 'string', 'array', 'null', stdClass::class, BSON\Type::class];

// "number" accepts all the numeric types
$bsonTypes['number'] = ['int', 'float', BSON\Int64::class, BSON\Decimal128::class];

$expressions = [];
$resolvesToInterfaces = [];
foreach ($bsonTypes as $name => $acceptedTypes) {
    $expressions[$name] = ['acceptedTypes' => $acceptedTypes];

    $resolvesTo = 'resolvesTo' . ucfirst($name);
    $resolvesToInterface = __NAMESPACE__ . '\\' . ucfirst($resolvesTo);
    $expressions[$resolvesTo] = [
        'generate' => PhpObject::PhpInterface,
        'implements' => [Type\ExpressionInterface::class],
        'returnType' => $resolvesToInterface,
        'acceptedTypes' => $acceptedTypes,
    ];

    $fieldPathName = $name . 'FieldPath';
    if ($name === 'any') {
        $fieldPathName = 'fieldPath';
    } else {
        $resolvesToInterfaces[] = $resolvesToInterface;
    }

    $expressions[$fieldPathName] = [
        'generate' => PhpObject::PhpClass,
        'implements' => [Type\FieldPathInterface::class, $resolvesToInterface],
        'acceptedTypes' => ['string'],
    ];
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
        'acceptedTypes' => [Type\QueryInterface::class, 'array'],
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
    'variable' => [
        'generate' => PhpObject::PhpClass,
        'implements' => [ResolvesToAny::class],
        'acceptedTypes' => ['string'],
    ],
    'geometry' => [
        'returnType' => Type\GeometryInterface::class,
        'acceptedTypes' => [Type\GeometryInterface::class, ...$bsonTypes['object']],
    ],
    'switchBranch' => [
        'returnType' => Type\SwitchBranchInterface::class,
        'acceptedTypes' => [Type\SwitchBranchInterface::class, ...$bsonTypes['object']],
    ],
    'timeUnit' => [
        'returnType' => Type\TimeUnit::class,
        'acceptedTypes' => [Type\TimeUnit::class, ResolvesToString::class, ...$bsonTypes['string']],
    ],
    'sortSpec' => [
        'returnType' => Type\Sort::class,
        'acceptedTypes' => [Type\Sort::class],
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
