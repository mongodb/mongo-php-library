<?php

namespace MongoDB\CodeGenerator\Definition;

/**
 * Type of PHP object to generate
 */
enum Generate: string
{
    case PhpClass = 'class';
    case PhpInterface = 'interface';
    case PhpEnum = 'enum';
}
