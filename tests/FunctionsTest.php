<?php

namespace MongoDB\Tests;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\WriteConcern;

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

    /**
     * @dataProvider provideWriteConcernsAndDocuments
     */
    public function testWriteConcernAsDocument(WriteConcern $writeConcern, $expectedDocument)
    {
        $this->assertEquals($expectedDocument, \MongoDB\write_concern_as_document($writeConcern));
    }

    public function provideWriteConcernsAndDocuments()
    {
        return [
            [ new WriteConcern(-3), (object) ['w' => 'majority'] ], // MONGOC_WRITE_CONCERN_W_MAJORITY
            [ new WriteConcern(-2), (object) [] ], // MONGOC_WRITE_CONCERN_W_DEFAULT
            [ new WriteConcern(-1), (object) ['w' => -1] ],
            [ new WriteConcern(0), (object) ['w' => 0] ],
            [ new WriteConcern(1), (object) ['w' => 1] ],
            [ new WriteConcern('majority'), (object) ['w' => 'majority'] ],
            [ new WriteConcern('tag'), (object) ['w' => 'tag'] ],
            [ new WriteConcern(1, 0), (object) ['w' => 1] ],
            [ new WriteConcern(1, 0, false), (object) ['w' => 1, 'j' => false] ],
            [ new WriteConcern(1, 1000), (object) ['w' => 1, 'wtimeout' => 1000] ],
            [ new WriteConcern(1, 1000, true), (object) ['w' => 1, 'wtimeout' => 1000, 'j' => true] ],
            [ new WriteConcern(-2, 0, true), (object) ['j' => true] ],
            // Note: wtimeout is only applicable applies for w > 1
            [ new WriteConcern(-2, 1000), (object) ['wtimeout' => 1000] ],
        ];
    }
}
