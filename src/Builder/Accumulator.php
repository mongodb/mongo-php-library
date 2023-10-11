<?php

namespace MongoDB\Builder;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\OutputWindow;
use MongoDB\Builder\Type\WindowInterface;
use stdClass;

enum Accumulator
{
    use Accumulator\FactoryTrait;

    public static function outputWindow(
        Document|Serializable|WindowInterface|stdClass|array $operator,
        Optional|array $documents = Optional::Undefined,
        Optional|array $range = Optional::Undefined,
        Optional|string $unit = Optional::Undefined,
    ): OutputWindow {
        return new OutputWindow($operator, $documents, $range, $unit);
    }
}
