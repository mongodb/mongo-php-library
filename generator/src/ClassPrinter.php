<?php

declare(strict_types=1);

namespace MongoDB\CodeGenerator;

use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PsrPrinter;

/**
 * Custom printer that allows single-line docblocks for methods with single-line content.
 * Nette's default PsrPrinter forces multi-line format for all Method docblocks.
 */
class ClassPrinter extends PsrPrinter
{
    protected function printDocComment(mixed $commentable): string
    {
        if ($commentable instanceof Method) {
            return Helpers::formatDocComment((string) $commentable->getComment(), false);
        }

        return parent::printDocComment($commentable);
    }
}
