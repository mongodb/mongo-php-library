<?php

namespace MongoDB\Tests;

use MongoDB\Client;
use MongoDB\Codec\Encoder;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\InvalidArgumentException as DriverInvalidArgumentException;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;

/**
 * Unit tests for the Client class.
 */
class ClientTest extends TestCase
{
    public function testConstructorDefaultUri(): void
    {
        $client = new Client();

        $this->assertEquals('mongodb://127.0.0.1/', (string) $client);
    }

    #[DoesNotPerformAssertions]
    public function testConstructorAutoEncryptionOpts(): void
    {
        $autoEncryptionOpts = [
            'keyVaultClient' => new Client(static::getUri()),
            'keyVaultNamespace' => 'default.keys',
            'kmsProviders' => ['aws' => ['accessKeyId' => 'abc', 'secretAccessKey' => 'def']],
        ];

        new Client(static::getUri(), [], ['autoEncryption' => $autoEncryptionOpts]);
    }

    #[DataProvider('provideInvalidConstructorDriverOptions')]
    public function testConstructorDriverOptionTypeChecks(array $driverOptions, string $exception = InvalidArgumentException::class): void
    {
        $this->expectException($exception);
        new Client(static::getUri(), [], $driverOptions);
    }

    public static function provideInvalidConstructorDriverOptions()
    {
        $options = [];

        foreach (self::getInvalidObjectValues() as $value) {
            $options[][] = ['builderEncoder' => $value];
        }

        foreach (self::getInvalidArrayValues(true) as $value) {
            $options[][] = ['typeMap' => $value];
        }

        $options[][] = ['autoEncryption' => ['keyVaultClient' => 'foo']];

        foreach (self::getInvalidStringValues() as $value) {
            $options[][] = ['driver' => ['name' => $value]];
        }

        foreach (self::getInvalidStringValues() as $value) {
            $options[][] = ['driver' => ['version' => $value]];
        }

        foreach (self::getInvalidStringValues() as $value) {
            $options[] = [
                'driverOptions' => ['driver' => ['platform' => $value]],
                'exception' => DriverInvalidArgumentException::class,
            ];
        }

        return $options;
    }

    public function testToString(): void
    {
        $client = new Client(static::getUri());

        $this->assertSame(static::getUri(), (string) $client);
    }

    public function testSelectCollectionInheritsOptions(): void
    {
        $uriOptions = [
            'readConcernLevel' => ReadConcern::LOCAL,
            'readPreference' => 'secondaryPreferred',
            'w' => WriteConcern::MAJORITY,
        ];

        $driverOptions = [
            'builderEncoder' => $builderEncoder = $this->createMock(Encoder::class),
            'typeMap' => ['root' => 'array'],
        ];

        $client = new Client(static::getUri(), $uriOptions, $driverOptions);
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $debug = $collection->__debugInfo();

        $this->assertSame($builderEncoder, $debug['builderEncoder']);
        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::SECONDARY_PREFERRED, $debug['readPreference']->getModeString());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testSelectCollectionPassesOptions(): void
    {
        $collectionOptions = [
            'builderEncoder' => $builderEncoder = $this->createMock(Encoder::class),
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $client = new Client(static::getUri());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName(), $collectionOptions);
        $debug = $collection->__debugInfo();

        $this->assertSame($builderEncoder, $debug['builderEncoder']);
        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::SECONDARY_PREFERRED, $debug['readPreference']->getModeString());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testGetSelectsDatabaseAndInheritsOptions(): void
    {
        $uriOptions = ['w' => WriteConcern::MAJORITY];

        $driverOptions = [
            'builderEncoder' => $builderEncoder = $this->createMock(Encoder::class),
            'typeMap' => ['root' => 'array'],
        ];

        $client = new Client(static::getUri(), $uriOptions, $driverOptions);
        $database = $client->{$this->getDatabaseName()};
        $debug = $database->__debugInfo();

        $this->assertSame($builderEncoder, $debug['builderEncoder']);
        $this->assertSame($this->getDatabaseName(), $debug['databaseName']);
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testSelectDatabaseInheritsOptions(): void
    {
        $uriOptions = [
            'readConcernLevel' => ReadConcern::LOCAL,
            'readPreference' => ReadPreference::SECONDARY_PREFERRED,
            'w' => WriteConcern::MAJORITY,
        ];

        $driverOptions = [
            'builderEncoder' => $builderEncoder = $this->createMock(Encoder::class),
            'typeMap' => ['root' => 'array'],
        ];

        $client = new Client(static::getUri(), $uriOptions, $driverOptions);
        $database = $client->selectDatabase($this->getDatabaseName());
        $debug = $database->__debugInfo();

        $this->assertSame($builderEncoder, $debug['builderEncoder']);
        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::SECONDARY_PREFERRED, $debug['readPreference']->getModeString());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testSelectDatabasePassesOptions(): void
    {
        $databaseOptions = [
            'builderEncoder' => $builderEncoder = $this->createMock(Encoder::class),
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $client = new Client(static::getUri());
        $database = $client->selectDatabase($this->getDatabaseName(), $databaseOptions);
        $debug = $database->__debugInfo();

        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::SECONDARY_PREFERRED, $debug['readPreference']->getModeString());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testCreateClientEncryption(): void
    {
        $client = new Client(static::getUri());

        $options = [
            'keyVaultNamespace' => 'default.keys',
            'kmsProviders' => ['aws' => ['accessKeyId' => 'abc', 'secretAccessKey' => 'def']],
        ];

        $clientEncryption = $client->createClientEncryption($options);
        $this->assertInstanceOf(ClientEncryption::class, $clientEncryption);
    }

    public function testCreateClientEncryptionWithKeyVaultClient(): void
    {
        $client = new Client(static::getUri());

        $options = [
            'keyVaultClient' => $client,
            'keyVaultNamespace' => 'default.keys',
            'kmsProviders' => ['aws' => ['accessKeyId' => 'abc', 'secretAccessKey' => 'def']],
        ];

        $clientEncryption = $client->createClientEncryption($options);
        $this->assertInstanceOf(ClientEncryption::class, $clientEncryption);
    }

    public function testCreateClientEncryptionWithManager(): void
    {
        $client = new Client(static::getUri());

        $options = [
            'keyVaultClient' => $client->getManager(),
            'keyVaultNamespace' => 'default.keys',
            'kmsProviders' => ['aws' => ['accessKeyId' => 'abc', 'secretAccessKey' => 'def']],
        ];

        $clientEncryption = $client->createClientEncryption($options);
        $this->assertInstanceOf(ClientEncryption::class, $clientEncryption);
    }

    public function testCreateClientEncryptionWithInvalidKeyVaultClient(): void
    {
        $client = new Client(static::getUri());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "keyVaultClient" option to have type "MongoDB\Client" or "MongoDB\Driver\Manager" but found "string"');

        $client->createClientEncryption(['keyVaultClient' => 'foo']);
    }
}
