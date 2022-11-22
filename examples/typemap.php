<?php
declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Unserializable;
use MongoDB\Client;
use UnexpectedValueException;

use function getenv;
use function is_array;
use function var_dump;

require __DIR__ . '/../vendor/autoload.php';

class TypeMapEntry implements Unserializable
{
    /** @var ObjectId */
    private $id;

    /** @var string */
    private $name;

    /** @var array<TypeMapEmail> */
    private $emails;

    private function __construct()
    {
    }

    public function getId(): ObjectId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function bsonUnserialize(array $data): void
    {
        if (! $data['_id'] instanceof ObjectId) {
            throw new UnexpectedValueException('_id field is not of the expected type');
        }

        if (! is_array($data['emails'])) {
            throw new UnexpectedValueException('emails field is not of the expected type');
        }

        $this->id = $data['_id'];
        $this->name = (string) $data['name'];

        /** @psalm-suppress MixedPropertyTypeCoercion */
        $this->emails = $data['emails'];
    }
}

class TypeMapEmail implements Unserializable
{
    /** @var string */
    private $type;

    /** @var string */
    private $address;

    private function __construct()
    {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function bsonUnserialize(array $data): void
    {
        $this->type = (string) $data['type'];
        $this->address = (string) $data['address'];
    }
}

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$collection = $client->test->typemap;
$collection->drop();

$document = [
    'name' => 'alcaeus',
    'emails' => [
        ['type' => 'work', 'address' => 'alcaeus@example.com'],
        ['type' => 'private', 'address' => 'secret@example.com'],
    ],
];

$collection->insertOne($document);

$typeMap = [
    'root' => TypeMapEntry::class, // Root object will be an Entry instance
    'fieldPaths' => [
        'emails' => 'array', // Emails field is used as PHP array
        'emails.$' => TypeMapEmail::class, // Each element in the emails array will be an Email instance
    ],
];

$entry = $collection->findOne([], ['typeMap' => $typeMap]);

/** @psalm-suppress ForbiddenCode */
var_dump($entry);
