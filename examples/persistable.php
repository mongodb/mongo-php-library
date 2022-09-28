<?php
declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Persistable;
use MongoDB\Client;
use MongoDB\Model\BSONArray;
use UnexpectedValueException;

use function array_search;
use function dirname;
use function getenv;
use function var_dump;

require dirname(__FILE__) . '/../vendor/autoload.php';

class PersistableEntry implements Persistable
{
    /** @var ObjectId */
    private $id;

    /** @var string */
    private $name;

    /** @var array<PersistableEmail> */
    private $emails = [];

    public function __construct(string $name)
    {
        $this->id = new ObjectId();
        $this->name = $name;
    }

    public function getId(): ObjectId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function addEmail(PersistableEmail $email): void
    {
        $this->emails[] = $email;
    }

    public function deleteEmail(PersistableEmail $email): void
    {
        $index = array_search($email, $this->emails, true);
        if ($index === false) {
            return;
        }

        unset($this->emails[$index]);
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
    private $type;

    /** @var string */
    private $address;

    public function __construct(string $type, string $address)
    {
        $this->type = $type;
        $this->address = $address;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAddress(): string
    {
        return $this->address;
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
$entry->addEmail(new PersistableEmail('work', 'alcaeus@example.com'));
$entry->addEmail(new PersistableEmail('private', 'secret@example.com'));

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$collection = $client->test->coll;
$collection->drop();

$collection->insertOne($entry);

$foundEntry = $collection->findOne([]);

/** @psalm-suppress ForbiddenCode */
var_dump($foundEntry);
