<?php

namespace MongoDB\Tests;

use MongoDB\Database;
use MongoDB\Driver\Cursor;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\DropDatabase;

/**
 * Documentation examples to be parsed for inclusion in the MongoDB manual.
 *
 * @see https://jira.mongodb.org/browse/DRIVERS-356
 */
class DocumentationExamplesTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());
    }

    public function tearDown()
    {
        if ($this->hasFailed()) {
            return;
        }

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());
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
        $this->assertInstanceOf('MongoDB\BSON\ObjectId', $insertOneResult->getInsertedId());
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
            $this->assertInstanceOf('MongoDB\BSON\ObjectId', $id);
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
            $this->assertInstanceOf('MongoDB\BSON\ObjectId', $id);
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
            $this->assertInstanceOf('MongoDB\BSON\ObjectId', $id);
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
            $this->assertInstanceOf('MongoDB\BSON\ObjectId', $id);
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
            $this->assertInstanceOf('MongoDB\BSON\ObjectId', $id);
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
            $this->assertInternalType('int', $id);
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
            $this->assertInstanceOf('MongoDB\BSON\ObjectId', $id);
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
        if (version_compare($this->getServerVersion(), '2.6.0', '<')) {
            $this->markTestSkipped('$currentDate update operator is not supported');
        }

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
            $this->assertInstanceOf('MongoDB\BSON\ObjectId', $id);
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
            $this->assertInstanceOf('MongoDB\BSON\ObjectId', $id);
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
        if (version_compare($this->getFeatureCompatibilityVersion(), '3.6', '<')) {
            $this->markTestSkipped('$changeStream is only supported on FCV 3.6 or higher');
        }

        $db = new Database($this->manager, $this->getDatabaseName());

        // Start Changestream Example 1
        $cursor = $db->inventory->watch();
        $cursor->next();
        $current = $cursor->current();
        // End Changestream Example 1

        $this->assertNull($current);

        // Start Changestream Example 2
        $cursor = $db->inventory->watch([], ['fullDocument' => \MongoDB\Operation\ChangeStream::FULL_DOCUMENT_UPDATE_LOOKUP]);
        $cursor->next();
        $current = $cursor->current();
        // End Changestream Example 2

        $this->assertNull($current);

        $insertedResult = $db->inventory->insertOne(['x' => 1]);
        $insertedId = $insertedResult->getInsertedId();
        $cursor->next();
        $current = $cursor->current();
        $expectedChange = (object) [
            '_id' => $current->_id,
            'operationType' => 'insert',
            'fullDocument' => (object) ['_id' => $insertedId, 'x' => 1],
            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'inventory'],
            'documentKey' => (object) ['_id' => $insertedId]
        ];
        $this->assertEquals($current, $expectedChange);

        // Start Changestream Example 3
        $resumeToken = ($current !== null) ? $current->_id : null;
        if ($resumeToken !== null) {
            $cursor = $db->inventory->watch([], ['resumeAfter' => $resumeToken]);
            $cursor->next();
        }
        // End Changestream Example 3

        $insertedResult = $db->inventory->insertOne(['x' => 2]);
        $insertedId = $insertedResult->getInsertedId();
        $cursor->next();
        $expectedChange = (object) [
            '_id' => $cursor->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => (object) ['_id' => $insertedId, 'x' => 2],
            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'inventory'],
            'documentKey' => (object) ['_id' => $insertedId]
        ];
        $this->assertEquals($cursor->current(), $expectedChange);

        // Start Changestream Example 4
        $pipeline = [['$match' => ['$or' => [['fullDocument.username' => 'alice'], ['operationType' => 'delete']]]]];
        $cursor = $db->inventory->watch($pipeline, []);
        $cursor->next();
        // End Changestream Example 4
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
