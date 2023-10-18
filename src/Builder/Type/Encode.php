<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

use MongoDB\Builder\BuilderEncoder;

/**
 * Defines how to encode a stage or an operator into BSON.
 *
 * @see BuilderEncoder
 */
enum Encode
{
    /**
     * Parameters are encoded as an array of values in the order they are defined by the spec and declared in the object.
     */
    case Array;

    /**
     * Parameters are encoded as an object with keys matching the parameter names
     */
    case Object;

    /**
     * Get the single parameter value
     */
    case Single;

    /**
     * Specific for $group stage
     */
    case Group;
}
