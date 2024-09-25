<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use MongoDB\Builder\Type\Encode;
use UnexpectedValueException;

use function array_map;
use function array_merge;
use function array_values;
use function assert;
use function count;
use function get_object_vars;
use function sprintf;

final class OperatorDefinition
{
    public readonly Encode $encode;

    /** @var list<ArgumentDefinition> */
    public readonly array $arguments;

    /** @var list<TestDefinition> */
    public readonly array $tests;

    public function __construct(
        public string $name,
        public string $link,
        string $encode,
        /** @var list<string> */
        public array $type,
        public string|null $description = null,
        array $arguments = [],
        array $tests = [],
    ) {
        $this->encode = match ($encode) {
            'single' => Encode::Single,
            'array' => Encode::Array,
            'object' => Encode::Object,
            'flat_object' => Encode::FlatObject,
            'dollar_object' => Encode::DollarObject,
            'group' => Encode::Group,
            default => throw new UnexpectedValueException(sprintf('Unexpected "encode" value for operator "%s". Got "%s"', $name, $encode)),
        };

        // Convert arguments to ArgumentDefinition objects
        // Optional arguments must be after required arguments
        $requiredArgs = $optionalArgs = [];
        foreach ($arguments as $arg) {
            $arg = new ArgumentDefinition(...get_object_vars($arg));
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

        $this->tests = array_map(
            static fn (object $test): TestDefinition => new TestDefinition(...get_object_vars($test)),
            array_values($tests),
        );
    }
}
