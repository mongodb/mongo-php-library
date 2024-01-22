<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use InvalidArgumentException;
use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Pipeline;
use MongoDB\CodeGenerator\Definition\GeneratorDefinition;
use MongoDB\CodeGenerator\Definition\OperatorDefinition;
use MongoDB\Tests\Builder\PipelineTestCase;
use Nette\PhpGenerator\EnumType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Type;
use RuntimeException;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Throwable;

use function base64_decode;
use function basename;
use function get_object_vars;
use function is_array;
use function is_object;
use function json_decode;
use function json_encode;
use function ksort;
use function sprintf;
use function str_replace;
use function ucwords;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;

/**
 * Generates a tests for all operators.
 */
class OperatorTestGenerator extends OperatorGenerator
{
    private const DATA_ENUM = 'Pipelines';

    public function generate(GeneratorDefinition $definition): void
    {
        $dataNamespace = $this->createExpectedClass($definition);

        foreach ($this->getOperators($definition) as $operator) {
            // Skip operators without tests
            if (! $operator->tests) {
                continue;
            }

            try {
                $this->writeFile($this->createClass($definition, $operator, $dataNamespace->getClasses()[self::DATA_ENUM]), false);
            } catch (Throwable $e) {
                throw new RuntimeException(sprintf('Failed to generate class for operator "%s"', $operator->name), 0, $e);
            }
        }

        $this->writeFile($dataNamespace);
    }

    public function createExpectedClass(GeneratorDefinition $definition): PhpNamespace
    {
        $dataNamespace = str_replace('MongoDB', 'MongoDB\\Tests', $definition->namespace);

        $namespace = new PhpNamespace($dataNamespace);
        $enum = $namespace->addEnum(self::DATA_ENUM);
        $enum->setType('string');

        return $namespace;
    }

    public function createClass(GeneratorDefinition $definition, OperatorDefinition $operator, EnumType $dataEnum): PhpNamespace
    {
        $testNamespace = str_replace('MongoDB', 'MongoDB\\Tests', $definition->namespace);
        $testClass = $this->getOperatorClassName($definition, $operator) . 'Test';

        $namespace = $this->readFile($testNamespace, $testClass)?->getNamespaces()[$testNamespace] ?? null;
        $namespace ??= new PhpNamespace($testNamespace);

        $class = $namespace->getClasses()[$testClass] ?? null;
        $class ??= $namespace->addClass($testClass);
        $namespace->addUse(PipelineTestCase::class);
        $class->setExtends(PipelineTestCase::class);
        $namespace->addUse(Pipeline::class);
        $class->setComment('Test ' . $operator->name . ' ' . basename($definition->configFiles));

        foreach ($operator->tests as $test) {
            $testName = 'test' . str_replace([' ', '-'], '', ucwords(str_replace('$', '', $test->name)));
            $caseName = str_replace([' ', '-'], '', ucwords(str_replace('$', '', $operator->name . ' ' . $test->name)));

            $pipeline = $this->convertYamlTaggedValues($test->pipeline);

            // Wrap the pipeline array into a document
            $json = Document::fromPHP(['pipeline' => $pipeline])->toCanonicalExtendedJSON();
            // Unwrap the pipeline array and reformat for prettier JSON
            $json = json_encode(json_decode($json)->pipeline, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $case = $dataEnum->addCase($caseName, new Literal('<<<\'JSON\'' . "\n" . $json . "\n" . 'JSON'));
            $case->setComment($test->name);
            if ($test->link) {
                $case->addComment('');
                $case->addComment('@see ' . $test->link);
            }

            $caseName = self::DATA_ENUM . '::' . $caseName;

            if ($class->hasMethod($testName)) {
                $testMethod = $class->getMethod($testName);
            } else {
                $testMethod = $class->addMethod($testName);
                $testMethod->setBody(<<<PHP
                \$pipeline = new Pipeline();

                \$this->assertSamePipeline({$caseName}, \$pipeline);
                PHP);
            }

            $testMethod->setPublic();
            $testMethod->setReturnType(Type::Void);
        }

        $methods = $class->getMethods();
        ksort($methods);
        $class->setMethods($methods);

        return $namespace;
    }

    private function convertYamlTaggedValues(mixed $object): mixed
    {
        if ($object instanceof TaggedValue) {
            $value = $object->getValue();

            return match ($object->getTag()) {
                'bson_regex' => new Regex(...(array) $value),
                'bson_int128' => new Int64($value),
                'bson_decimal128' => new Decimal128($value),
                'bson_utcdatetime' => new UTCDateTime($value),
                'bson_binary' => new Binary(base64_decode($value)),
                default => throw new InvalidArgumentException(sprintf('Yaml tag "%s" is not supported.', $object->getTag())),
            };
        }

        if (is_array($object)) {
            foreach ($object as $key => $value) {
                $object[$key] = $this->convertYamlTaggedValues($value);
            }

            return $object;
        }

        if (is_object($object)) {
            foreach (get_object_vars($object) as $key => $value) {
                $object->{$key} = $this->convertYamlTaggedValues($value);
            }

            return $object;
        }

        return $object;
    }
}
