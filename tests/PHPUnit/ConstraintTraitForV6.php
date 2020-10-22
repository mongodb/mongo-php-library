<?php

namespace MongoDB\Tests\PHPUnit;

use Symfony\Bridge\PhpUnit\Legacy\ConstraintTraitForV6 as BaseConstraintTraitForV6;

trait ConstraintTraitForV6
{
    use BaseConstraintTraitForV6;
    use EvaluateTrait;

    public function evaluate($other, $description = '', $returnResult = false)
    {
        return $this->doEvaluate($other, $description, $returnResult);
    }
}
