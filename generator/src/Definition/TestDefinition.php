<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function array_is_list;
use function assert;
use function sprintf;

final class TestDefinition
{
    public function __construct(
        public string $name,
        /** @var list<object> */
        public array $pipeline,
        public string|null $link = null,
    ) {
        assert(array_is_list($pipeline), sprintf('Argument "%s" pipeline must be a list', $name));
    }
}
