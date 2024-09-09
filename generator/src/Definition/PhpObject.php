<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

/**
 * Type of PHP object to generate
 */
enum PhpObject: string
{
    case PhpClass = 'class';
    case PhpInterface = 'interface';
    case PhpEnum = 'enum';
}
