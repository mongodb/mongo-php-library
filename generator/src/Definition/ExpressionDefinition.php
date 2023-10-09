<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function assert;
use function interface_exists;
use function is_string;
use function ucfirst;

final readonly class ExpressionDefinition
{
    /** @var list<string|class-string>  */
    public array $acceptedTypes;

    /** @var string|null Interface to implement for operators that resolve to this type. Generated class/enum/interface. */
    public ?string $returnType;

    public function __construct(
        public string $name,
        array $acceptedTypes,
        ?string $returnType = null,
        public ?string $extends = null,
        /** @var list<class-string> */
        public array $implements = [],
        public array $values = [],
        public ?Generate $generate = null,
    ) {
        assert($generate === Generate::PhpClass || ! $extends, $name . ': Cannot specify "extends" when "generate" is not "class"');
        assert($generate === Generate::PhpEnum || ! $this->values, $name . ': Cannot specify "values" when "generate" is not "enum"');
        assert($returnType === null || interface_exists($returnType), $name . ': Return type must be an interface');

        foreach ($acceptedTypes as $acceptedType) {
            assert(is_string($acceptedType), $name . ': AcceptedTypes must be an array of strings.');
        }

        if ($generate) {
            $returnType = 'MongoDB\\Builder\\Expression\\' . ucfirst($this->name);
        }

        $this->returnType = $returnType;
        $this->acceptedTypes = $acceptedTypes;
    }
}
