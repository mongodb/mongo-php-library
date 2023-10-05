<?php

// Target namespace for the generated files, allows to use ::class notation without use statements

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON;
use MongoDB\Builder\Aggregation\AccumulatorInterface;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query\QueryInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/** @param class-string $resolvesTo */
function typeFieldPath(string $resolvesTo): array
{
    return [
        'class' => true,
        'extends' => FieldPath::class,
        'implements' => [$resolvesTo],
        'types' => ['string'],
    ];
}

return [
    'mixed' => ['scalar' => true, 'types' => ['mixed']],
    'null' => ['scalar' => true, 'types' => ['null']],
    'int' => ['scalar' => true, 'types' => ['int', BSON\Int64::class]],
    'double' => ['scalar' => true, 'types' => ['int', BSON\Int64::class, 'float']],
    'float' => ['scalar' => true, 'types' => ['int', BSON\Int64::class, 'float']],
    'decimal' => ['scalar' => true, 'types' => ['int', BSON\Int64::class, 'float', BSON\Decimal128::class]],
    'number' => ['scalar' => true, 'types' => ['int', BSON\Int64::class, 'float', BSON\Decimal128::class]],
    'string' => ['scalar' => true, 'types' => ['string']],
    'bool' => ['scalar' => true, 'types' => ['bool']],
    'object' => ['scalar' => true, 'types' => ['array', stdClass::class, BSON\Document::class, BSON\Serializable::class]],
    'Regex' => ['scalar' => true, 'types' => [BSON\Regex::class]],
    'Constant' => ['scalar' => true, 'types' => ['mixed']],
    'Binary' => ['scalar' => true, 'types' => ['string', BSON\Binary::class]],

    AccumulatorInterface::class => ['scalar' => true, 'types' => [AccumulatorInterface::class]],
    QueryInterface::class => ['scalar' => true, 'types' => [QueryInterface::class, 'array', stdClass::class]],

    // @todo merge this types
    'list' => ['scalar' => true, 'types' => ['list', BSONArray::class, BSON\PackedArray::class]],
    'array' => ['scalar' => true, 'types' => ['list', BSONArray::class, BSON\PackedArray::class]],

    // @todo fine-tune all this types
    'Granularity' => ['scalar' => true, 'types' => ['string']],
    'FullDocument' => ['scalar' => true, 'types' => ['string']],
    'FullDocumentBeforeChange' => ['scalar' => true, 'types' => ['string']],
    'AccumulatorPercentile' => ['scalar' => true, 'types' => ['string']],
    'Timestamp' => ['scalar' => true, 'types' => ['int']],
    'CollStats' => ['scalar' => true, 'types' => [stdClass::class, 'array']],
    'Range' => ['scalar' => true, 'types' => [stdClass::class, 'array']],
    'FillOut' => ['scalar' => true, 'types' => [stdClass::class, 'array']],
    'WhenMatched' => ['scalar' => true, 'types' => ['string']],
    'WhenNotMatched' => ['scalar' => true, 'types' => ['string']],
    'OutCollection' => ['scalar' => true, 'types' => ['string', stdClass::class, 'array']],
    'Pipeline' => ['scalar' => true, 'types' => [Pipeline::class, 'array']],
    'SortSpec' => ['scalar' => true, 'types' => [stdClass::class, 'array']],
    'Window' => ['scalar' => true, 'types' => [stdClass::class, 'array']],
    'GeoPoint' => ['scalar' => true, 'types' => [stdClass::class, 'array']],
    'Geometry' => ['scalar' => true, 'types' => [stdClass::class, 'array']],

    // Use Interface suffix to avoid confusion with MongoDB\Builder\Expression factory class
    ExpressionInterface::class => [
        'types' => ['mixed'],
    ],
    // @todo must not start with $
    // Allows ORMs to translate field names
    FieldName::class => [
        'class' => true,
        'types' => ['string'],
    ],
    // @todo if replaced by a string, it must start with $
    FieldPath::class => [
        'class' => true,
        'implements' => [ExpressionInterface::class],
        'types' => ['string'],
    ],
    // @todo if replaced by a string, it must start with $$
    Variable::class => [
        'class' => true,
        'implements' => [ExpressionInterface::class],
        'types' => ['string'],
    ],
    Literal::class => [
        'class' => true,
        'implements' => [ExpressionInterface::class],
        'types' => ['mixed'],
    ],
    // @todo check for use-case
    ExpressionObject::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['array', stdClass::class, BSON\Document::class, BSON\Serializable::class],
    ],
    // @todo check for use-case
    Operator::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['array', stdClass::class, BSON\Document::class, BSON\Serializable::class],
    ],
    ResolvesToArray::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['list', BSONArray::class, BSON\PackedArray::class],
    ],
    ArrayFieldPath::class => typeFieldPath(ResolvesToArray::class),
    ResolvesToBool::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['bool'],
    ],
    BoolFieldPath::class => typeFieldPath(ResolvesToBool::class),
    ResolvesToDate::class => [
        'implements' => [ExpressionInterface::class],
        'types' => [DateTimeInterface::class, BSON\UTCDateTime::class],
    ],
    DateFieldPath::class => typeFieldPath(ResolvesToDate::class),
    ResolvesToTimestamp::class => [
        'implements' => [ResolvesToInt::class],
        'types' => ['int', BSON\Int64::class],
    ],
    TimestampFieldPath::class => typeFieldPath(ResolvesToTimestamp::class),
    ResolvesToObjectId::class => [
        'implements' => [ExpressionInterface::class],
        'types' => [BSON\ObjectId::class],
    ],
    ObjectIdFieldPath::class => typeFieldPath(ResolvesToObjectId::class),
    ResolvesToObject::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['array', stdClass::class, BSON\Document::class, BSON\Serializable::class],
    ],
    ObjectFieldPath::class => typeFieldPath(ResolvesToObject::class),
    ResolvesToNull::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['null'],
    ],
    NullFieldPath::class => typeFieldPath(ResolvesToNull::class),
    ResolvesToNumber::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['int', 'float', BSON\Int64::class, BSON\Decimal128::class],
    ],
    NumberFieldPath::class => typeFieldPath(ResolvesToNumber::class),
    ResolvesToDecimal::class => [
        'implements' => [ResolvesToNumber::class],
        'types' => ['int', 'float', BSON\Int64::class, BSON\Decimal128::class],
    ],
    DecimalFieldPath::class => typeFieldPath(ResolvesToDecimal::class),
    ResolvesToDouble::class => [
        'implements' => [ResolvesToNumber::class],
        'types' => ['int', BSON\Int64::class, 'float'],
    ],
    DoubleFieldPath::class => typeFieldPath(ResolvesToDouble::class),
    ResolvesToFloat::class => [
        'implements' => [ResolvesToNumber::class],
        'types' => ['int', 'float', BSON\Int64::class],
    ],
    FloatFieldPath::class => typeFieldPath(ResolvesToFloat::class),
    ResolvesToInt::class => [
        'implements' => [ResolvesToNumber::class],
        'types' => ['int', BSON\Int64::class],
    ],
    IntFieldPath::class => typeFieldPath(ResolvesToInt::class),
    ResolvesToLong::class => [
        'implements' => [ResolvesToNumber::class],
        'types' => ['int', BSON\Int64::class],
    ],
    LongFieldPath::class => typeFieldPath(ResolvesToLong::class),
    ResolvesToString::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['string'],
    ],
    StringFieldPath::class => typeFieldPath(ResolvesToString::class),
    ResolvesToBinary::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['string', BSON\Binary::class],
    ],
    BinaryFieldPath::class => typeFieldPath(ResolvesToBinary::class),
];
