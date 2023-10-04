<?php

namespace MongoDB\Builder;

enum Encode
{
    // @todo add comments (see schema.json)
    case Array;
    case Object;
    case Single;
    case Group;
}
