<?php

namespace MongoDB\Tests;

use MongoDB\Driver\ReadConcern;

/**
 * Unit tests for utility functions.
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideReadConcernsAndDocuments
     */
    public function testReadConcernAsDocument(ReadConcern $readConcern, $expectedDocument)
    {
        $this->assertEquals($expectedDocument, \MongoDB\read_concern_as_document($readConcern));
    }

    public function provideReadConcernsAndDocuments()
    {
        return [
            [ new ReadConcern, (object) [] ],
            [ new ReadConcern(ReadConcern::LOCAL), (object) ['level' => ReadConcern::LOCAL] ],
            [ new ReadConcern(ReadConcern::MAJORITY), (object) ['level' => ReadConcern::MAJORITY] ],
        ];
    }
}
