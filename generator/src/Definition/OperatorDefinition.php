<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use InvalidArgumentException;
use MongoDB\Builder\Type\Encode;
use UnexpectedValueException;

use function array_is_list;
use function array_map;
use function array_merge;
use function array_values;
use function assert;
use function count;
use function get_debug_type;
use function get_object_vars;
use function is_object;
use function is_string;
use function sprintf;
use function version_compare;

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
        public bool $wrapObject = true,
        array $arguments = [],
        array $tests = [],
        public string $minVersion = '',
        mixed ...$ignoredOtherArgs,
    ) {
        $this->encode = match ($encode) {
            'single' => Encode::Single,
            'array' => Encode::Array,
            'object' => Encode::Object,
            default => throw new UnexpectedValueException(sprintf('Unexpected "encode" value for operator "%s". Got "%s"', $name, $encode)),
        };

        if (! $wrapObject && $this->encode !== Encode::Object) {
            throw new UnexpectedValueException(sprintf('Operator "%s" cannot have wrapObject set to false when encode is not "object"', $name));
        }

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

        // The type can be an object with other properties. Ignoring other properties, we only keep the "name" property.
        assert(array_is_list($this->type), 'Type must be a list of string');
        foreach ($this->type as &$t) {
            if (is_object($t)) {
                $t =  $t->name ?? throw new InvalidArgumentException('Type array must have a "name" key');
            }

            assert(is_string($t), sprintf('Type must be a list of strings. Got %s', get_debug_type($t)));
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

        if ($this->minVersion && version_compare($this->minVersion, '4.4', '>=')) {
            $this->description .= sprintf("\nNew in MongoDB %s\n", $this->minVersion);
        }
    }
}
