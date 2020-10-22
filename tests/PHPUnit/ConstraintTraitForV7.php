<?php

namespace MongoDB\Tests\PHPUnit;

use SebastianBergmann\Exporter\Exporter;
use Symfony\Bridge\PhpUnit\Legacy\ConstraintTraitForV7 as BaseConstraintTraitForV7;

trait ConstraintTraitForV7
{
    use BaseConstraintTraitForV7;
    use EvaluateTrait;

    public function evaluate($other, $description = '', $returnResult = false)
    {
        return $this->doEvaluate($other, $description, $returnResult);
    }

    protected function exporter() : Exporter
    {
        if (! isset($this->exporter)) {
            $this->exporter = new Exporter();
        }

        return $this->exporter;
    }
}
