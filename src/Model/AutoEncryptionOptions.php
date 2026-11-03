<?php

namespace MongoDB\Model;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\Driver\Manager;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_filter;
use function is_array;
use function sprintf;

/**
 * KmsProvidersShape keys are a KMS provider name optionally suffixed with ":<name>" to define
 * several sets of credentials for the same provider (e.g. "aws:name2"). TlsOptionsShape keys
 * follow the same convention.
 *
 * @phpstan-type KmsProvidersShape = non-empty-array<string,
 *     array{key: string|Binary}
 *     |array{accessKeyId?: string, secretAccessKey?: string, sessionToken?: string}
 *     |array{tenantId?: string, clientId?: string, clientSecret?: string, identityPlatformEndpoint?: string}
 *     |array{email?: string, privateKey?: string, endpoint?: string}
 *     |array{endpoint?: string}
 *     |stdClass
 * >
 * @phpstan-type ExtraOptionsShape = array{
 *     cryptSharedLibPath?: string,
 *     cryptSharedLibSearchPaths?: list<string>,
 *     cryptSharedLibRequired?: bool,
 *     mongocryptdSpawnPath?: string,
 *     mongocryptdSpawnArgs?: list<string>,
 *     mongocryptdURI?: string,
 *     mongocryptdBypassSpawn?: bool,
 * }
 * @phpstan-type TlsOptionsShape = array<string, array{
 *     tlsCAFile?: string,
 *     tlsCertificateKeyFile?: string,
 *     tlsCertificateKeyFilePassword?: string,
 *     tlsDisableOCSPEndpointCheck?: bool,
 * }>
 * @phpstan-type EncryptedFieldShape = array{
 *     path: string,
 *     bsonType: string,
 *     keyId?: Binary|null,
 *     keyAltName?: string,
 *     queries?: list<array{
 *         queryType: 'equality'|'range',
 *         contention?: int,
 *         min?: int|float|Int64|Decimal128|UTCDateTime,
 *         max?: int|float|Int64|Decimal128|UTCDateTime,
 *         sparsity?: int,
 *         trimFactor?: int,
 *         precision?: int,
 *     }>,
 * }
 * @phpstan-type EncryptedFieldsShape = array{
 *     fields: list<EncryptedFieldShape>,
 *     escCollection?: string,
 *     ecocCollection?: string,
 * }
 * @phpstan-type AutoEncryptionOptionsShape = array{
 *     keyVaultNamespace?: string,
 *     kmsProviders?: KmsProvidersShape|stdClass,
 *     schemaMap?: array<string, array<string, mixed>>,
 *     encryptedFieldsMap?: array<string, EncryptedFieldsShape>,
 *     extraOptions?: ExtraOptionsShape,
 *     tlsOptions?: TlsOptionsShape,
 *     keyVaultClient?: Client|Manager,
 *     bypassAutoEncryption?: bool,
 *     bypassQueryAnalysis?: bool,
 * }
 * @psalm-type KmsProvidersShape = non-empty-array<string,
 *     array{key: string|Binary}
 *     |array{accessKeyId?: string, secretAccessKey?: string, sessionToken?: string}
 *     |array{tenantId?: string, clientId?: string, clientSecret?: string, identityPlatformEndpoint?: string}
 *     |array{email?: string, privateKey?: string, endpoint?: string}
 *     |array{endpoint?: string}
 *     |stdClass
 * >
 * @psalm-type ExtraOptionsShape = array{
 *     cryptSharedLibPath?: string,
 *     cryptSharedLibSearchPaths?: list<string>,
 *     cryptSharedLibRequired?: bool,
 *     mongocryptdSpawnPath?: string,
 *     mongocryptdSpawnArgs?: list<string>,
 *     mongocryptdURI?: string,
 *     mongocryptdBypassSpawn?: bool,
 * }
 * @psalm-type TlsOptionsShape = array<string, array{
 *     tlsCAFile?: string,
 *     tlsCertificateKeyFile?: string,
 *     tlsCertificateKeyFilePassword?: string,
 *     tlsDisableOCSPEndpointCheck?: bool,
 * }>
 * @psalm-type EncryptedFieldShape = array{
 *     path: string,
 *     bsonType: string,
 *     keyId?: Binary|null,
 *     keyAltName?: string,
 *     queries?: list<array{
 *         queryType: 'equality'|'range',
 *         contention?: int,
 *         min?: int|float|Int64|Decimal128|UTCDateTime,
 *         max?: int|float|Int64|Decimal128|UTCDateTime,
 *         sparsity?: int,
 *         trimFactor?: int,
 *         precision?: int,
 *     }>,
 * }
 * @psalm-type EncryptedFieldsShape = array{
 *     fields: list<EncryptedFieldShape>,
 *     escCollection?: string,
 *     ecocCollection?: string,
 * }
 * @psalm-type AutoEncryptionOptionsShape = array{
 *     keyVaultNamespace?: string,
 *     kmsProviders?: KmsProvidersShape|stdClass,
 *     schemaMap?: array<string, array<string, mixed>>,
 *     encryptedFieldsMap?: array<string, EncryptedFieldsShape>,
 *     extraOptions?: ExtraOptionsShape,
 *     tlsOptions?: TlsOptionsShape,
 *     keyVaultClient?: Client|Manager,
 *     bypassAutoEncryption?: bool,
 *     bypassQueryAnalysis?: bool,
 * }
 * @internal
 */
final class AutoEncryptionOptions
{
    private const KEY_KEY_VAULT_CLIENT = 'keyVaultClient';
    private const KEY_KMS_PROVIDERS = 'kmsProviders';

    private function __construct(
        private readonly ?Manager $keyVaultClient,
        private readonly array|stdClass|null $kmsProviders,
        private readonly array $miscOptions,
    ) {
    }

    /** @param AutoEncryptionOptionsShape $options */
    public static function fromArray(array $options): self
    {
        $kmsProviders = $options[self::KEY_KMS_PROVIDERS] ?? null;
        $keyVaultClient = $options[self::KEY_KEY_VAULT_CLIENT] ?? null;

        unset($options[self::KEY_KMS_PROVIDERS], $options[self::KEY_KEY_VAULT_CLIENT]);

        // The server requires an empty document for automatic credentials.
        if (is_array($kmsProviders)) {
            foreach ($kmsProviders as $name => $provider) {
                if ($provider === []) {
                    $kmsProviders[$name] = new stdClass();
                }
            }
        }

        if ($keyVaultClient !== null && ! $keyVaultClient instanceof Client && ! $keyVaultClient instanceof Manager) {
            throw InvalidArgumentException::invalidType(
                sprintf('"%s" option', self::KEY_KEY_VAULT_CLIENT),
                $keyVaultClient,
                [Client::class, Manager::class],
            );
        }

        return new self(
            keyVaultClient: $keyVaultClient instanceof Client ? $keyVaultClient->getManager() : $keyVaultClient,
            kmsProviders: $kmsProviders,
            miscOptions: $options,
        );
    }

    public function toArray(): array
    {
        return array_filter(
            [
                self::KEY_KEY_VAULT_CLIENT => $this->keyVaultClient,
                self::KEY_KMS_PROVIDERS => $this->kmsProviders,
            ] + $this->miscOptions,
            static fn ($option) => $option !== null,
        );
    }
}
