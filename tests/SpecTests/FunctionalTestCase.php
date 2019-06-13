<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;
use MongoDB\Tests\TestCase;
use PHPUnit\Framework\SkippedTest;
use ArrayIterator;
use InvalidArgumentException;
use IteratorIterator;
use LogicException;
use MultipleIterator;
use stdClass;
use UnexpectedValueException;

/**
 * Base class for spec test runners.
 *
 * @see https://github.com/mongodb/specifications
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    const TOPOLOGY_SINGLE = 'single';
    const TOPOLOGY_REPLICASET = 'replicaset';
    const TOPOLOGY_SHARDED = 'sharded';

    private $configuredFailPoints = [];
    private $context;

    public function setUp()
    {
        parent::setUp();

        $this->configuredFailPoints = [];
        $this->context = null;
    }

    public function tearDown()
    {
        $this->context = null;
        $this->disableFailPoints();

        parent::tearDown();
    }

    /**
     * Assert that the expected and actual command documents match.
     *
     * Note: Spec tests that do not assert command started events may throw an
     * exception in lieu of implementing this method.
     *
     * @param stdClass $expectedCommand Expected command document
     * @param stdClass $actualCommand   Actual command document
     */
    abstract public static function assertCommandMatches(stdClass $expected, stdClass $actual);

    /**
     * Assert that the expected and actual command reply documents match.
     *
     * Note: Spec tests that do not assert command started events may throw an
     * exception in lieu of implementing this method.
     *
     * @param stdClass $expected Expected command reply document
     * @param stdClass $actual   Actual command reply document
     */
    abstract public static function assertCommandReplyMatches(stdClass $expected, stdClass $actual);

    /**
     * Asserts that two given documents match.
     *
     * Extra keys in the actual value's document(s) will be ignored.
     *
     * @param array|object $expectedDocument
     * @param array|object $actualDocument
     * @param string       $message
     */
    protected static function assertDocumentsMatch($expectedDocument, $actualDocument, $message = '')
    {
        $constraint = new DocumentsMatchConstraint($expectedDocument, true, true);

        static::assertThat($actualDocument, $constraint, $message);
    }

    /**
     * Assert data within the outcome collection.
     *
     * @param array $expectedDocuments
     */
    protected function assertOutcomeCollectionData(array $expectedDocuments)
    {
        $outcomeCollection = $this->getOutcomeCollection();
        $findOptions = $this->getContext()->outcomeFindOptions;

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($expectedDocuments));
        $mi->attachIterator(new IteratorIterator($outcomeCollection->find([], $findOptions)));

        foreach ($mi as $documents) {
            list($expectedDocument, $actualDocument) = $documents;
            $this->assertNotNull($expectedDocument);
            $this->assertNotNull($actualDocument);
            $this->assertSameDocument($expectedDocument, $actualDocument);
        }
    }

    /**
     * Checks server version and topology requirements.
     *
     * @param array $runOn
     * @throws SkippedTest if the server requirements are not met
     */
    protected function checkServerRequirements(array $runOn)
    {
        foreach ($runOn as $req) {
            $minServerVersion = isset($req->minServerVersion) ? $req->minServerVersion : null;
            $maxServerVersion = isset($req->maxServerVersion) ? $req->maxServerVersion : null;
            $topologies = isset($req->topology) ? $req->topology : null;

            if ($this->isServerRequirementSatisifed($minServerVersion, $maxServerVersion, $topologies)) {
                return;
            }
        }

        $serverVersion = $this->getServerVersion();
        $topology = $this->getTopology();

        $this->markTestSkipped(sprintf('Server version "%s" and topology "%s" do not meet test requirements: %s', $serverVersion, $topology, json_encode($runOn)));
    }

    /**
     * Configure a fail point for the test.
     *
     * The fail point will automatically be disabled during tearDown() to avoid
     * affecting a subsequent test.
     *
     * @param stdClass $command configureFailPoint command document
     * @throws InvalidArgumentException if $command is not a configureFailPoint command
     */
    protected function configureFailPoint(stdClass $command)
    {
        if (key($command) !== 'configureFailPoint') {
            throw new InvalidArgumentException('$command is not a configureFailPoint command');
        }

        $database = new Database($this->manager, 'admin');
        $cursor = $database->command($command);
        $result = $cursor->toArray()[0];

        $this->assertCommandSucceeded($result);

        // Record the fail point so it can be disabled during tearDown()
        $this->configuredFailPoints[] = $command->configureFailPoint;
    }

    /**
     * Decode a JSON spec test.
     *
     * This decodes the file through the driver's extended JSON parser to ensure
     * proper handling of special types.
     *
     * @param string $json
     * @return array
     */
    protected function decodeJson($json)
    {
        return \MongoDB\BSON\toPHP(\MongoDB\BSON\fromJSON($json));
    }

    /**
     * Return the test context.
     *
     * @return Context
     * @throws LogicException if the context has not been set
     */
    protected function getContext()
    {
        if (!$this->context instanceof Context) {
            throw new LogicException('Context has not been set');
        }

        return $this->context;
    }

    /**
     * Set the test context.
     *
     * @param Context $context
     */
    protected function setContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Drop the test and outcome collections by dropping them.
     */
    protected function dropTestAndOutcomeCollections()
    {
        $context = $this->getContext();

        $collection = $context->getCollection();
        $collection->drop($context->defaultWriteOptions);

        $outcomeCollection = $this->getOutcomeCollection();

        // Avoid redundant drop if the test and outcome collections are the same
        if ($outcomeCollection->getNamespace() !== $collection->getNamespace()) {
            $outcomeCollection->drop($context->defaultWriteOptions);
        }
    }

    /**
     * Insert data fixtures into the test collection.
     *
     * @param array $documents
     */
    protected function insertDataFixtures(array $documents)
    {
        if (empty($documents)) {
            return;
        }

        $context = $this->getContext();
        $collection = $context->getCollection();
        $collection->insertMany($documents, $context->defaultWriteOptions);
    }

    private function getOutcomeCollection()
    {
        $context = $this->getContext();

        // Outcome collection need not use the client under test
        return new Collection($this->manager, $context->databaseName, $context->outcomeCollectionName);
    }

    /**
     * Disables any fail points that were configured earlier in the test.
     *
     * This tracks fail points set via configureFailPoint() and should be called
     * during tearDown().
     */
    private function disableFailPoints()
    {
        $database = new Database($this->manager, 'admin');

        foreach ($this->configuredFailPoints as $failPoint) {
            $database->command(['configureFailPoint' => $failPoint, 'mode' => 'off']);
        }
    }

    /**
     * Return the corresponding topology constants for the current topology.
     *
     * @return string
     * @throws UnexpectedValueException if topology is neither single nor RS nor sharded
     */
    private function getTopology()
    {
        $topologyTypeMap = [
            Server::TYPE_STANDALONE => self::TOPOLOGY_SINGLE,
            Server::TYPE_RS_PRIMARY => self::TOPOLOGY_REPLICASET,
            Server::TYPE_MONGOS => self::TOPOLOGY_SHARDED,
        ];

        $primaryType = $this->getPrimaryServer()->getType();

        if (isset($topologyTypeMap[$primaryType])) {
            return $topologyTypeMap[$primaryType];
        }

        throw new UnexpectedValueException('Toplogy is neither single nor RS nor sharded');
    }

    /**
     * Checks if server version and topology requirements are satifised.
     *
     * @param string|null $minServerVersion
     * @param string|null $maxServerVersion
     * @param array|null  $topologies
     * @return boolean
     */
    private function isServerRequirementSatisifed($minServerVersion, $maxServerVersion, array $topologies = null)
    {
        $serverVersion = $this->getServerVersion();

        if (isset($minServerVersion) && version_compare($serverVersion, $minServerVersion, '<')) {
            return false;
        }

        if (isset($maxServerVersion) && version_compare($serverVersion, $maxServerVersion, '>')) {
            return false;
        }

        $topology = $this->getTopology();

        if (isset($topologies) && ! in_array($topology, $topologies)) {
            return false;
        }

        return true;
    }
}
