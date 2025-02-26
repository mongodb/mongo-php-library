<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\BSON\Type;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Codec\Encoder;
use stdClass;

/**
 * @template BSONType of Type|stdClass|array|string|int
 * @template NativeType
 * @template-extends Encoder<BSONType, NativeType>
 * @internal
 */
interface ExpressionEncoder extends Encoder
{
    public function __construct(BuilderEncoder $encoder);
}
