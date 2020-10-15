<?php

namespace MongoDB\Tests\PHPUnit;

trait EvaluateTrait
{
    private function doEvaluate($other, $description, $returnResult)
    {
        $success = false;

        if ($this->matches($other)) {
            $success = true;
        }

        if ($returnResult) {
            return $success;
        }

        if (! $success) {
            $this->fail($other, $description);
        }

        return null;
    }
}
