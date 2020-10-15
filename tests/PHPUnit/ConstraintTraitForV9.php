<?php

namespace MongoDB\Tests\PHPUnit;

use Symfony\Bridge\PhpUnit\Legacy\ConstraintTraitForV7;

trait ConstraintTraitForV9
{
    use ConstraintTraitForV7;
    use EvaluateTrait;

    public function evaluate($other, string $description = '', bool $returnResult = false) : ?bool
    {
        return $this->doEvaluate($other, $description, $returnResult);
    }
}
