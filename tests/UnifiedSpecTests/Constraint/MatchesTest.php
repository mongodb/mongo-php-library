<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use MongoDB\BSON\Binary;
use MongoDB\Tests\FunctionalTestCase;
use MongoDB\Tests\UnifiedSpecTests\EntityMap;
use PHPUnit\Framework\ExpectationFailedException;
use stdClass;

use function hex2bin;
use function preg_quote;
use function version_compare;

class MatchesTest extends FunctionalTestCase
{
    public function testMatchesDocument(): void
    {
        $c = new Matches(['x' => 1, 'y' => ['a' => 1, 'b' => 2]]);
        $this->assertResult(false, $c, ['x' => 1, 'y' => 2], 'Incorrect value');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2]], 'Exact match');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2], 'z' => 3], 'Extra keys in root are permitted');
        $this->assertResult(false, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2, 'c' => 3]], 'Extra keys in embedded are not permitted');
        $this->assertResult(true, $c, ['y' => ['b' => 2, 'a' => 1], 'x' => 1], 'Root and embedded key order is not significant');
    }

    public function testDoNotAllowExtraRootKeys(): void
    {
        $c = new Matches(['x' => 1], null, false);
        $this->assertResult(false, $c, ['x' => 1, 'y' => 1], 'Extra keys in root are prohibited');
    }

    public function testDoNotAllowOperators(): void
    {
        $c = new Matches(['x' => ['$$exists' => true]], null, true, false);
        $this->assertResult(false, $c, ['x' => 1], 'Operators are not processed');
        $this->assertResult(true, $c, ['x' => ['$$exists' => true]], 'Operators are not processed but compared as-is');
    }

    public function testOperatorExists(): void
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

    public function testOperatorType(): void
    {
        $c = new Matches(['x' => ['$$type' => 'string']]);
        $this->assertResult(true, $c, ['x' => 'foo'], 'string matches string type');
        $this->assertResult(false, $c, ['x' => 1], 'integer does not match string type');

        $c = new Matches(['x' => ['$$type' => ['string', 'bool']]]);
        $this->assertResult(true, $c, ['x' => 'foo'], 'string matches [string,bool] type');
        $this->assertResult(true, $c, ['x' => true], 'bool matches [string,bool] type');
        $this->assertResult(false, $c, ['x' => 1], 'integer does not match [string,bool] type');
    }

    public function testOperatorMatchesEntity(): void
    {
        $entityMap = new EntityMap();
        $entityMap->set('integer', 1);
        $entityMap->set('object', ['y' => 1]);

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
    }

    public function testOperatorMatchesHexBytes(): void
    {
        $c = new Matches(['$$matchesHexBytes' => 'DEADBEEF']);
        $this->assertResult(true, $c, hex2bin('DEADBEEF'), 'value matches hex bytes (root-level)');
        $this->assertResult(false, $c, hex2bin('90ABCDEF'), 'value does not match hex bytes (root-level)');

        $c = new Matches(['x' => ['$$matchesHexBytes' => '90ABCDEF']]);
        $this->assertResult(true, $c, ['x' => hex2bin('90ABCDEF')], 'value matches hex bytes (embedded)');
        $this->assertResult(false, $c, ['x' => hex2bin('DEADBEEF')], 'value does not match hex bytes (embedded)');
    }

    public function testOperatorUnsetOrMatches(): void
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

    public function testOperatorSessionLsid(): void
    {
        if (version_compare($this->getFeatureCompatibilityVersion(), '3.6', '<')) {
            $this->markTestSkipped('startSession() is only supported on FCV 3.6 or higher');
        }

        $session = $this->manager->startSession();

        $entityMap = new EntityMap();
        $entityMap->set('session', $session);

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
    public function testErrorMessages($expectedMessageRegex, Matches $constraint, $actualValue): void
    {
        try {
            $constraint->evaluate($actualValue);
            $this->fail('Expected a comparison failure');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString('Failed asserting that expected value matches actual value.', $e->getMessage());
            $this->assertMatchesRegularExpression($expectedMessageRegex, $e->getMessage());
        }
    }

    public function errorMessageProvider()
    {
        return [
            'assertEquals: type check (root-level)' => [
                '#bool(ean)? is not expected type "string"#',
                new Matches('foo'),
                true,
            ],
            'assertEquals: type check (embedded)' => [
                '#Field path "x": bool(ean)? is not expected type "string"#',
                new Matches(['x' => 'foo']),
                ['x' => true],
            ],
            'assertEquals: comparison failure (root-level)' => [
                '#' . preg_quote('Failed asserting that two strings are equal.', '#') . '#',
                new Matches('foo'),
                'bar',
            ],
            'assertEquals: comparison failure (embedded)' => [
                '#' . preg_quote('Field path "x": Failed asserting that two strings are equal.', '#') . '#',
                new Matches(['x' => 'foo']),
                ['x' => 'bar'],
            ],
            'assertMatchesArray: type check (root-level)' => [
                '#' . preg_quote('MongoDB\Model\BSONDocument is not instance of expected class "MongoDB\Model\BSONArray"', '#') . '#',
                new Matches([1, 2, 3]),
                ['x' => 1],
            ],
            'assertMatchesArray: type check (embedded)' => [
                '#Field path "x": int(eger)? is not instance of expected class "MongoDB\\\\Model\\\\BSONArray"#',
                new Matches(['x' => [1, 2, 3]]),
                ['x' => 1],
            ],
            'assertMatchesArray: count check (root-level)' => [
                '#' . preg_quote('$actual count is 2, expected 3', '#') . '#',
                new Matches(['x' => [1, 2, 3]]),
                ['x' => [1, 2]],
            ],
            'assertMatchesArray: count check (embedded)' => [
                '#' . preg_quote('Field path "x": $actual count is 2, expected 3', '#') . '#',
                new Matches(['x' => [1, 2, 3]]),
                ['x' => [1, 2]],
            ],
            'assertMatchesDocument: type check (root-level)' => [
                '#int(eger)? is not instance of expected class "MongoDB\\\\Model\\\\BSONDocument"#',
                new Matches(['x' => 1]),
                1,
            ],
            'assertMatchesDocument: type check (embedded)' => [
                '#Field path "x": int(eger)? is not instance of expected class "MongoDB\\\\Model\\\\BSONDocument"#',
                new Matches(['x' => ['y' => 1]]),
                ['x' => 1],
            ],
            'assertMatchesDocument: expected key missing (root-level)' => [
                '#' . preg_quote('$actual does not have expected key "x"', '#') . '#',
                new Matches(['x' => 1]),
                new stdClass(),
            ],
            'assertMatchesDocument: expected key missing (embedded)' => [
                '#' . preg_quote('Field path "x": $actual does not have expected key "y"', '#') . '#',
                new Matches(['x' => ['y' => 1]]),
                ['x' => new stdClass()],
            ],
            'assertMatchesDocument: unexpected key present (embedded)' => [
                '#' . preg_quote('Field path "x": $actual has unexpected key "y', '#') . '#',
                new Matches(['x' => new stdClass()]),
                ['x' => ['y' => 1]],
            ],
        ];
    }

    /**
     * @dataProvider operatorErrorMessageProvider
     */
    public function testOperatorSyntaxValidation($expectedMessage, Matches $constraint): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $constraint->evaluate(['x' => 1], '', true);
    }

    public function operatorErrorMessageProvider()
    {
        return [
            '$$exists type' => [
                '$$exists requires bool',
                new Matches(['x' => ['$$exists' => 1]]),
            ],
            '$$type type (string)' => [
                '$$type requires string or string[]',
                new Matches(['x' => ['$$type' => 1]]),
            ],
            '$$type type (string[])' => [
                '$$type requires string or string[]',
                new Matches(['x' => ['$$type' => [1]]]),
            ],
            '$$matchesEntity requires EntityMap' => [
                '$$matchesEntity requires EntityMap',
                new Matches(['x' => ['$$matchesEntity' => 'foo']]),
            ],
            '$$matchesEntity type' => [
                '$$matchesEntity requires string',
                new Matches(['x' => ['$$matchesEntity' => 1]], new EntityMap()),
            ],
            '$$matchesHexBytes type' => [
                '$$matchesHexBytes requires string',
                new Matches(['$$matchesHexBytes' => 1]),
            ],
            '$$matchesHexBytes string format' => [
                '$$matchesHexBytes requires pairs of hex chars',
                new Matches(['$$matchesHexBytes' => 'f00']),
            ],
            '$$sessionLsid requires EntityMap' => [
                '$$sessionLsid requires EntityMap',
                new Matches(['x' => ['$$sessionLsid' => 'foo']]),
            ],
            '$$sessionLsid type' => [
                '$$sessionLsid requires string',
                new Matches(['x' => ['$$sessionLsid' => 1]], new EntityMap()),
            ],
        ];
    }

    private function assertResult($expected, Matches $constraint, $value, string $message = ''): void
    {
        $this->assertSame($expected, $constraint->evaluate($value, '', true), $message);
    }
}
