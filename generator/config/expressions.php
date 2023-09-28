<?php

// Target namespace for the generated files, allows to use ::class notation without use statements

namespace MongoDB\Builder\Expression;

use MongoDB\BSON;
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
    // Use Interface suffix to avoid confusion with MongoDB\Builder\Expression factory class
    ExpressionInterface::class => [
        'types' => ['mixed'],
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
        'types' => ['DateTimeInterface', 'UTCDateTime'],
    ],
    DateFieldPath::class => typeFieldPath(ResolvesToDate::class),
    ResolvesToObject::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['array', 'object', BSON\Document::class, BSON\Serializable::class],
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
    ResolvesToString::class => [
        'implements' => [ExpressionInterface::class],
        'types' => ['string'],
    ],
    StringFieldPath::class => typeFieldPath(ResolvesToString::class),
];
