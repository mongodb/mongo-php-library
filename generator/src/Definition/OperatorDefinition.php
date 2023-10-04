<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use MongoDB\Builder\Encode;
use UnexpectedValueException;

use function array_merge;
use function sprintf;

final readonly class OperatorDefinition
{
    public Encode $encode;
    /** @var list<ArgumentDefinition> */
    public array $arguments;

    public function __construct(
        public string $name,
        public array $category,
        public string $link,
        string $encode,
        public array $type,
        public ?string $description = null,
        array $arguments = [],
    ) {
        $this->encode = match ($encode) {
            'single' => Encode::Single,
            'array' => Encode::Array,
            'object' => Encode::Object,
            'empty object' => Encode::Object,
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

        $this->arguments = array_merge($requiredArgs, $optionalArgs);
    }
}
