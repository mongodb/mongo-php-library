<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

/**
 * Allow to omit optional arguments in operator builders.
 * NULL cannot be used to mark an argument as not set, because it is a valid value for some operators.
 *
 * @internal
 */
enum Optional
{
    /** The argument value is not set */
    case Undefined;
}
