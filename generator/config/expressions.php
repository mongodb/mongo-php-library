<?php

// Target namespace for the generated files, allows to use ::class notation without use statements

namespace MongoDB\Builder\Expression;

use MongoDB\BSON;
use MongoDB\Model\BSONArray;
use stdClass;

return [
    Expression::class => [
        'types' => ['mixed'],
    ],
    FieldPath::class => [
        'class' => true,
        'implements' => [Expression::class],
        'types' => ['string'],
    ],
    Variable::class => [
        'class' => true,
        'implements' => [Expression::class],
        'types' => ['string'],
    ],
    Literal::class => [
        'class' => true,
        'implements' => [Expression::class],
        'types' => ['mixed'],
    ],
    ExpressionObject::class => [
        'class' => true,
        'implements' => [Expression::class],
        'types' => ['array', stdClass::class, BSON\Document::class, BSON\Serializable::class],
    ],
    Operator::class => [
        'implements' => [Expression::class],
        'types' => ['array', stdClass::class, BSON\Document::class, BSON\Serializable::class],
    ],
    ResolvesToArray::class => [
        'implements' => [Expression::class],
        'types' => ['list', BSONArray::class, BSON\PackedArray::class],
    ],
    ResolvesToBool::class => [
        'implements' => [Expression::class],
        'types' => ['bool'],
    ],
    ResolvesToDate::class => [
        'implements' => [Expression::class],
        'types' => ['DateTimeInterface', 'UTCDateTime'],
    ],
    ResolvesToObject::class => [
        'implements' => [Expression::class],
        'types' => ['array', 'object', BSON\Document::class, BSON\Serializable::class],
    ],
    ResolvesToNull::class => [
        'implements' => [Expression::class],
        'types' => ['null'],
    ],
    ResolvesToNumber::class => [
        'implements' => [Expression::class],
        'types' => ['int', 'float', BSON\Int64::class, BSON\Decimal128::class],
    ],
    ResolvesToDecimal::class => [
        'implements' => [ResolvesToNumber::class],
        'types' => ['int', 'float', BSON\Int64::class, BSON\Decimal128::class],
    ],
    ResolvesToFloat::class => [
        'implements' => [ResolvesToNumber::class],
        'types' => ['int', 'float', BSON\Int64::class],
    ],
    ResolvesToInt::class => [
        'implements' => [ResolvesToNumber::class],
        'types' => ['int', BSON\Int64::class],
    ],
    ResolvesToString::class => [
        'implements' => [Expression::class],
        'types' => ['string'],
    ],
];
