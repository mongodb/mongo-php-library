<?php
declare(strict_types=1);

namespace MongoDB\Examples\Persistable;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Persistable;
use MongoDB\Client;
use MongoDB\Model\BSONArray;
use stdClass;
use UnexpectedValueException;

use function getenv;
use function print_r;

require __DIR__ . '/../vendor/autoload.php';

class PersistableEntry implements Persistable
{
    private ObjectId $id;

    public string $name;

    /** @var array<PersistableEmail> */
    public array $emails = [];

    public function __construct(string $name)
    {
        $this->id = new ObjectId();
        $this->name = $name;
    }

    public function getId(): ObjectId
    {
        return $this->id;
    }

    public function bsonSerialize(): stdClass
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
    public string $type;

    public string $address;

    public function __construct(string $type, string $address)
    {
        $this->type = $type;
        $this->address = $address;
    }

    public function bsonSerialize(): stdClass
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

print_r($foundEntry);
