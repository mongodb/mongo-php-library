<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

interface DictionaryInterface
{
    public function getValue(): string|int|array;
}
