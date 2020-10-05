<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Bridge\PhpUnit\ConstraintTrait;
use function get_resource_type;
use function is_resource;

final class IsStream extends Constraint
{
    use ConstraintTrait;

    private function doMatches($other) : bool
    {
        return is_resource($other) && get_resource_type($other) === 'stream';
    }

    private function doToString() : string
    {
        return 'is a stream';
    }
}
