<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function assert;
use function is_string;
use function ucfirst;

final class ExpressionDefinition
{
    public function __construct(
        public string $name,
        /** @var list<string> */
        public array $acceptedTypes,
        /** Interface to implement for operators that resolve to this type. Generated class/enum/interface. */
        public string|null $returnType = null,
        public string|null $extends = null,
        /** @var list<class-string> */
        public array $implements = [],
        public array $values = [],
        public PhpObject|null $generate = null,
    ) {
        assert($generate === PhpObject::PhpClass || ! $extends, $name . ': Cannot specify "extends" when "generate" is not "class"');
        assert($generate === PhpObject::PhpEnum || ! $this->values, $name . ': Cannot specify "values" when "generate" is not "enum"');
        //assert($returnType === null || interface_exists($returnType), $name . ': Return type must be an interface');

        foreach ($acceptedTypes as $acceptedType) {
            assert(is_string($acceptedType), $name . ': AcceptedTypes must be an array of strings.');
        }

        if ($generate) {
            $this->returnType = 'MongoDB\\Builder\\Expression\\' . ucfirst($this->name);
        }
    }
}
