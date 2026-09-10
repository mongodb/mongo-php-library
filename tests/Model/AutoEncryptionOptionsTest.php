<?php

namespace MongoDB\Tests\Model;

use Generator;
use MongoDB\Client;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\AutoEncryptionOptions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class AutoEncryptionOptionsTest extends TestCase
{
    #[DataProvider('fromArrayProvider')]
    public function testFromArray(array $options, array $expected): void
    {
        $actual = AutoEncryptionOptions::fromArray($options);
        $this->assertEquals($expected, $actual->toArray());
    }

    public function testFromArrayFailsForInvalidOptions(): void
    {
        $this->expectException(InvalidArgumentException::class);

        AutoEncryptionOptions::fromArray([
            'keyVaultClient' => new stdClass(),
        ]);
    }

    public static function fromArrayProvider(): Generator
    {
        $client = new Client();

        yield 'with manager passed for `keyVaultClient`' => [
            [
                'keyVaultClient' => $client->getManager(),
                'kmsProviders' => new stdClass(),
            ],
            [
                'keyVaultClient' => $client->getManager(),
                'kmsProviders' => new stdClass(),
            ],
        ];

        yield 'with client passed for `keyVaultClient`' => [
            ['keyVaultClient' => $client],
            [
                'keyVaultClient' => $client->getManager(),
            ],
        ];

        yield 'with extra options' => [
            [
                'kmsProviders' => [
                    'foo' => [],
                    'aws' => ['foo' => 'bar'],
                ],
                'tlsProviders' => [
                    ['foo' => 'bar'],
                ],
                'disableClientPersistence' => false,
            ],
            [
                'kmsProviders' => [
                    'foo' => new stdClass(),
                    'aws' => ['foo' => 'bar'],
                ],
                'tlsProviders' => [
                    ['foo' => 'bar'],
                ],
                'disableClientPersistence' => false,
            ],
        ];
    }
}
