<?php

namespace MongoDB\Builder;

/**
 * Defines how to encode a stage or an operator into BSON.
 *
 * @see BuilderEncoder
 */
enum Encode
{
    // @todo add comments (see schema.json)
    case Array;
    case Object;
    case Single;
    case Group;
}
