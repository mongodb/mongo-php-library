<?php
declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Persistable;
use MongoDB\Client;
use MongoDB\Model\BSONArray;
use UnexpectedValueException;

use function getenv;
use function var_dump;

require __DIR__ . '/../vendor/autoload.php';

class PersistableEntry implements Persistable
{
    /** @var ObjectId */
    private $id;

    /** @var string */
    public $name;

    /** @var array<PersistableEmail> */
    public $emails = [];

    public function __construct(string $name)
    {
        $this->id = new ObjectId();
        $this->name = $name;
    }

    public function getId(): ObjectId
    {
        return $this->id;
    }

    public function bsonSerialize(): object
    {
        return (object) [
            '_id' => $this->id,
            'name' => $this->name,
            'emails' => $this->emails,
        ];
    }

    public function bsonUnserialize(array $data): void
    {
        if (! $data['_id'] instanceof ObjectId) {
            throw new UnexpectedValueException('_id field is not of the expected type');
        }

        if (! $data['emails'] instanceof BSONArray) {
            throw new UnexpectedValueException('emails field is not of the expected type');
        }

        $this->id = $data['_id'];
        $this->name = (string) $data['name'];

        /** @psalm-suppress MixedPropertyTypeCoercion */
        $this->emails = $data['emails']->getArrayCopy(); // Emails will be passed as a BSONArray instance
    }
}

class PersistableEmail implements Persistable
{
    /** @var string */
    public $type;

    /** @var string */
    public $address;

    public function __construct(string $type, string $address)
    {
        $this->type = $type;
        $this->address = $address;
    }

    public function bsonSerialize(): object
    {
        return (object) [
            'type' => $this->type,
            'address' => $this->address,
        ];
    }

    public function bsonUnserialize(array $data): void
    {
        $this->type = (string) $data['type'];
        $this->address = (string) $data['address'];
    }
}

$entry = new PersistableEntry('alcaeus');
$entry->emails[] = new PersistableEmail('work', 'alcaeus@example.com');
$entry->emails[] = new PersistableEmail('private', 'secret@example.com');

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$collection = $client->test->persistable;
$collection->drop();

$collection->insertOne($entry);

$foundEntry = $collection->findOne([]);

/** @psalm-suppress ForbiddenCode */
var_dump($foundEntry);
