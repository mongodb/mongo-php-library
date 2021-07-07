<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\BSON\Binary;
use MongoDB\BSON\UTCDateTime;

use function fclose;
use function feof;
use function fread;
use function fseek;
use function fstat;
use function fwrite;

use const SEEK_CUR;
use const SEEK_END;
use const SEEK_SET;

/**
 * Functional tests for the internal StreamWrapper class.
 */
class StreamWrapperFunctionalTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->filesCollection->insertMany([
            ['_id' => 'length-10', 'length' => 10, 'chunkSize' => 4, 'uploadDate' => new UTCDateTime('1484202200000')],
        ]);

        $this->chunksCollection->insertMany([
            ['_id' => 1, 'files_id' => 'length-10', 'n' => 0, 'data' => new Binary('abcd', Binary::TYPE_GENERIC)],
            ['_id' => 2, 'files_id' => 'length-10', 'n' => 1, 'data' => new Binary('efgh', Binary::TYPE_GENERIC)],
            ['_id' => 3, 'files_id' => 'length-10', 'n' => 2, 'data' => new Binary('ij', Binary::TYPE_GENERIC)],
        ]);
    }

    public function testReadableStreamClose(): void
    {
        $stream = $this->bucket->openDownloadStream('length-10');

        $this->assertTrue(fclose($stream));
    }

    public function testReadableStreamEof(): void
    {
        $stream = $this->bucket->openDownloadStream('length-10');

        $this->assertFalse(feof($stream));
        $this->assertStreamContents('abcdefghij', $stream);
        $this->assertTrue(feof($stream));
    }

    public function testReadableStreamRead(): void
    {
        $stream = $this->bucket->openDownloadStream('length-10');

        $this->assertSame('abc', fread($stream, 3));
        $this->assertSame('defghij', fread($stream, 10));
        $this->assertSame('', fread($stream, 3));
    }

    public function testReadableStreamSeek(): void
    {
        $stream = $this->bucket->openDownloadStream('length-10');

        $this->assertSame(0, fseek($stream, 2, SEEK_SET));
        $this->assertSame('cde', fread($stream, 3));
        $this->assertSame(0, fseek($stream, 10, SEEK_SET));
        $this->assertSame('', fread($stream, 3));
        $this->assertSame(-1, fseek($stream, -1, SEEK_SET));
        $this->assertSame(-1, fseek($stream, 11, SEEK_SET));

        $this->assertSame(0, fseek($stream, -5, SEEK_CUR));
        $this->assertSame('fgh', fread($stream, 3));
        $this->assertSame(0, fseek($stream, 1, SEEK_CUR));
        $this->assertSame('j', fread($stream, 3));
        $this->assertSame(-1, fseek($stream, 1, SEEK_CUR));
        $this->assertSame(-1, fseek($stream, -11, SEEK_CUR));

        $this->assertSame(0, fseek($stream, 0, SEEK_END));
        $this->assertSame('', fread($stream, 3));
        $this->assertSame(0, fseek($stream, -8, SEEK_END));
        $this->assertSame('cde', fread($stream, 3));
        $this->assertSame(-1, fseek($stream, -11, SEEK_END));
        $this->assertSame(-1, fseek($stream, 1, SEEK_END));
    }

    public function testReadableStreamStat(): void
    {
        $stream = $this->bucket->openDownloadStream('length-10');

        $stat = fstat($stream);
        $this->assertSame(0100444, $stat[2]);
        $this->assertSame(0100444, $stat['mode']);
        $this->assertSame(10, $stat[7]);
        $this->assertSame(10, $stat['size']);
        $this->assertSame(1484202200, $stat[9]);
        $this->assertSame(1484202200, $stat['mtime']);
        $this->assertSame(1484202200, $stat[10]);
        $this->assertSame(1484202200, $stat['ctime']);
        $this->assertSame(4, $stat[11]);
        $this->assertSame(4, $stat['blksize']);
    }

    public function testReadableStreamWrite(): void
    {
        $stream = $this->bucket->openDownloadStream('length-10');

        $this->assertSame(0, fwrite($stream, 'foobar'));
    }

    public function testWritableStreamClose(): void
    {
        $stream = $this->bucket->openUploadStream('filename');

        $this->assertSame(6, fwrite($stream, 'foobar'));
        $this->assertTrue(fclose($stream));

        $this->assertStreamContents('foobar', $this->bucket->openDownloadStreamByName('filename'));
    }

    public function testWritableStreamEof(): void
    {
        $stream = $this->bucket->openUploadStream('filename');

        $this->assertFalse(feof($stream));
        $this->assertSame(6, fwrite($stream, 'foobar'));
        $this->assertFalse(feof($stream));
    }

    public function testWritableStreamRead(): void
    {
        $stream = $this->bucket->openUploadStream('filename');

        $this->assertSame('', fread($stream, 8192));
        $this->assertSame(6, fwrite($stream, 'foobar'));
        $this->assertSame('', fread($stream, 8192));
    }

    public function testWritableStreamSeek(): void
    {
        $stream = $this->bucket->openUploadStream('filename');

        $this->assertSame(6, fwrite($stream, 'foobar'));

        $this->assertSame(-1, fseek($stream, 0, SEEK_SET));
        $this->assertSame(-1, fseek($stream, 7, SEEK_SET));
        $this->assertSame(0, fseek($stream, 6, SEEK_SET));

        $this->assertSame(0, fseek($stream, 0, SEEK_CUR));
        $this->assertSame(-1, fseek($stream, -1, SEEK_CUR));
        $this->assertSame(-1, fseek($stream, 1, SEEK_CUR));

        $this->assertSame(0, fseek($stream, 0, SEEK_END));
        $this->assertSame(-1, fseek($stream, -1, SEEK_END));
        $this->assertSame(-1, fseek($stream, 1, SEEK_END));
    }

    public function testWritableStreamStatBeforeSaving(): void
    {
        $stream = $this->bucket->openUploadStream('filename', ['chunkSizeBytes' => 1024]);

        $stat = fstat($stream);
        $this->assertSame(0100222, $stat[2]);
        $this->assertSame(0100222, $stat['mode']);
        $this->assertSame(0, $stat[7]);
        $this->assertSame(0, $stat['size']);
        $this->assertSame(0, $stat[9]);
        $this->assertSame(0, $stat['mtime']);
        $this->assertSame(0, $stat[10]);
        $this->assertSame(0, $stat['ctime']);
        $this->assertSame(1024, $stat[11]);
        $this->assertSame(1024, $stat['blksize']);

        $this->assertSame(6, fwrite($stream, 'foobar'));

        $stat = fstat($stream);
        $this->assertSame(6, $stat[7]);
        $this->assertSame(6, $stat['size']);
    }

    public function testWritableStreamStatAfterSaving(): void
    {
        $stream = $this->bucket->openDownloadStream('length-10');

        $stat = fstat($stream);
        $this->assertSame(0100444, $stat[2]);
        $this->assertSame(0100444, $stat['mode']);
        $this->assertSame(10, $stat[7]);
        $this->assertSame(10, $stat['size']);
        $this->assertSame(1484202200, $stat[9]);
        $this->assertSame(1484202200, $stat['mtime']);
        $this->assertSame(1484202200, $stat[10]);
        $this->assertSame(1484202200, $stat['ctime']);
        $this->assertSame(4, $stat[11]);
        $this->assertSame(4, $stat['blksize']);
    }

    public function testWritableStreamWrite(): void
    {
        $stream = $this->bucket->openUploadStream('filename');

        $this->assertSame(6, fwrite($stream, 'foobar'));
    }
}
