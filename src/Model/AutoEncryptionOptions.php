<?php

namespace MongoDB\Model;

use MongoDB\Client;
use MongoDB\Driver\Manager;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_diff_key;
use function array_filter;
use function is_array;
use function sprintf;

/** @internal */
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

    /** @param array{kmsProviders?: stdClass|array<string, array>, keyVaultClient?: Client|Manager} $options */
    public static function fromArray(array $options): self
    {
        // The server requires an empty document for automatic credentials.
        if (isset($options[self::KEY_KMS_PROVIDERS]) && is_array($options[self::KEY_KMS_PROVIDERS])) {
            foreach ($options[self::KEY_KMS_PROVIDERS] as $name => $provider) {
                if ($provider === []) {
                    $options[self::KEY_KMS_PROVIDERS][$name] = new stdClass();
                }
            }
        }

        $keyVaultClient = $options[self::KEY_KEY_VAULT_CLIENT] ?? null;

        if ($keyVaultClient !== null && ! $keyVaultClient instanceof Client && ! $keyVaultClient instanceof Manager) {
            throw InvalidArgumentException::invalidType(
                sprintf('"%s" option', self::KEY_KEY_VAULT_CLIENT),
                $keyVaultClient,
                [Client::class, Manager::class],
            );
        }

        return new self(
            keyVaultClient: $keyVaultClient instanceof Client ? $keyVaultClient->getManager() : $keyVaultClient,
            kmsProviders: $options[self::KEY_KMS_PROVIDERS] ?? null,
            miscOptions: array_diff_key($options, [self::KEY_KEY_VAULT_CLIENT => 1, self::KEY_KMS_PROVIDERS => 1]),
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
