<?php

declare(strict_types=1);

namespace MongoDB\Builder;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Encoder\CombinedFieldQueryEncoder;
use MongoDB\Builder\Encoder\DateTimeEncoder;
use MongoDB\Builder\Encoder\DictionaryEncoder;
use MongoDB\Builder\Encoder\ExpressionEncoder;
use MongoDB\Builder\Encoder\FieldPathEncoder;
use MongoDB\Builder\Encoder\OperatorEncoder;
use MongoDB\Builder\Encoder\OutputWindowEncoder;
use MongoDB\Builder\Encoder\PipelineEncoder;
use MongoDB\Builder\Encoder\QueryEncoder;
use MongoDB\Builder\Encoder\VariableEncoder;
use MongoDB\Builder\Expression\Variable;
use MongoDB\Builder\Type\CombinedFieldQuery;
use MongoDB\Builder\Type\DictionaryInterface;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\FieldPathInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\OutputWindow;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function array_key_exists;
use function is_object;

/** @template-implements Encoder<Type|stdClass|array|string|int, Pipeline|StageInterface|ExpressionInterface|QueryInterface> */
final class BuilderEncoder implements Encoder
{
    /** @template-use EncodeIfSupported<Type|stdClass|array|string|int, Pipeline|StageInterface|ExpressionInterface|QueryInterface> */
    use EncodeIfSupported;

    /** @var array<class-string, class-string<ExpressionEncoder>> */
    private array $defaultEncoders = [
        Pipeline::class => PipelineEncoder::class,
        Variable::class => VariableEncoder::class,
        DictionaryInterface::class => DictionaryEncoder::class,
        FieldPathInterface::class => FieldPathEncoder::class,
        CombinedFieldQuery::class => CombinedFieldQueryEncoder::class,
        QueryObject::class => QueryEncoder::class,
        OutputWindow::class => OutputWindowEncoder::class,
        OperatorInterface::class => OperatorEncoder::class,
        DateTimeInterface::class => DateTimeEncoder::class,
    ];

    /** @var array<class-string, ExpressionEncoder|null> */
    private array $cachedEncoders = [];

    /** @param array<class-string, class-string<ExpressionEncoder>> $customEncoders */
    public function __construct(private readonly array $customEncoders = [])
    {
    }

    /** @psalm-assert-if-true object $value */
    public function canEncode(mixed $value): bool
    {
        if (! is_object($value)) {
            return false;
        }

        return (bool) $this->getEncoderFor($value)?->canEncode($value);
    }

    public function encode(mixed $value): Type|stdClass|array|string|int
    {
        $encoder = $this->getEncoderFor($value);

        if (! $encoder?->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return $encoder->encode($value);
    }

    private function getEncoderFor(object $value): ExpressionEncoder|null
    {
        $valueClass = $value::class;
        if (array_key_exists($valueClass, $this->cachedEncoders)) {
            return $this->cachedEncoders[$valueClass];
        }

        $encoderList = $this->customEncoders + $this->defaultEncoders;

        // First attempt: match class name exactly
        if (isset($encoderList[$valueClass])) {
            return $this->cachedEncoders[$valueClass] = new $encoderList[$valueClass]($this);
        }

        // Second attempt: catch child classes
        foreach ($encoderList as $className => $encoderClass) {
            if ($value instanceof $className) {
                return $this->cachedEncoders[$valueClass] = new $encoderClass($this);
            }
        }

        return $this->cachedEncoders[$valueClass] = null;
    }
}
