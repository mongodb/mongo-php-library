<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\Tests\FunctionalTestCase;
use MongoDB\Tests\UnifiedSpecTests\EntityMap;
use PHPUnit\Framework\ExpectationFailedException;
use stdClass;
use function fopen;
use function fwrite;
use function hex2bin;
use function rewind;

class MatchesTest extends FunctionalTestCase
{
    public function testMatchesDocument()
    {
        $c = new Matches(['x' => 1, 'y' => ['a' => 1, 'b' => 2]]);

        $this->assertResult(false, $c, ['x' => 1, 'y' => 2], 'Incorrect value');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2]], 'Exact match');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2], 'z' => 3], 'Extra keys in root are permitted');
        $this->assertResult(false, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2, 'c' => 3]], 'Extra keys in embedded are not permitted');
        $this->assertResult(true, $c, ['y' => ['b' => 2, 'a' => 1], 'x' => 1], 'Root and embedded key order is not significant');
    }

    public function testOperatorExists()
    {
        $c = new Matches(['x' => ['$$exists' => true]]);
        $this->assertResult(true, $c, ['x' => '1'], 'root-level key exists');
        $this->assertResult(false, $c, new stdClass(), 'root-level key missing');
        $this->assertResult(true, $c, ['x' => '1', 'y' => 1], 'root-level key exists (extra key)');
        $this->assertResult(false, $c, ['y' => 1], 'root-level key missing (extra key)');

        $c = new Matches(['x' => ['$$exists' => false]]);
        $this->assertResult(false, $c, ['x' => '1'], 'root-level key exists');
        $this->assertResult(true, $c, new stdClass(), 'root-level key missing');
        $this->assertResult(false, $c, ['x' => '1', 'y' => 1], 'root-level key exists (extra key)');
        $this->assertResult(true, $c, ['y' => 1], 'root-level key missing (extra key)');

        $c = new Matches(['x' => ['y' => ['$$exists' => true]]]);
        $this->assertResult(true, $c, ['x' => ['y' => '1']], 'embedded key exists');
        $this->assertResult(false, $c, ['x' => new stdClass()], 'embedded key missing');

        $c = new Matches(['x' => ['y' => ['$$exists' => false]]]);
        $this->assertResult(false, $c, ['x' => ['y' => 1]], 'embedded key exists');
        $this->assertResult(true, $c, ['x' => new stdClass()], 'embedded key missing');
    }

    public function testOperatorType()
    {
        $c = new Matches(['x' => ['$$type' => 'string']]);

        $this->assertResult(true, $c, ['x' => 'foo'], 'string matches string type');
        $this->assertResult(false, $c, ['x' => 1], 'integer does not match string type');

        $c = new Matches(['x' => ['$$type' => ['string', 'bool']]]);

        $this->assertResult(true, $c, ['x' => 'foo'], 'string matches [string,bool] type');
        $this->assertResult(true, $c, ['x' => true], 'bool matches [string,bool] type');
        $this->assertResult(false, $c, ['x' => 1], 'integer does not match [string,bool] type');
    }

    public function testOperatorMatchesEntity()
    {
        $entityMap = new EntityMap();
        $entityMap['integer'] = 1;
        $entityMap['object'] = ['y' => 1];

        $c = new Matches(['x' => ['$$matchesEntity' => 'integer']], $entityMap);

        $this->assertResult(true, $c, ['x' => 1], 'value matches integer entity (embedded)');
        $this->assertResult(false, $c, ['x' => 2], 'value does not match integer entity (embedded)');
        $this->assertResult(false, $c, ['x' => ['y' => 1]], 'value does not match integer entity (embedded)');

        $c = new Matches(['x' => ['$$matchesEntity' => 'object']], $entityMap);

        $this->assertResult(true, $c, ['x' => ['y' => 1]], 'value matches object entity (embedded)');
        $this->assertResult(false, $c, ['x' => 1], 'value does not match object entity (embedded)');
        $this->assertResult(false, $c, ['x' => ['y' => 1, 'z' => 2]], 'value does not match object entity (embedded)');

        $c = new Matches(['$$matchesEntity' => 'object'], $entityMap);

        $this->assertResult(true, $c, ['y' => 1], 'value matches object entity (root-level)');
        $this->assertResult(true, $c, ['x' => 2, 'y' => 1], 'value matches object entity (root-level)');
        $this->assertResult(false, $c, ['x' => ['y' => 1, 'z' => 2]], 'value does not match object entity (root-level)');

        $c = new Matches(['$$matchesEntity' => 'undefined'], $entityMap);

        $this->assertResult(false, $c, 'undefined', 'value does not match undefined entity (root-level)');

        $c = new Matches(['x' => ['$$matchesEntity' => 'undefined']], $entityMap);

        $this->assertResult(false, $c, ['x' => 'undefined'], 'value does not match undefined entity (embedded)');
    }

    public function testOperatorMatchesHexBytes()
    {
        $stream1 = fopen('php://temp', 'w+b');
        fwrite($stream1, hex2bin('DEADBEEF'));
        rewind($stream1);

        $stream2 = fopen('php://temp', 'w+b');
        fwrite($stream2, hex2bin('90ABCDEF'));
        rewind($stream2);

        $c = new Matches(['$$matchesHexBytes' => 'DEADBEEF']);

        $this->assertResult(true, $c, $stream1, 'value matches hex bytes (root-level)');
        $this->assertResult(false, $c, $stream2, 'value does not match hex bytes (root-level)');
        $this->assertResult(false, $c, 1, 'value is not a stream');

        $c = new Matches(['x' => ['$$matchesHexBytes' => '90ABCDEF']]);

        $this->assertResult(true, $c, ['x' => $stream2], 'value matches hex bytes (embedded)');
        $this->assertResult(false, $c, ['x' => $stream1], 'value does not match hex bytes (embedded)');
        $this->assertResult(false, $c, ['x' => 1], 'value is not a stream');
    }

    public function testOperatorUnsetOrMatches()
    {
        $c = new Matches(['$$unsetOrMatches' => ['x' => 1]]);

        $this->assertResult(true, $c, null, 'null value is considered unset (root-level)');
        $this->assertResult(true, $c, ['x' => 1], 'value matches (root-level)');
        $this->assertResult(true, $c, ['x' => 1, 'y' => 1], 'value matches (root-level)');
        $this->assertResult(false, $c, ['x' => 2], 'value does not match (root-level)');

        $c = new Matches(['x' => ['$$unsetOrMatches' => ['y' => 1]]]);

        $this->assertResult(true, $c, new stdClass(), 'missing value is considered unset (embedded)');
        $this->assertResult(false, $c, ['x' => null], 'null value is not considered unset (embedded)');
        $this->assertResult(true, $c, ['x' => ['y' => 1]], 'value matches (embedded)');
        $this->assertResult(false, $c, ['x' => ['y' => 1, 'z' => 2]], 'value does not match (embedded)');
    }

    public function testOperatorSessionLsid()
    {
        if (version_compare($this->getFeatureCompatibilityVersion(), '3.6', '<')) {
            $this->markTestSkipped('startSession() is only supported on FCV 3.6 or higher');
        }

        $session = $this->manager->startSession();

        $entityMap = new EntityMap();
        $entityMap['session'] = $session;

        $lsidWithWrongId = ['id' => new Binary('0123456789ABCDEF', Binary::TYPE_UUID)];
        $lsidWithExtraField = (array) $session->getLogicalSessionId() + ['y' => 1];

        $c = new Matches(['$$sessionLsid' => 'session'], $entityMap);

        $this->assertResult(true, $c, $session->getLogicalSessionId(), 'session LSID matches (root-level)');
        $this->assertResult(false, $c, $lsidWithWrongId, 'session LSID does not match (root-level)');
        $this->assertResult(false, $c, $lsidWithExtraField, 'session LSID does not match (root-level)');
        $this->assertResult(false, $c, 1, 'session LSID does not match (root-level)');

        $c = new Matches(['x' => ['$$sessionLsid' => 'session']], $entityMap);

        $this->assertResult(true, $c, ['x' => $session->getLogicalSessionId()], 'session LSID matches (embedded)');
        $this->assertResult(false, $c, ['x' => $lsidWithWrongId], 'session LSID does not match (embedded)');
        $this->assertResult(false, $c, ['x' => $lsidWithExtraField], 'session LSID does not match (embedded)');
        $this->assertResult(false, $c, ['x' => 1], 'session LSID does not match (embedded)');
    }

    /**
     * @dataProvider errorMessageProvider
     */
    public function testErrorMessages($expectedMessagePart, Matches $constraint, $actualValue)
    {
        try {
            $constraint->evaluate($actualValue);
            $this->fail('Expected a comparison failure');
        } catch (ExpectationFailedException $failure) {
            $this->assertStringContainsString('Failed asserting that expected value matches actual value.', $failure->getMessage());
            $this->assertStringContainsString($expectedMessagePart, $failure->getMessage());
        }
    }

    public function errorMessageProvider()
    {
        return [
            'assertEquals: type check (root-level)' => [
                'string is not expected type "integer"',
                new Matches(1),
                '1',
            ],
            'assertEquals: type check (embedded)' => [
                'Field path "x": string is not expected type "integer"',
                new Matches(['x' => 1]),
                ['x' => '1'],
            ],
            'assertEquals: comparison failure (root-level)' => [
                'Failed asserting that two strings are equal.',
                new Matches('foo'),
                'bar',
            ],
            'assertEquals: comparison failure (embedded)' => [
                'Field path "x": Failed asserting that two strings are equal.',
                new Matches(['x' => 'foo']),
                ['x' => 'bar'],
            ],
            'assertMatchesArray: type check (root-level)' => [
                'MongoDB\Model\BSONDocument is not instance of expected class "MongoDB\Model\BSONArray"',
                new Matches([1, 2, 3]),
                ['x' => 1],
            ],
            'assertMatchesArray: type check (embedded)' => [
                'Field path "x": integer is not instance of expected class "MongoDB\Model\BSONArray"',
                new Matches(['x' => [1, 2, 3]]),
                ['x' => 1],
            ],
            'assertMatchesArray: count check (root-level)' => [
                '$actual count is 2, expected 3',
                new Matches(['x' => [1, 2, 3]]),
                ['x' => [1, 2]],
            ],
            'assertMatchesArray: count check (embedded)' => [
                'Field path "x": $actual count is 2, expected 3',
                new Matches(['x' => [1, 2, 3]]),
                ['x' => [1, 2]],
            ],
            'assertMatchesDocument: type check (root-level)' => [
                'integer is not instance of expected class "MongoDB\Model\BSONDocument"',
                new Matches(['x' => 1]),
                1,
            ],
            'assertMatchesDocument: type check (embedded)' => [
                'Field path "x": integer is not instance of expected class "MongoDB\Model\BSONDocument"',
                new Matches(['x' => ['y' => 1]]),
                ['x' => 1],
            ],
            'assertMatchesDocument: expected key missing (root-level)' => [
                '$actual does not have expected key "x"',
                new Matches(['x' => 1]),
                new stdClass(),
            ],
            'assertMatchesDocument: expected key missing (embedded)' => [
                'Field path "x": $actual does not have expected key "y"',
                new Matches(['x' => ['y' => 1]]),
                ['x' => new stdClass()],
            ],
            'assertMatchesDocument: unexpected key present (embedded)' => [
                'Field path "x": $actual has unexpected key "y',
                new Matches(['x' => new stdClass()]),
                ['x' => ['y' => 1]],
            ],
        ];
    }

    private function assertResult($expected, Matches $constraint, $value, $message)
    {
        $this->assertSame($expected, $constraint->evaluate($value, '', true), $message);
    }
}
