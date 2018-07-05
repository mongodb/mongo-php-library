<?php

namespace MongoDB\Tests\Model;

/**
 * This class is used by the BSONArray and BSONDocument clone tests.
 */
class UncloneableObject
{
    private function __clone()
    {
    }
}
