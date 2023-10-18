<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use MongoDB\Builder\Type\Encode;
use UnexpectedValueException;

use function array_merge;
use function assert;
use function count;
use function sprintf;

final class OperatorDefinition
{
    public Encode $encode;

    /** @var list<ArgumentDefinition> */
    public array $arguments;

    public function __construct(
        public string $name,
        public string $link,
        string $encode,
        /** @var list<string> */
        public array $type,
        public string|null $description = null,
        array $arguments = [],
    ) {
        $this->encode = match ($encode) {
            'single' => Encode::Single,
            'array' => Encode::Array,
            'object' => Encode::Object,
            'group' => Encode::Group,
            default => throw new UnexpectedValueException(sprintf('Unexpected "encode" value for operator "%s". Got "%s"', $name, $encode)),
        };

        // Convert arguments to ArgumentDefinition objects
        // Optional arguments must be after required arguments
        $requiredArgs = $optionalArgs = [];
        foreach ($arguments as $arg) {
            $arg = new ArgumentDefinition(...$arg);
            if ($arg->optional) {
                $optionalArgs[] = $arg;
            } else {
                $requiredArgs[] = $arg;
            }
        }

        // "single" encode operators must have one required argument
        if ($this->encode === Encode::Single) {
            assert(count($requiredArgs) === 1, sprintf('Single encode operator "%s" must have one argument', $name));
            assert(count($optionalArgs) === 0, sprintf('Single encode operator "%s" argument cannot be optional', $name));
        }

        $this->arguments = array_merge($requiredArgs, $optionalArgs);
    }
}
