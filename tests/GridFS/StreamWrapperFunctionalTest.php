<?php

namespace MongoDB\Tests\GridFS;

use MongoDB\BSON\Binary;
use MongoDB\BSON\UTCDateTime;
use MongoDB\GridFS\Exception\FileNotFoundException;
use MongoDB\GridFS\Exception\LogicException;
use MongoDB\GridFS\StreamWrapper;

use function copy;
use function fclose;
use function feof;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function filesize;
use function filetype;
use function fopen;
use function fread;
use function fseek;
use function fstat;
use function fwrite;
use function is_dir;
use function is_file;
use function is_link;
use function rename;
use function stream_context_create;
use function stream_get_contents;
use function time;
use function unlink;
use function usleep;

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
            ['_id' => 1, 'files_id' => 'length-10', 'n' => 0, 'data' => new Binary('abcd')],
            ['_id' => 2, 'files_id' => 'length-10', 'n' => 1, 'data' => new Binary('efgh')],
            ['_id' => 3, 'files_id' => 'length-10', 'n' => 2, 'data' => new Binary('ij')],
        ]);
    }

    public function tearDown(): void
    {
        StreamWrapper::setContextResolver('bucket', null);

        parent::tearDown();
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
        $this->assertSame(1_484_202_200, $stat[9]);
        $this->assertSame(1_484_202_200, $stat['mtime']);
        $this->assertSame(1_484_202_200, $stat[10]);
        $this->assertSame(1_484_202_200, $stat['ctime']);
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
        $this->assertSame(1_484_202_200, $stat[9]);
        $this->assertSame(1_484_202_200, $stat['mtime']);
        $this->assertSame(1_484_202_200, $stat[10]);
        $this->assertSame(1_484_202_200, $stat['ctime']);
        $this->assertSame(4, $stat[11]);
        $this->assertSame(4, $stat['blksize']);
    }

    public function testWritableStreamWrite(): void
    {
        $stream = $this->bucket->openUploadStream('filename');

        $this->assertSame(6, fwrite($stream, 'foobar'));
    }

    /** @dataProvider provideUrl */
    public function testStreamWithContextResolver(string $url, string $expectedFilename): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');

        $stream = fopen($url, 'wb');

        $this->assertSame(6, fwrite($stream, 'foobar'));
        $this->assertTrue(fclose($stream));

        $file = $this->filesCollection->findOne(['filename' => $expectedFilename]);
        $this->assertNotNull($file);

        $stream = fopen($url, 'rb');

        $this->assertSame('foobar', fread($stream, 10));
        $this->assertTrue(fclose($stream));
    }

    public static function provideUrl()
    {
        yield 'simple file' => ['gridfs://bucket/filename', 'filename'];
        yield 'subdirectory file' => ['gridfs://bucket/path/to/filename.txt', 'path/to/filename.txt'];
        yield 'question mark can be used in file name' => ['gridfs://bucket/file%20name?foo=bar', 'file%20name?foo=bar'];
    }

    public function testFilePutAndGetContents(): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');

        $filename = 'gridfs://bucket/path/to/filename';

        $this->assertSame(6, file_put_contents($filename, 'foobar'));

        $file = $this->filesCollection->findOne(['filename' => 'path/to/filename']);
        $this->assertNotNull($file);

        $this->assertSame('foobar', file_get_contents($filename));
    }

    public function testEmptyFilename(): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');

        $filename = 'gridfs://bucket';

        $this->assertSame(6, file_put_contents($filename, 'foobar'));

        $file = $this->filesCollection->findOne(['filename' => '']);
        $this->assertNotNull($file);

        $this->assertSame('foobar', file_get_contents($filename));
    }

    public function testOpenSpecificRevision(): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');

        $filename = 'gridfs://bucket/path/to/filename';

        // Insert 3 revisions, wait 1ms between each to ensure they have different uploadDate
        file_put_contents($filename, 'version 0');
        usleep(1000);
        file_put_contents($filename, 'version 1');
        usleep(1000);
        file_put_contents($filename, 'version 2');

        $context = stream_context_create([
            'gridfs' => ['revision' => -2],
        ]);
        $stream = fopen($filename, 'r', false, $context);
        $this->assertSame('version 1', stream_get_contents($stream));
        fclose($stream);

        // Revision not existing
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File with name "path/to/filename" and revision "10" not found in "gridfs://bucket/path/to/filename"');
        $context = stream_context_create([
            'gridfs' => ['revision' => 10],
        ]);
        fopen($filename, 'r', false, $context);
    }

    public function testFileNoFoundWithContextResolver(): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File with name "filename" and revision "-1" not found in "gridfs://bucket/filename"');

        fopen('gridfs://bucket/filename', 'r');
    }

    public function testFileNoFoundWithoutDefaultResolver(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('GridFS stream wrapper has no bucket alias: "bucket"');

        fopen('gridfs://bucket/filename', 'w');
    }

    public function testFileStats(): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');
        $path = 'gridfs://bucket/filename';

        $this->assertFalse(file_exists($path));
        $this->assertFalse(is_file($path));

        $time = time();
        $this->assertSame(6, file_put_contents($path, 'foobar'));

        $this->assertTrue(file_exists($path));
        $this->assertSame('file', filetype($path));
        $this->assertTrue(is_file($path));
        $this->assertFalse(is_dir($path));
        $this->assertFalse(is_link($path));
        $this->assertSame(6, filesize($path));
        $this->assertGreaterThanOrEqual($time, filemtime($path));
        $this->assertLessThanOrEqual(time(), filemtime($path));
    }

    public function testCopy(): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');
        $path = 'gridfs://bucket/filename';

        $this->assertSame(6, file_put_contents($path, 'foobar'));

        copy($path, $path . '.copy');
        $this->assertSame('foobar', file_get_contents($path . '.copy'));
        $this->assertSame('foobar', file_get_contents($path));
    }

    public function testRenameAllRevisions(): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');
        $path = 'gridfs://bucket/filename';

        $this->assertSame(6, file_put_contents($path, 'foobar'));
        $this->assertSame(6, file_put_contents($path, 'foobar'));
        $this->assertSame(6, file_put_contents($path, 'foobar'));

        $result = rename($path, $path . '.renamed');
        $this->assertTrue($result);
        $this->assertTrue(file_exists($path . '.renamed'));
        $this->assertFalse(file_exists($path));
        $this->assertSame('foobar', file_get_contents($path . '.renamed'));

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File with name "gridfs://bucket/filename" not found');
        rename($path, $path . '.renamed');
    }

    public function testRenameSameFilename(): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');
        $path = 'gridfs://bucket/filename';

        $this->assertSame(6, file_put_contents($path, 'foobar'));

        $result = rename($path, $path);
        $this->assertTrue($result);
        $this->assertTrue(file_exists($path));
        $this->assertSame('foobar', file_get_contents($path));

        $path = 'gridfs://bucket/missing';
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File with name "gridfs://bucket/missing" not found');
        rename($path, $path);
    }

    public function testRenamePathMismatch(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot rename "gridfs://bucket/filename" to "gridfs://other/newname" because they are not in the same GridFS bucket.');

        rename('gridfs://bucket/filename', 'gridfs://other/newname');
    }

    public function testUnlinkAllRevisions(): void
    {
        $this->bucket->registerGlobalStreamWrapperAlias('bucket');
        $path = 'gridfs://bucket/path/to/filename';

        file_put_contents($path, 'version 0');
        file_put_contents($path, 'version 1');

        $result = unlink($path);

        $this->assertTrue($result);
        $this->assertFalse(file_exists($path));

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File with name "gridfs://bucket/path/to/filename" not found');
        unlink($path);
    }
}
