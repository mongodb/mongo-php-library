<?php

namespace MongoDB\Exception;

class InvalidArgumentTypeException extends InvalidArgumentException
{
    public function __construct($name, $value, $expectedType)
    {
        parent::__construct(sprintf('Expected %s to have type "%s" but found "%s"', $name, $expectedType, is_object($value) ? get_class($value) : gettype($value)));
    }
}
