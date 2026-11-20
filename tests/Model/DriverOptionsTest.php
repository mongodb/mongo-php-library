<?php

namespace MongoDB\Tests\Model;

use Generator;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\DriverOptions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class DriverOptionsTest extends TestCase
{
    #[DataProvider('provideOptions')]
    public function testFromArray(array $options, array $expected): void
    {
        $actual = DriverOptions::fromArray($options);

        $actualArray = $actual->toArray();
        // This changes per runtime, so is tested with regex separately in `testDriverInfo`
        unset($actualArray['driver']['version']);

        $this->assertEquals($expected, $actualArray);
    }

    #[DataProvider('provideInvalidOptions')]
    public function testFromArrayFailsForInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        DriverOptions::fromArray($options);
    }

    public function testIsAutoEncryptionEnabled(): void
    {
        $enabled = DriverOptions::fromArray([
            'kmsProviders' => [
                'foo' => new StdClass(),
                'aws' => ['foo' => 'bar'],
            ],
            'autoEncryption' => ['keyVaultNamespace' => 'foo'],
        ]);

        $this->assertTrue($enabled->isAutoEncryptionEnabled());

        $notEnabled = DriverOptions::fromArray([
            'autoEncryption' => [
                'kmsProviders' => [
                    'foo' => new StdClass(),
                    'aws' => ['foo' => 'bar'],
                ],
            ],
        ]);

        $this->assertFalse($notEnabled->isAutoEncryptionEnabled());
    }

    #[DataProvider('provideDriverInfo')]
    public function testDriverInfo(array $options, string $name, string $versionRegex, ?string $platform): void
    {
        $options = DriverOptions::fromArray(['driver' => $options]);

        $this->assertEquals($name, $options->driver['name']);
        $this->assertMatchesRegularExpression($versionRegex, $options->driver['version']);
        $this->assertEquals($platform, $options->driver['platform']);
    }

    public static function provideOptions(): Generator
    {
        yield 'defaults' => [
            [],
            [
                'typeMap' => [
                    'array' => BSONArray::class,
                    'document' => BSONDocument::class,
                    'root' => BSONDocument::class,
                ],
                'builderEncoder' => new BuilderEncoder(),
                'driver' => ['name' => 'PHPLIB'],
            ],
        ];

        yield 'encryption enabled' => [
            [
                'autoEncryption' => ['keyVaultNamespace' => 'foo'],
            ],
            [
                'typeMap' => [
                    'array' => BSONArray::class,
                    'document' => BSONDocument::class,
                    'root' => BSONDocument::class,
                ],
                'autoEncryption' => ['keyVaultNamespace' => 'foo'],
                'builderEncoder' => new BuilderEncoder(),
                'driver' => [
                    'name' => 'PHPLIB',
                    'platform' => 'iue',
                ],
            ],
        ];

        yield 'encryption enabled with platform' => [
            [
                'autoEncryption' => ['keyVaultNamespace' => 'foo'],
                'driver' => ['platform' => 'bar'],
            ],
            [
                'typeMap' => [
                    'array' => BSONArray::class,
                    'document' => BSONDocument::class,
                    'root' => BSONDocument::class,
                ],
                'autoEncryption' => ['keyVaultNamespace' => 'foo'],
                'builderEncoder' => new BuilderEncoder(),
                'driver' => [
                    'name' => 'PHPLIB',
                    'platform' => 'iue bar',
                ],
            ],
        ];

        yield 'extra options' => [
            [
                'typeMap' => [],
                'builderEncoder' => new BuilderEncoder(),
                'autoEncryption' => [],
                'some' => 'option',
                'some_other' => ['option' => 'too'],
                'driver' => ['platform' => 'foo'],
            ],
            [
                'typeMap' => [],
                'builderEncoder' => new BuilderEncoder(),
                'driver' => [
                    'name' => 'PHPLIB',
                    'platform' => 'foo',
                ],
                'some' => 'option',
                'some_other' => ['option' => 'too'],
            ],
        ];
    }

    public static function provideInvalidOptions(): Generator
    {
        yield 'invalid type for type map' => [
            ['typeMap' => null],
        ];

        yield 'invalid type for builder encoder' => [
            [
                'builderEncoder' => new StdClass(),
            ],
        ];
    }

    public static function provideDriverInfo(): Generator
    {
        yield 'all' => [
            [
                'name' => 'foo',
                'version' => 'bar',
                'platform' => 'baz',
            ],
            'PHPLIB/foo',
            '/^.+\/bar$/',
            'baz',
        ];

        yield 'default' => [[], 'PHPLIB', '/.+/', null];
    }
}
