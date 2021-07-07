<?php

namespace MongoDB\Tests\SpecTests;

use stdClass;

use function array_diff;
use function basename;
use function file_get_contents;
use function glob;
use function is_array;
use function is_numeric;

/**
 * Command monitoring spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/command-monitoring
 */
class CommandMonitoringSpecTest extends FunctionalTestCase
{
    /**
     * Assert that the expected and actual command documents match.
     *
     * Note: this method may modify the $expected object.
     *
     * @param stdClass $expected Expected command document
     * @param stdClass $actual   Actual command document
     */
    public static function assertCommandMatches(stdClass $expected, stdClass $actual): void
    {
        if (isset($expected->getMore) && $expected->getMore === 42) {
            static::assertObjectHasAttribute('getMore', $actual);
            static::assertThat($actual->getMore, static::logicalOr(
                static::isInstanceOf(Int64::class),
                static::isType('integer')
            ));
            unset($expected->getMore);
        }

        if (isset($expected->killCursors) && isset($expected->cursors) && is_array($expected->cursors)) {
            static::assertObjectHasAttribute('cursors', $actual);
            static::assertIsArray($actual->cursors);

            foreach ($expected->cursors as $i => $cursorId) {
                static::assertArrayHasKey($i, $actual->cursors);

                if ($cursorId === 42) {
                    static::assertThat($actual->cursors[$i], static::logicalOr(
                        static::isInstanceOf(Int64::class),
                        static::isType('integer')
                    ));
                }
            }

            unset($expected->cursors);
        }

        static::assertDocumentsMatch($expected, $actual);
    }

    /**
     * Assert that the expected and actual command reply documents match.
     *
     * Note: this method may modify the $expectedReply object.
     *
     * @param stdClass $expected Expected command reply document
     * @param stdClass $actual   Actual command reply document
     */
    public static function assertCommandReplyMatches(stdClass $expected, stdClass $actual): void
    {
        if (isset($expected->cursor->id) && $expected->cursor->id === 42) {
            static::assertObjectHasAttribute('cursor', $actual);
            static::assertIsObject($actual->cursor);
            static::assertObjectHasAttribute('id', $actual->cursor);
            static::assertThat($actual->cursor->id, static::logicalOr(
                static::isInstanceOf(Int64::class),
                static::isType('integer')
            ));
            unset($expected->cursor->id);
        }

        if (isset($expected->cursorsUnknown) && is_array($expected->cursorsUnknown)) {
            static::assertObjectHasAttribute('cursorsUnknown', $actual);
            static::assertIsArray($actual->cursorsUnknown);

            foreach ($expected->cursorsUnknown as $i => $cursorId) {
                static::assertArrayHasKey($i, $actual->cursorsUnknown);

                if ($cursorId === 42) {
                    static::assertThat($actual->cursorsUnknown[$i], static::logicalOr(
                        static::isInstanceOf(Int64::class),
                        static::isType('integer')
                    ));
                }
            }

            unset($expected->cursorsUnknown);
        }

        if (isset($expected->ok) && is_numeric($expected->ok)) {
            static::assertObjectHasAttribute('ok', $actual);
            static::assertIsNumeric($actual->ok);
            static::assertEquals($expected->ok, $actual->ok);
            unset($expected->ok);
        }

        if (isset($expected->writeErrors) && is_array($expected->writeErrors)) {
            static::assertObjectHasAttribute('writeErrors', $actual);
            static::assertIsArray($actual->writeErrors);

            foreach ($expected->writeErrors as $i => $expectedWriteError) {
                static::assertArrayHasKey($i, $actual->writeErrors);
                $actualWriteError = $actual->writeErrors[$i];

                if (isset($expectedWriteError->code) && $expectedWriteError->code === 42) {
                    static::assertObjectHasAttribute('code', $actualWriteError);
                    static::assertThat($actualWriteError->code, static::logicalOr(
                        static::isInstanceOf(Int64::class),
                        static::isType('integer')
                    ));
                    unset($expected->writeErrors[$i]->code);
                }

                if (isset($expectedWriteError->errmsg) && $expectedWriteError->errmsg === '') {
                    static::assertObjectHasAttribute('errmsg', $actualWriteError);
                    static::assertIsString($actualWriteError->errmsg);
                    static::assertNotEmpty($actualWriteError->errmsg);
                    unset($expected->writeErrors[$i]->errmsg);
                }
            }
        }

        static::assertDocumentsMatch($expected, $actual);
    }

    /**
     * Execute an individual test case from the specification.
     *
     * @dataProvider provideTests
     * @param stdClass $test           Individual "tests[]" document
     * @param array    $data           Top-level "data" array to initialize collection
     * @param string   $databaseName   Name of database under test
     * @param string   $collectionName Name of collection under test
     */
    public function testCommandMonitoring(stdClass $test, array $data, ?string $databaseName = null, ?string $collectionName = null): void
    {
        $this->checkServerRequirements($this->createRunOn($test));

        $databaseName = $databaseName ?? $this->getDatabaseName();
        $collectionName = $collectionName ?? $this->getCollectionName();

        $context = Context::fromCommandMonitoring($test, $databaseName, $collectionName);
        $this->setContext($context);

        $this->dropTestAndOutcomeCollections();
        $this->insertDataFixtures($data);

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromCommandMonitoring($test->expectations);
            $commandExpectations->startMonitoring();
        }

        Operation::fromCommandMonitoring($test->operation)->assert($this, $context);

        if (isset($commandExpectations)) {
            $commandExpectations->stopMonitoring();
            $commandExpectations->assert($this, $context);
        }
    }

    public function provideTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/command-monitoring/*.json') as $filename) {
            $json = $this->decodeJson(file_get_contents($filename));
            $group = basename($filename, '.json');
            $data = $json->data ?? [];
            // phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
            $databaseName = $json->database_name ?? null;
            $collectionName = $json->collection_name ?? null;
            // phpcs:enable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $data, $databaseName, $collectionName];
            }
        }

        return $testArgs;
    }

    /**
     * Convert the server and topology requirements to a standard "runOn" array
     * used by other specifications.
     *
     * @param stdClass $test
     * @return array
     */
    private function createRunOn(stdClass $test): array
    {
        $req = new stdClass();

        $topologies = [
            self::TOPOLOGY_SINGLE,
            self::TOPOLOGY_REPLICASET,
            self::TOPOLOGY_SHARDED,
        ];

        // phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        /* Append ".99" as patch version, since command monitoring tests expect
         * the minor version to be an inclusive upper bound. */
        if (isset($test->ignore_if_server_version_greater_than)) {
            $req->maxServerVersion = $test->ignore_if_server_version_greater_than . '.99';
        }

        if (isset($test->ignore_if_server_version_less_than)) {
            $req->minServerVersion = $test->ignore_if_server_version_less_than;
        }

        if (isset($test->ignore_if_topology_type)) {
            $req->topology = array_diff($topologies, $test->ignore_if_topology_type);
        }

        // phpcs:enable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

        return [$req];
    }
}
