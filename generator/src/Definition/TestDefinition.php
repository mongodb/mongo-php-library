<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use stdClass;

use function array_is_list;
use function assert;
use function sprintf;

final class TestDefinition
{
    public function __construct(
        public string $name,
        public string|null $link = null,
        /** @var list<object> */
        public array|null $pipeline = null,
        public array|stdClass|null $filter = null,
        public array|stdClass|null $update = null,
        mixed ...$ignoredOtherArgs,
    ) {
        assert(null === $this->pipeline || array_is_list($pipeline), sprintf('Argument "%s" pipeline must be a list', $name));
    }
}
