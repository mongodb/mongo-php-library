<?php

namespace MongoDB\Model;

use Composer\InstalledVersions;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Client;
use MongoDB\Codec\Encoder;
use MongoDB\Driver\Manager;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;
use Throwable;

use function array_diff_key;
use function array_filter;
use function is_array;
use function is_string;
use function sprintf;
use function trim;

/** @internal */
final class DriverOptions
{
    private const KEY_TYPE_MAP = 'typeMap';
    private const KEY_BUILDER_ENCODER = 'builderEncoder';
    private const KEY_AUTO_ENCRYPTION = 'autoEncryption';
    private const KEY_DRIVER = 'driver';
    private const DEFAULT_TYPE_MAP = [
        'array' => BSONArray::class,
        'document' => BSONDocument::class,
        'root' => BSONDocument::class,
    ];

    private const HANDSHAKE_SEPARATOR = '/';

    private static ?string $version = null;

    public array $driver;

    /**
     * @param array|null                                                $autoEncryption
     * @param array{name?: string, version?: string, platform?: string} $driver
     */
    private function __construct(
        public readonly array $typeMap,
        public readonly Encoder $builderEncoder,
        public readonly ?array $autoEncryption,
        private readonly array $miscOptions,
        array $driver,
    ) {
        $this->driver = $this->mergeDriverInfo($driver);
    }

    public static function fromArray(array $options): self
    {
        $options += [self::KEY_TYPE_MAP => self::DEFAULT_TYPE_MAP];

        if (! is_array($options[self::KEY_TYPE_MAP])) {
            throw InvalidArgumentException::invalidType(
                sprintf('"%s" driver option', self::KEY_TYPE_MAP),
                $options[self::KEY_TYPE_MAP],
                'array',
            );
        }

        if (isset($options[self::KEY_BUILDER_ENCODER]) && ! $options[self::KEY_BUILDER_ENCODER] instanceof Encoder) {
            throw InvalidArgumentException::invalidType(
                sprintf('"%s" option', self::KEY_BUILDER_ENCODER),
                $options[self::KEY_BUILDER_ENCODER],
                Encoder::class,
            );
        }

        /** @var array{kmsProviders?: stdClass|array<string, array>, keyVaultClient?: Client|Manager} $autoEncryptionOptions */
        $autoEncryptionOptions = $options[self::KEY_AUTO_ENCRYPTION] ?? [];
        $autoEncryption = ! empty($autoEncryptionOptions)
            ? AutoEncryptionOptions::fromArray($autoEncryptionOptions)->toArray()
            : null;

        /** @var array{name?: string, version?: string, platform?: string} $driver $driver */
        $driver = $options[self::KEY_DRIVER] ?? [];

        return new self(
            typeMap: $options[self::KEY_TYPE_MAP],
            builderEncoder: $options[self::KEY_BUILDER_ENCODER] ?? new BuilderEncoder(),
            autoEncryption: $autoEncryption,
            miscOptions: array_diff_key($options, [
                self::KEY_TYPE_MAP => 1,
                self::KEY_BUILDER_ENCODER => 1,
                self::KEY_AUTO_ENCRYPTION => 1,
                self::KEY_DRIVER => 1,
            ]),
            driver: $driver,
        );
    }

    public function isAutoEncryptionEnabled(): bool
    {
        return isset($this->autoEncryption['keyVaultNamespace']);
    }

    public function toArray(): array
    {
        return array_filter(
            [
                'typeMap' => $this->typeMap,
                'builderEncoder' => $this->builderEncoder,
                'autoEncryption' => $this->autoEncryption,
                'driver' => $this->driver,
            ] + $this->miscOptions,
            static fn ($option) => $option !== null,
        );
    }

    private static function getVersion(): string
    {
        if (self::$version === null) {
            try {
                self::$version = InstalledVersions::getPrettyVersion('mongodb/mongodb') ?? 'unknown';
            } catch (Throwable) {
                self::$version = 'error';
            }
        }

        return self::$version;
    }

    /** @param array{name?: string, version?: string, platform?: string} $driver */
    private function mergeDriverInfo(array $driver): array
    {
        if (isset($driver['name'])) {
            if (! is_string($driver['name'])) {
                throw InvalidArgumentException::invalidType(
                    '"name" handshake option',
                    $driver['name'],
                    'string',
                );
            }
        }

        if (isset($driver['version'])) {
            if (! is_string($driver['version'])) {
                throw InvalidArgumentException::invalidType('"version" handshake option', $driver['version'], 'string');
            }
        }

        $mergedDriver = [
            'name' => 'PHPLIB',
            'version' => self::getVersion(),
        ];

        if (isset($driver['name'])) {
            $mergedDriver['name'] .= self::HANDSHAKE_SEPARATOR . $driver['name'];
        }

        if (isset($driver['version'])) {
            $mergedDriver['version'] .= self::HANDSHAKE_SEPARATOR . $driver['version'];
        }

        if ($this->isAutoEncryptionEnabled()) {
            $driver['platform'] = trim(sprintf('iue %s', $driver['platform'] ?? ''));
        }

        if (isset($driver['platform'])) {
            $mergedDriver['platform'] = $driver['platform'];
        }

        return $mergedDriver;
    }
}
