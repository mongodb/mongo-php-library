<?php

namespace MongoDB\Tests;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use function in_array;
use function ob_end_clean;
use function ob_start;
use function var_dump;
use function version_compare;

/**
 * Documentation examples to be parsed for inclusion in the MongoDB manual.
 *
 * @see https://jira.mongodb.org/browse/DRIVERS-356
 * @see https://jira.mongodb.org/browse/DRIVERS-488
 * @see https://jira.mongodb.org/browse/DRIVERS-547
 */
class DocumentationExamplesTest extends FunctionalTestCase
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        parent::setUp();

        $this->dropCollection();
    }

    private function doTearDown()
    {
        if ($this->hasFailed()) {
            return;
        }

        $this->dropCollection();
    }

    public function testExample_1_2()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 1
        $insertOneResult = $db->inventory->insertOne([
            'item' => 'canvas',
            'qty' => 100,
            'tags' => ['cotton'],
            'size' => ['h' => 28, 'w' => 35.5, 'uom' => 'cm'],
        ]);
        // End Example 1

        $this->assertSame(1, $insertOneResult->getInsertedCount());
        $this->assertInstanceOf(ObjectId::class, $insertOneResult->getInsertedId());
        $this->assertInventoryCount(1);

        // Start Example 2
        $cursor = $db->inventory->find(['item' => 'canvas']);
        // End Example 2

        $this->assertCursorCount(1, $cursor);
    }

    public function testExample_3()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 3
        $insertManyResult = $db->inventory->insertMany([
            [
                'item' => 'journal',
                'qty' => 25,
                'tags' => ['blank', 'red'],
                'size' => ['h' => 14, 'w' => 21, 'uom' => 'cm'],
            ],
            [
                'item' => 'mat',
                'qty' => 85,
                'tags' => ['gray'],
                'size' => ['h' => 27.9, 'w' => 35.5, 'uom' => 'cm'],
            ],
            [
                'item' => 'mousepad',
                'qty' => 25,
                'tags' => ['gel', 'blue'],
                'size' => ['h' => 19, 'w' => 22.85, 'uom' => 'cm'],
            ],
        ]);
        // End Example 3

        $this->assertSame(3, $insertManyResult->getInsertedCount());
        foreach ($insertManyResult->getInsertedIds() as $id) {
            $this->assertInstanceOf(ObjectId::class, $id);
        }
        $this->assertInventoryCount(3);
    }

    public function testExample_6_13()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 6
        $insertManyResult = $db->inventory->insertMany([
            [
                'item' => 'journal',
                'qty' => 25,
                'size' => ['h' => 14, 'w' => 21, 'uom' => 'cm'],
                'status' => 'A',
            ],
            [
                'item' => 'notebook',
                'qty' => 50,
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'status' => 'A',
            ],
            [
                'item' => 'paper',
                'qty' => 100,
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'status' => 'D',
            ],
            [
                'item' => 'planner',
                'qty' => 75,
                'size' => ['h' => 22.85, 'w' => 30, 'uom' => 'cm'],
                'status' => 'D',
            ],
            [
                'item' => 'postcard',
                'qty' => 45,
                'size' => ['h' => 10, 'w' => 15.25, 'uom' => 'cm'],
                'status' => 'A',
            ],
        ]);
        // End Example 6

        $this->assertSame(5, $insertManyResult->getInsertedCount());
        foreach ($insertManyResult->getInsertedIds() as $id) {
            $this->assertInstanceOf(ObjectId::class, $id);
        }
        $this->assertInventoryCount(5);

        // Start Example 7
        $cursor = $db->inventory->find([]);
        // End Example 7

        $this->assertCursorCount(5, $cursor);

        // Start Example 8
        $cursor = $db->inventory->find();
        // End Example 8

        $this->assertCursorCount(5, $cursor);

        // Start Example 9
        $cursor = $db->inventory->find(['status' => 'D']);
        // End Example 9

        $this->assertCursorCount(2, $cursor);

        // Start Example 10
        $cursor = $db->inventory->find(['status' => ['$in' => ['A', 'D']]]);
        // End Example 10

        $this->assertCursorCount(5, $cursor);

        // Start Example 11
        $cursor = $db->inventory->find([
            'status' => 'A',
            'qty' => ['$lt' => 30],
        ]);
        // End Example 11

        $this->assertCursorCount(1, $cursor);

        // Start Example 12
        $cursor = $db->inventory->find([
            '$or' => [
                ['status' => 'A'],
                ['qty' => ['$lt' => 30]],
            ],
        ]);
        // End Example 12

        $this->assertCursorCount(3, $cursor);

        // Start Example 13
        $cursor = $db->inventory->find([
            'status' => 'A',
            '$or' => [
                ['qty' => ['$lt' => 30]],
                // Alternatively: ['item' => new \MongoDB\BSON\Regex('^p')]
                ['item' => ['$regex' => '^p']],
            ],
        ]);
        // End Example 13

        $this->assertCursorCount(2, $cursor);
    }

    public function testExample_14_19()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 14
        $insertManyResult = $db->inventory->insertMany([
            [
                'item' => 'journal',
                'qty' => 25,
                'size' => ['h' => 14, 'w' => 21, 'uom' => 'cm'],
                'status' => 'A',
            ],
            [
                'item' => 'notebook',
                'qty' => 50,
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'status' => 'A',
            ],
            [
                'item' => 'paper',
                'qty' => 100,
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'status' => 'D',
            ],
            [
                'item' => 'planner',
                'qty' => 75,
                'size' => ['h' => 22.85, 'w' => 30, 'uom' => 'cm'],
                'status' => 'D',
            ],
            [
                'item' => 'postcard',
                'qty' => 45,
                'size' => ['h' => 10, 'w' => 15.25, 'uom' => 'cm'],
                'status' => 'A',
            ],
        ]);
        // End Example 14

        $this->assertSame(5, $insertManyResult->getInsertedCount());
        foreach ($insertManyResult->getInsertedIds() as $id) {
            $this->assertInstanceOf(ObjectId::class, $id);
        }
        $this->assertInventoryCount(5);

        // Start Example 15
        $cursor = $db->inventory->find(['size' => ['h' => 14, 'w' => 21, 'uom' => 'cm']]);
        // End Example 15

        $this->assertCursorCount(1, $cursor);

        // Start Example 16
        $cursor = $db->inventory->find(['size' => ['w' => 21, 'h' => 14, 'uom' => 'cm']]);
        // End Example 16

        $this->assertCursorCount(0, $cursor);

        // Start Example 17
        $cursor = $db->inventory->find(['size.uom' => 'in']);
        // End Example 17

        $this->assertCursorCount(2, $cursor);

        // Start Example 18
        $cursor = $db->inventory->find(['size.h' => ['$lt' => 15]]);
        // End Example 18

        $this->assertCursorCount(4, $cursor);

        // Start Example 19
        $cursor = $db->inventory->find([
            'size.h' => ['$lt' => 15],
            'size.uom' => 'in',
            'status' => 'D',
        ]);
        // End Example 19

        $this->assertCursorCount(1, $cursor);
    }

    public function testExample_20_28()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 20
        $insertManyResult = $db->inventory->insertMany([
            [
                'item' => 'journal',
                'qty' => 25,
                'tags' => ['blank', 'red'],
                'dim_cm' => [14, 21],
            ],
            [
                'item' => 'notebook',
                'qty' => 50,
                'tags' => ['red', 'blank'],
                'dim_cm' => [14, 21],
            ],
            [
                'item' => 'paper',
                'qty' => 100,
                'tags' => ['red', 'blank', 'plain'],
                'dim_cm' => [14, 21],
            ],
            [
                'item' => 'planner',
                'qty' => 75,
                'tags' => ['blank', 'red'],
                'dim_cm' => [22.85, 30],
            ],
            [
                'item' => 'postcard',
                'qty' => 45,
                'tags' => ['blue'],
                'dim_cm' => [10, 15.25],
            ],
        ]);
        // End Example 20

        $this->assertSame(5, $insertManyResult->getInsertedCount());
        foreach ($insertManyResult->getInsertedIds() as $id) {
            $this->assertInstanceOf(ObjectId::class, $id);
        }
        $this->assertInventoryCount(5);

        // Start Example 21
        $cursor = $db->inventory->find(['tags' => ['red', 'blank']]);
        // End Example 21

        $this->assertCursorCount(1, $cursor);

        // Start Example 22
        $cursor = $db->inventory->find(['tags' => ['$all' => ['red', 'blank']]]);
        // End Example 22

        $this->assertCursorCount(4, $cursor);

        // Start Example 23
        $cursor = $db->inventory->find(['tags' => 'red']);
        // End Example 23

        $this->assertCursorCount(4, $cursor);

        // Start Example 24
        $cursor = $db->inventory->find(['dim_cm' => ['$gt' => 25]]);
        // End Example 24

        $this->assertCursorCount(1, $cursor);

        // Start Example 25
        $cursor = $db->inventory->find([
            'dim_cm' => [
                '$gt' => 15,
                '$lt' => 20,
            ],
        ]);
        // End Example 25

        $this->assertCursorCount(4, $cursor);

        // Start Example 26
        $cursor = $db->inventory->find([
            'dim_cm' => [
                '$elemMatch' => [
                    '$gt' => 22,
                    '$lt' => 30,
                ],
            ],
        ]);
        // End Example 26

        $this->assertCursorCount(1, $cursor);

        // Start Example 27
        $cursor = $db->inventory->find(['dim_cm.1' => ['$gt' => 25]]);
        // End Example 27

        $this->assertCursorCount(1, $cursor);

        // Start Example 28
        $cursor = $db->inventory->find(['tags' => ['$size' => 3]]);
        // End Example 28

        $this->assertCursorCount(1, $cursor);
    }

    public function testExample_29_37()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 29
        $insertManyResult = $db->inventory->insertMany([
            [
                'item' => 'journal',
                'instock' => [
                    ['warehouse' => 'A',  'qty' => 5],
                    ['warehouse' => 'C',  'qty' => 15],
                ],
            ],
            [
                'item' => 'notebook',
                'instock' => [
                    ['warehouse' => 'C',  'qty' => 5],
                ],
            ],
            [
                'item' => 'paper',
                'instock' => [
                    ['warehouse' => 'A',  'qty' => 60],
                    ['warehouse' => 'B',  'qty' => 15],
                ],
            ],
            [
                'item' => 'planner',
                'instock' => [
                    ['warehouse' => 'A',  'qty' => 40],
                    ['warehouse' => 'B',  'qty' => 5],
                ],
            ],
            [
                'item' => 'postcard',
                'instock' => [
                    ['warehouse' => 'B',  'qty' => 15],
                    ['warehouse' => 'C',  'qty' => 35],
                ],
            ],
        ]);
        // End Example 29

        $this->assertSame(5, $insertManyResult->getInsertedCount());
        foreach ($insertManyResult->getInsertedIds() as $id) {
            $this->assertInstanceOf(ObjectId::class, $id);
        }
        $this->assertInventoryCount(5);

        // Start Example 30
        $cursor = $db->inventory->find(['instock' => ['warehouse' => 'A', 'qty' => 5]]);
        // End Example 30

        $this->assertCursorCount(1, $cursor);

        // Start Example 31
        $cursor = $db->inventory->find(['instock' => ['qty' => 5, 'warehouse' => 'A']]);
        // End Example 31

        $this->assertCursorCount(0, $cursor);

        // Start Example 32
        $cursor = $db->inventory->find(['instock.0.qty' => ['$lte' => 20]]);
        // End Example 32

        $this->assertCursorCount(3, $cursor);

        // Start Example 33
        $cursor = $db->inventory->find(['instock.qty' => ['$lte' => 20]]);
        // End Example 33

        $this->assertCursorCount(5, $cursor);

        // Start Example 34
        $cursor = $db->inventory->find(['instock' => ['$elemMatch' => ['qty' => 5, 'warehouse' => 'A']]]);
        // End Example 34

        $this->assertCursorCount(1, $cursor);

        // Start Example 35
        $cursor = $db->inventory->find(['instock' => ['$elemMatch' => ['qty' => ['$gt' => 10, '$lte' => 20]]]]);
        // End Example 35

        $this->assertCursorCount(3, $cursor);

        // Start Example 36
        $cursor = $db->inventory->find(['instock.qty' => ['$gt' => 10, '$lte' => 20]]);
        // End Example 36

        $this->assertCursorCount(4, $cursor);

        // Start Example 37
        $cursor = $db->inventory->find(['instock.qty' => 5, 'instock.warehouse' => 'A']);
        // End Example 37

        $this->assertCursorCount(2, $cursor);
    }

    public function testExample_38_41()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 38
        $insertManyResult = $db->inventory->insertMany([
            ['_id' => 1, 'item' => null],
            ['_id' => 2],
        ]);
        // End Example 38

        $this->assertSame(2, $insertManyResult->getInsertedCount());
        foreach ($insertManyResult->getInsertedIds() as $id) {
            $this->assertIsInt($id);
        }
        $this->assertInventoryCount(2);

        // Start Example 39
        $cursor = $db->inventory->find(['item' => null]);
        // End Example 39

        $this->assertCursorCount(2, $cursor);

        // Start Example 40
        $cursor = $db->inventory->find(['item' => ['$type' => 10]]);
        // End Example 40

        $this->assertCursorCount(1, $cursor);

        // Start Example 41
        $cursor = $db->inventory->find(['item' => ['$exists' => false]]);
        // End Example 41

        $this->assertCursorCount(1, $cursor);
    }

    public function testExample_42_50()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 42
        $insertManyResult = $db->inventory->insertMany([
            [
                'item' => 'journal',
                'status' => 'A',
                'size' => ['h' => 14, 'w' => 21, 'uom' => 'cm'],
                'instock' => [
                    ['warehouse' => 'A', 'qty' => 5],
                ],
            ],
            [
                'item' => 'notebook',
                'status' => 'A',
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'instock' => [
                    ['warehouse' => 'C', 'qty' => 5],
                ],
            ],
            [
                'item' => 'paper',
                'status' => 'D',
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'instock' => [
                    ['warehouse' => 'A', 'qty' => 60],
                ],
            ],
            [
                'item' => 'planner',
                'status' => 'D',
                'size' => ['h' => 22.85, 'w' => 30, 'uom' => 'cm'],
                'instock' => [
                    ['warehouse' => 'A', 'qty' => 40],
                ],
            ],
            [
                'item' => 'postcard',
                'status' => 'A',
                'size' => ['h' => 10, 'w' => 15.25, 'uom' => 'cm'],
                'instock' => [
                    ['warehouse' => 'B', 'qty' => 15],
                    ['warehouse' => 'C', 'qty' => 35],
                ],
            ],
        ]);
        // End Example 42

        $this->assertSame(5, $insertManyResult->getInsertedCount());
        foreach ($insertManyResult->getInsertedIds() as $id) {
            $this->assertInstanceOf(ObjectId::class, $id);
        }
        $this->assertInventoryCount(5);

        // Start Example 43
        $cursor = $db->inventory->find(['status' => 'A']);
        // End Example 43

        $documents = $cursor->toArray();
        $this->assertCount(3, $documents);
        foreach ($documents as $document) {
            foreach (['_id', 'item', 'status', 'size', 'instock'] as $field) {
                $this->assertObjectHasAttribute($field, $document);
            }
        }

        // Start Example 44
        $cursor = $db->inventory->find(
            ['status' => 'A'],
            ['projection' => ['item' => 1, 'status' => 1]]
        );
        // End Example 44

        $documents = $cursor->toArray();
        $this->assertCount(3, $documents);
        foreach ($documents as $document) {
            foreach (['_id', 'item', 'status'] as $field) {
                $this->assertObjectHasAttribute($field, $document);
            }
            foreach (['size', 'instock'] as $field) {
                $this->assertObjectNotHasAttribute($field, $document);
            }
        }

        // Start Example 45
        $cursor = $db->inventory->find(
            ['status' => 'A'],
            ['projection' => ['item' => 1, 'status' => 1, '_id' => 0]]
        );
        // End Example 45

        $documents = $cursor->toArray();
        $this->assertCount(3, $documents);
        foreach ($documents as $document) {
            foreach (['item', 'status'] as $field) {
                $this->assertObjectHasAttribute($field, $document);
            }
            foreach (['_id', 'size', 'instock'] as $field) {
                $this->assertObjectNotHasAttribute($field, $document);
            }
        }

        // Start Example 46
        $cursor = $db->inventory->find(
            ['status' => 'A'],
            ['projection' => ['status' => 0, 'instock' => 0]]
        );
        // End Example 46

        $documents = $cursor->toArray();
        $this->assertCount(3, $documents);
        foreach ($documents as $document) {
            foreach (['_id', 'item', 'size'] as $field) {
                $this->assertObjectHasAttribute($field, $document);
            }
            foreach (['status', 'instock'] as $field) {
                $this->assertObjectNotHasAttribute($field, $document);
            }
        }

        // Start Example 47
        $cursor = $db->inventory->find(
            ['status' => 'A'],
            ['projection' => ['item' => 1, 'status' => 1, 'size.uom' => 1]]
        );
        // End Example 47

        $documents = $cursor->toArray();
        $this->assertCount(3, $documents);
        foreach ($documents as $document) {
            foreach (['_id', 'item', 'status', 'size'] as $field) {
                $this->assertObjectHasAttribute($field, $document);
            }
            $this->assertObjectNotHasAttribute('instock', $document);
            $this->assertObjectHasAttribute('uom', $document->size);
            $this->assertObjectNotHasAttribute('h', $document->size);
            $this->assertObjectNotHasAttribute('w', $document->size);
        }

        // Start Example 48
        $cursor = $db->inventory->find(
            ['status' => 'A'],
            ['projection' => ['size.uom' => 0]]
        );
        // End Example 48

        $documents = $cursor->toArray();
        $this->assertCount(3, $documents);
        foreach ($documents as $document) {
            foreach (['_id', 'item', 'status', 'size', 'instock'] as $field) {
                $this->assertObjectHasAttribute($field, $document);
            }
            $this->assertObjectHasAttribute('h', $document->size);
            $this->assertObjectHasAttribute('w', $document->size);
            $this->assertObjectNotHasAttribute('uom', $document->size);
        }

        // Start Example 49
        $cursor = $db->inventory->find(
            ['status' => 'A'],
            ['projection' => ['item' => 1, 'status' => 1, 'instock.qty' => 1]]
        );
        // End Example 49

        $documents = $cursor->toArray();
        $this->assertCount(3, $documents);
        foreach ($documents as $document) {
            foreach (['_id', 'item', 'status', 'instock'] as $field) {
                $this->assertObjectHasAttribute($field, $document);
            }
            $this->assertObjectNotHasAttribute('size', $document);
            foreach ($document->instock as $instock) {
                $this->assertObjectHasAttribute('qty', $instock);
                $this->assertObjectNotHasAttribute('warehouse', $instock);
            }
        }

        // Start Example 50
        $cursor = $db->inventory->find(
            ['status' => 'A'],
            ['projection' => ['item' => 1, 'status' => 1, 'instock' => ['$slice' => -1]]]
        );
        // End Example 50

        $documents = $cursor->toArray();
        $this->assertCount(3, $documents);
        foreach ($documents as $document) {
            foreach (['_id', 'item', 'status', 'instock'] as $field) {
                $this->assertObjectHasAttribute($field, $document);
            }
            $this->assertObjectNotHasAttribute('size', $document);
            $this->assertCount(1, $document->instock);
        }
    }

    public function testExample_51_54()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 51
        $insertManyResult = $db->inventory->insertMany([
            [
                'item' => 'canvas',
                'qty' => 100,
                'size' => ['h' => 28, 'w' => 35.5, 'uom' => 'cm'],
                'status' => 'A',
            ],
            [
                'item' => 'journal',
                'qty' => 25,
                'size' => ['h' => 14, 'w' => 21, 'uom' => 'cm'],
                'status' => 'A',
            ],
            [
                'item' => 'mat',
                'qty' => 85,
                'size' => ['h' => 27.9, 'w' => 35.5, 'uom' => 'cm'],
                'status' => 'A',
            ],
            [
                'item' => 'mousepad',
                'qty' => 25,
                'size' => ['h' => 19, 'w' => 22.85, 'uom' => 'cm'],
                'status' => 'P',
            ],
            [
                'item' => 'notebook',
                'qty' => 50,
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'status' => 'P',
            ],
            [
                'item' => 'paper',
                'qty' => 100,
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'status' => 'D',
            ],
            [
                'item' => 'planner',
                'qty' => 75,
                'size' => ['h' => 22.85, 'w' => 30, 'uom' => 'cm'],
                'status' => 'D',
            ],
            [
                'item' => 'postcard',
                'qty' => 45,
                'size' => ['h' => 10, 'w' => 15.25, 'uom' => 'cm'],
                'status' => 'A',
            ],
            [
                'item' => 'sketchbook',
                'qty' => 80,
                'size' => ['h' => 14, 'w' => 21, 'uom' => 'cm'],
                'status' => 'A',
            ],
            [
                'item' => 'sketch pad',
                'qty' => 95,
                'size' => ['h' => 22.85, 'w' => 30.5, 'uom' => 'cm'],
                'status' => 'A',
            ],
        ]);
        // End Example 51

        $this->assertSame(10, $insertManyResult->getInsertedCount());
        foreach ($insertManyResult->getInsertedIds() as $id) {
            $this->assertInstanceOf(ObjectId::class, $id);
        }
        $this->assertInventoryCount(10);

        // Start Example 52
        $updateResult = $db->inventory->updateOne(
            ['item' => 'paper'],
            [
                '$set' => ['size.uom' => 'cm', 'status' => 'P'],
                '$currentDate' => ['lastModified' => true],
            ]
        );
        // End Example 52

        $this->assertSame(1, $updateResult->getMatchedCount());
        $this->assertSame(1, $updateResult->getModifiedCount());
        $cursor = $db->inventory->find([
            'item' => 'paper',
            'size.uom' => 'cm',
            'status' => 'P',
            'lastModified' => ['$type' => 9],
        ]);
        $this->assertCursorCount(1, $cursor);

        // Start Example 53
        $updateResult = $db->inventory->updateMany(
            ['qty' => ['$lt' => 50]],
            [
                '$set' => ['size.uom' => 'cm', 'status' => 'P'],
                '$currentDate' => ['lastModified' => true],
            ]
        );
        // End Example 53

        $this->assertSame(3, $updateResult->getMatchedCount());
        $this->assertSame(3, $updateResult->getModifiedCount());
        $cursor = $db->inventory->find([
            'qty' => ['$lt' => 50],
            'size.uom' => 'cm',
            'status' => 'P',
            'lastModified' => ['$type' => 9],
        ]);
        $this->assertCursorCount(3, $cursor);

        // Start Example 54
        $updateResult = $db->inventory->replaceOne(
            ['item' => 'paper'],
            [
                'item' => 'paper',
                'instock' => [
                    ['warehouse' => 'A', 'qty' => 60],
                    ['warehouse' => 'B', 'qty' => 40],
                ],
            ]
        );
        // End Example 54

        $this->assertSame(1, $updateResult->getMatchedCount());
        $this->assertSame(1, $updateResult->getModifiedCount());
        $cursor = $db->inventory->find([
            'item' => 'paper',
            'instock' => [
                ['warehouse' => 'A', 'qty' => 60],
                ['warehouse' => 'B', 'qty' => 40],
            ],
        ]);
        $this->assertCursorCount(1, $cursor);
    }

    public function testExample_55_58()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Example 55
        $insertManyResult = $db->inventory->insertMany([
            [
                'item' => 'journal',
                'qty' => 25,
                'size' => ['h' => 14, 'w' => 21, 'uom' => 'cm'],
                'status' => 'A',
            ],
            [
                'item' => 'notebook',
                'qty' => 50,
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'status' => 'P',
            ],
            [
                'item' => 'paper',
                'qty' => 100,
                'size' => ['h' => 8.5, 'w' => 11, 'uom' => 'in'],
                'status' => 'D',
            ],
            [
                'item' => 'planner',
                'qty' => 75,
                'size' => ['h' => 22.85, 'w' => 30, 'uom' => 'cm'],
                'status' => 'D',
            ],
            [
                'item' => 'postcard',
                'qty' => 45,
                'size' => ['h' => 10, 'w' => 15.25, 'uom' => 'cm'],
                'status' => 'A',
            ],
        ]);
        // End Example 55

        $this->assertSame(5, $insertManyResult->getInsertedCount());
        foreach ($insertManyResult->getInsertedIds() as $id) {
            $this->assertInstanceOf(ObjectId::class, $id);
        }
        $this->assertInventoryCount(5);

        // Start Example 57
        $deleteResult = $db->inventory->deleteMany(['status' => 'A']);
        // End Example 57

        $this->assertSame(2, $deleteResult->getDeletedCount());
        $cursor = $db->inventory->find(['status' => 'A']);
        $this->assertCursorCount(0, $cursor);

        // Start Example 58
        $deleteResult = $db->inventory->deleteOne(['status' => 'D']);
        // End Example 58

        $this->assertSame(1, $deleteResult->getDeletedCount());
        $cursor = $db->inventory->find(['status' => 'D']);
        $this->assertCursorCount(1, $cursor);

        // Start Example 56
        $deleteResult = $db->inventory->deleteMany([]);
        // End Example 56

        $this->assertSame(2, $deleteResult->getDeletedCount());
        $this->assertInventoryCount(0);
    }

    public function testChangeStreamExample_1_4()
    {
        $this->skipIfChangeStreamIsNotSupported();

        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Test does not apply on sharded clusters: need more than a single getMore call on the change stream.');
        }

        $db = new Database($this->manager, $this->getDatabaseName());
        $db->dropCollection('inventory');
        $db->createCollection('inventory');

        // Start Changestream Example 1
        $changeStream = $db->inventory->watch();
        $changeStream->rewind();

        $firstChange = $changeStream->current();

        $changeStream->next();

        $secondChange = $changeStream->current();
        // End Changestream Example 1

        $this->assertNull($firstChange);
        $this->assertNull($secondChange);

        // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
        // Start Changestream Example 2
        $changeStream = $db->inventory->watch([], ['fullDocument' => \MongoDB\Operation\Watch::FULL_DOCUMENT_UPDATE_LOOKUP]);
        $changeStream->rewind();

        $firstChange = $changeStream->current();

        $changeStream->next();

        $secondChange = $changeStream->current();
        // End Changestream Example 2
        // phpcs:enable

        $this->assertNull($firstChange);
        $this->assertNull($secondChange);

        $insertManyResult = $db->inventory->insertMany([
            ['_id' => 1, 'x' => 'foo'],
            ['_id' => 2, 'x' => 'bar'],
        ]);
        $this->assertEquals(2, $insertManyResult->getInsertedCount());

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $lastChange = $changeStream->current();

        $expectedChange = [
            '_id' => $lastChange->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 1, 'x' => 'foo'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => 'inventory'],
            'documentKey' => ['_id' => 1],
        ];

        $this->assertMatchesDocument($expectedChange, $lastChange);

        // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
        // Start Changestream Example 3
        $resumeToken = $changeStream->getResumeToken();

        if ($resumeToken === null) {
            throw new \Exception('Resume token was not found');
        }

        $changeStream = $db->inventory->watch([], ['resumeAfter' => $resumeToken]);
        $changeStream->rewind();

        $firstChange = $changeStream->current();
        // End Changestream Example 3
        // phpcs:enable

        $expectedChange = [
            '_id' => $firstChange->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => 'inventory'],
            'documentKey' => ['_id' => 2],
        ];

        $this->assertMatchesDocument($expectedChange, $firstChange);

        // Start Changestream Example 4
        $pipeline = [
            ['$match' => ['fullDocument.username' => 'alice']],
            ['$addFields' => ['newField' => 'this is an added field!']],
        ];
        $changeStream = $db->inventory->watch($pipeline);
        $changeStream->rewind();

        $firstChange = $changeStream->current();

        $changeStream->next();

        $secondChange = $changeStream->current();
        // End Changestream Example 4

        $this->assertNull($firstChange);
        $this->assertNull($secondChange);
    }

    public function testAggregation_example_1()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Aggregation Example 1
        $cursor = $db->sales->aggregate([
            ['$match' => ['items.fruit' => 'banana']],
            ['$sort' => ['date' => 1]],
        ]);
        // End Aggregation Example 1

        $this->assertInstanceOf(Cursor::class, $cursor);
    }

    public function testAggregation_example_2()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Aggregation Example 2
        $cursor = $db->sales->aggregate([
            ['$unwind' => '$items'],
            ['$match' => ['items.fruit' => 'banana']],
            [
                '$group' => [
                    '_id' => ['day' => ['$dayOfWeek' => '$date']],
                    'count' => ['$sum' => '$items.quantity'],
                ],
            ],
            [
                '$project' => [
                    'dayOfWeek' => '$_id.day',
                    'numberSold' => '$count',
                    '_id' => 0,
                ],
            ],
            ['$sort' => ['numberSold' => 1]],
        ]);
        // End Aggregation Example 2

        $this->assertInstanceOf(Cursor::class, $cursor);
    }

    public function testAggregation_example_3()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Aggregation Example 3
        $cursor = $db->sales->aggregate([
            ['$unwind' => '$items'],
            [
                '$group' => [
                    '_id' => ['day' => ['$dayOfWeek' => '$date']],
                    'items_sold' => ['$sum' => '$items.quantity'],
                    'revenue' => [
                        '$sum' => [
                            '$multiply' => ['$items.quantity', '$items.price'],
                        ],
                    ],
                ],
            ],
            [
                '$project' => [
                    'day' => '$_id.day',
                    'revenue' => 1,
                    'items_sold' => 1,
                    'discount' => [
                        '$cond' => [
                            'if' => ['$lte' => ['$revenue', 250]],
                            'then' => 25,
                            'else' => 0,
                        ],
                    ],
                ],
            ],
        ]);
        // End Aggregation Example 3

        $this->assertInstanceOf(Cursor::class, $cursor);
    }

    public function testAggregation_example_4()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('$lookup does not support "let" option');
        }

        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Aggregation Example 4
        $cursor = $db->air_alliances->aggregate([
            [
                '$lookup' => [
                    'from' => 'air_airlines',
                    'let' => ['constituents' => '$airlines'],
                    'pipeline' => [[
                        '$match' => [
                            '$expr' => ['$in' => ['$name', '$constituents']],
                        ],
                    ],
                    ],
                    'as' => 'airlines',
                ],
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'name' => 1,
                    'airlines' => [
                        '$filter' => [
                            'input' => '$airlines',
                            'as' => 'airline',
                            'cond' => ['$eq' => ['$$airline.country', 'Canada']],
                        ],
                    ],
                ],
            ],
        ]);
        // End Aggregation Example 4

        $this->assertInstanceOf(Cursor::class, $cursor);
    }

    public function testRunCommand_example_1()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start runCommand Example 1
        $cursor = $db->command(['buildInfo' => 1]);
        $result = $cursor->toArray()[0];
        // End runCommand Example 1

        $this->assertInstanceOf(Cursor::class, $cursor);
    }

    public function testRunCommand_example_2()
    {
        $db = new Database($this->manager, $this->getDatabaseName());
        $db->dropCollection('restaurants');
        $db->createCollection('restaurants');

        // Start runCommand Example 2
        $cursor = $db->command(['collStats' => 'restaurants']);
        $result = $cursor->toArray()[0];
        // End runCommand Example 2

        $this->assertInstanceOf(Cursor::class, $cursor);
    }

    public function testIndex_example_1()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Index Example 1
        $indexName = $db->records->createIndex(['score' => 1]);
        // End Index Example 1

        $this->assertEquals('score_1', $indexName);
    }

    public function testIndex_example_2()
    {
        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Index Example 2
        $indexName = $db->restaurants->createIndex(
            ['cuisine' => 1, 'name' => 1],
            ['partialFilterExpression' => ['rating' => ['$gt' => 5]]]
        );
        // End Index Example 2

        $this->assertEquals('cuisine_1_name_1', $indexName);
    }

    // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
    // phpcs:disable Squiz.Commenting.FunctionComment.WrongStyle
    // phpcs:disable Squiz.WhiteSpace.FunctionSpacing.After
    // Start Transactions Intro Example 1
    private function updateEmployeeInfo1(\MongoDB\Client $client, \MongoDB\Driver\Session $session)
    {
        $session->startTransaction([
            'readConcern' => new \MongoDB\Driver\ReadConcern('snapshot'),
            'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY),
        ]);

        try {
            $client->hr->employees->updateOne(
                ['employee' => 3],
                ['$set' => ['status' => 'Inactive']],
                ['session' => $session]
            );
            $client->reporting->events->insertOne(
                ['employee' => 3, 'status' => [ 'new' => 'Inactive', 'old' => 'Active']],
                ['session' => $session]
            );
        } catch (\MongoDB\Driver\Exception\Exception $error) {
            echo "Caught exception during transaction, aborting.\n";
            $session->abortTransaction();
            throw $error;
        }

        while (true) {
            try {
                $session->commitTransaction();
                echo "Transaction committed.\n";
                break;
            } catch (\MongoDB\Driver\Exception\CommandException $error) {
                $resultDoc = $error->getResultDocument();

                if (isset($resultDoc->errorLabels) && in_array('UnknownTransactionCommitResult', $resultDoc->errorLabels)) {
                    echo "UnknownTransactionCommitResult, retrying commit operation ...\n";
                    continue;
                } else {
                    echo "Error during commit ...\n";
                    throw $error;
                }
            } catch (\MongoDB\Driver\Exception\Exception $error) {
                echo "Error during commit ...\n";
                throw $error;
            }
        }
    }
    // End Transactions Intro Example 1
    // phpcs:enable

    public function testTransactions_intro_example_1()
    {
        $this->skipIfTransactionsAreNotSupported();

        $this->assertNotNull('This test intentionally performs no assertions');

        $client = new Client(static::getUri());

        /* The WC is required: https://docs.mongodb.com/manual/core/transactions/#transactions-and-locks */
        $client->hr->dropCollection('employees', ['writeConcern' => new WriteConcern('majority')]);
        $client->reporting->dropCollection('events', ['writeConcern' => new WriteConcern('majority')]);

        /* Collections need to be created before a transaction starts */
        $client->hr->createCollection('employees', ['writeConcern' => new WriteConcern('majority')]);
        $client->reporting->createCollection('events', ['writeConcern' => new WriteConcern('majority')]);

        $session = $client->startSession();

        ob_start();
        try {
            $this->updateEmployeeInfo1($client, $session);
        } finally {
            ob_end_clean();
        }
    }

    // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
    // phpcs:disable Squiz.Commenting.FunctionComment.WrongStyle
    // phpcs:disable Squiz.WhiteSpace.FunctionSpacing.After
    // Start Transactions Retry Example 1
    private function runTransactionWithRetry1(callable $txnFunc, \MongoDB\Client $client, \MongoDB\Driver\Session $session)
    {
        while (true) {
            try {
                $txnFunc($client, $session);  // performs transaction
                break;
            } catch (\MongoDB\Driver\Exception\CommandException $error) {
                $resultDoc = $error->getResultDocument();
                echo "Transaction aborted. Caught exception during transaction.\n";

                // If transient error, retry the whole transaction
                if (isset($resultDoc->errorLabels) && in_array('TransientTransactionError', $resultDoc->errorLabels)) {
                    echo "TransientTransactionError, retrying transaction ...\n";
                    continue;
                } else {
                    throw $error;
                }
            } catch (\MongoDB\Driver\Exception\Exception $error) {
                throw $error;
            }
        }
    }
    // End Transactions Retry Example 1
    // phpcs:enable

    // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
    // phpcs:disable Squiz.Commenting.FunctionComment.WrongStyle
    // phpcs:disable Squiz.WhiteSpace.FunctionSpacing.After
    // Start Transactions Retry Example 2
    private function commitWithRetry2(\MongoDB\Driver\Session $session)
    {
        while (true) {
            try {
                $session->commitTransaction();
                echo "Transaction committed.\n";
                break;
            } catch (\MongoDB\Driver\Exception\CommandException $error) {
                $resultDoc = $error->getResultDocument();

                if (isset($resultDoc->errorLabels) && in_array('UnknownTransactionCommitResult', $resultDoc->errorLabels)) {
                    echo "UnknownTransactionCommitResult, retrying commit operation ...\n";
                    continue;
                } else {
                    echo "Error during commit ...\n";
                    throw $error;
                }
            } catch (\MongoDB\Driver\Exception\Exception $error) {
                echo "Error during commit ...\n";
                throw $error;
            }
        }
    }
    // End Transactions Retry Example 2
    // phpcs:enable

    // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
    // phpcs:disable Squiz.Commenting.FunctionComment.WrongStyle
    // phpcs:disable Squiz.WhiteSpace.FunctionSpacing.After
    // Start Transactions Retry Example 3
    private function runTransactionWithRetry3(callable $txnFunc, \MongoDB\Client $client, \MongoDB\Driver\Session $session)
    {
        while (true) {
            try {
                $txnFunc($client, $session);  // performs transaction
                break;
            } catch (\MongoDB\Driver\Exception\CommandException $error) {
                $resultDoc = $error->getResultDocument();

                // If transient error, retry the whole transaction
                if (isset($resultDoc->errorLabels) && in_array('TransientTransactionError', $resultDoc->errorLabels)) {
                    continue;
                } else {
                    throw $error;
                }
            } catch (\MongoDB\Driver\Exception\Exception $error) {
                throw $error;
            }
        }
    }

    private function commitWithRetry3(\MongoDB\Driver\Session $session)
    {
        while (true) {
            try {
                $session->commitTransaction();
                echo "Transaction committed.\n";
                break;
            } catch (\MongoDB\Driver\Exception\CommandException $error) {
                $resultDoc = $error->getResultDocument();

                if (isset($resultDoc->errorLabels) && in_array('UnknownTransactionCommitResult', $resultDoc->errorLabels)) {
                    echo "UnknownTransactionCommitResult, retrying commit operation ...\n";
                    continue;
                } else {
                    echo "Error during commit ...\n";
                    throw $error;
                }
            } catch (\MongoDB\Driver\Exception\Exception $error) {
                echo "Error during commit ...\n";
                throw $error;
            }
        }
    }

    private function updateEmployeeInfo3(\MongoDB\Client $client, \MongoDB\Driver\Session $session)
    {
        $session->startTransaction([
            'readConcern' => new \MongoDB\Driver\ReadConcern("snapshot"),
            'readPrefernece' => new \MongoDB\Driver\ReadPreference(\MongoDB\Driver\ReadPreference::RP_PRIMARY),
            'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY),
        ]);

        try {
            $client->hr->employees->updateOne(
                ['employee' => 3],
                ['$set' => ['status' => 'Inactive']],
                ['session' => $session]
            );
            $client->reporting->events->insertOne(
                ['employee' => 3, 'status' => [ 'new' => 'Inactive', 'old' => 'Active']],
                ['session' => $session]
            );
        } catch (\MongoDB\Driver\Exception\Exception $error) {
            echo "Caught exception during transaction, aborting.\n";
            $session->abortTransaction();
            throw $error;
        }

        $this->commitWithRetry3($session);
    }

    private function doUpdateEmployeeInfo(\MongoDB\Client $client)
    {
        // Start a session.
        $session = $client->startSession();

        try {
            $this->runTransactionWithRetry3([$this, 'updateEmployeeInfo3'], $client, $session);
        } catch (\MongoDB\Driver\Exception\Exception $error) {
            // Do something with error
        }
    }
    // End Transactions Retry Example 3
    // phpcs:enable

    public function testTransactions_retry_example_3()
    {
        $this->skipIfTransactionsAreNotSupported();

        $this->assertNotNull('This test intentionally performs no assertions');

        $client = new Client(static::getUri());

        /* The WC is required: https://docs.mongodb.com/manual/core/transactions/#transactions-and-locks */
        $client->hr->dropCollection('employees', ['writeConcern' => new WriteConcern('majority')]);
        $client->reporting->dropCollection('events', ['writeConcern' => new WriteConcern('majority')]);

        /* Collections need to be created before a transaction starts */
        $client->hr->createCollection('employees', ['writeConcern' => new WriteConcern('majority')]);
        $client->reporting->createCollection('events', ['writeConcern' => new WriteConcern('majority')]);

        ob_start();
        try {
            $this->doUpdateEmployeeInfo($client);
        } finally {
            ob_end_clean();
        }
    }

    public function testCausalConsistency()
    {
        $this->skipIfCausalConsistencyIsNotSupported();

        try {
            $this->manager->selectServer(new ReadPreference('secondary'));
        } catch (ConnectionTimeoutException $e) {
            $this->markTestSkipped('Secondary is not available');
        }

        $this->assertNotNull('This test intentionally performs no assertions');

        // Prep
        $client = new Client(static::getUri());
        $items = $client->selectDatabase(
            'test',
            [ 'writeConcern' => new WriteConcern(WriteConcern::MAJORITY) ]
        )->items;

        $items->drop();
        $items->insertOne(
            [ 'sku' => '111', 'name' => 'Peanuts', 'start' => new UTCDateTime() ]
        );

        // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
        // Start Causal Consistency Example 1
        $items = $client->selectDatabase(
            'test',
            [
                'readConcern' => new \MongoDB\Driver\ReadConcern(\MongoDB\Driver\ReadConcern::MAJORITY),
                'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000),
            ]
        )->items;

        $s1 = $client->startSession(
            [ 'causalConsistency' => true ]
        );

        $currentDate = new \MongoDB\BSON\UTCDateTime();

        $items->updateOne(
            [ 'sku' => '111', 'end' => [ '$exists' => false ] ],
            [ '$set' => [ 'end' => $currentDate ] ],
            [ 'session' => $s1 ]
        );
        $items->insertOne(
            [ 'sku' => '111-nuts', 'name' => 'Pecans', 'start' => $currentDate ],
            [ 'session' => $s1 ]
        );
        // End Causal Consistency Example 1
        // phpcs:enable

        ob_start();

        // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
        // Start Causal Consistency Example 2
        $s2 = $client->startSession(
            [ 'causalConsistency' => true ]
        );
        $s2->advanceClusterTime($s1->getClusterTime());
        $s2->advanceOperationTime($s1->getOperationTime());

        $items = $client->selectDatabase(
            'test',
            [
                'readPreference' => new \MongoDB\Driver\ReadPreference(\MongoDB\Driver\ReadPreference::RP_SECONDARY),
                'readConcern' => new \MongoDB\Driver\ReadConcern(\MongoDB\Driver\ReadConcern::MAJORITY),
                'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000),
            ]
        )->items;

        $result = $items->find(
            [ 'end' => [ '$exists' => false ] ],
            [ 'session' => $s2 ]
        );
        foreach ($result as $item) {
            var_dump($item);
        }
        // End Causal Consistency Example 2
        // phpcs:enable

        ob_end_clean();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testWithTransactionExample()
    {
        $this->skipIfTransactionsAreNotSupported();

        $uriString = static::getUri(true);

        // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
        // Start Transactions withTxn API Example 1
        /*
         * For a replica set, include the replica set name and a seedlist of the members in the URI string; e.g.
         * uriString = 'mongodb://mongodb0.example.com:27017,mongodb1.example.com:27017/?replicaSet=myRepl'
         * For a sharded cluster, connect to the mongos instances; e.g.
         * uriString = 'mongodb://mongos0.example.com:27017,mongos1.example.com:27017/'
         */

        $client = new \MongoDB\Client($uriString);

        // Prerequisite: Create collections. CRUD operations in transactions must be on existing collections.
        $client->selectCollection(
            'mydb1',
            'foo',
            [
                'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000),
            ]
        )->insertOne(['abc' => 0]);

        $client->selectCollection(
            'mydb2',
            'bar',
            [
                'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000),
            ]
        )->insertOne(['xyz' => 0]);

        // Step 1: Define the callback that specifies the sequence of operations to perform inside the transactions.

        $callback = function (\MongoDB\Driver\Session $session) use ($client) {
            $client
                ->selectCollection('mydb1', 'foo')
                ->insertOne(['abc' => 1], ['session' => $session]);

            $client
                ->selectCollection('mydb2', 'bar')
                ->insertOne(['xyz' => 999], ['session' => $session]);
        };

        // Step 2: Start a client session.

        $session = $client->startSession();

        // Step 3: Use with_transaction to start a transaction, execute the callback, and commit (or abort on error).

        $transactionOptions = [
            'readConcern' => new \MongoDB\Driver\ReadConcern(\MongoDB\Driver\ReadConcern::LOCAL),
            'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000),
            'readPreference' => new \MongoDB\Driver\ReadPreference(\MongoDB\Driver\ReadPreference::RP_PRIMARY),
        ];

        \MongoDB\with_transaction($session, $callback, $transactionOptions);

        // End Transactions withTxn API Example 1
        // phpcs:enable
    }

    /**
     * Return the test collection name.
     *
     * @return string
     */
    protected function getCollectionName()
    {
        return 'inventory';
    }

    private function assertCursorCount($count, Cursor $cursor)
    {
        $this->assertCount($count, $cursor->toArray());
    }

    private function assertInventoryCount($count)
    {
        $this->assertCollectionCount($this->getDatabaseName() . '.' . $this->getCollectionName(), $count);
    }
}
