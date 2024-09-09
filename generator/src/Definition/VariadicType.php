<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

enum VariadicType: string
{
    case Array = 'array';
    case Object = 'object';
}
