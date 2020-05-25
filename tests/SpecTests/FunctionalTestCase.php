<?php

namespace MongoDB\Tests\SpecTests;

use ArrayIterator;
use IteratorIterator;
use LogicException;
use MongoDB\Collection;
use MongoDB\Driver\Server;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;
use MultipleIterator;
use PHPUnit\Framework\SkippedTest;
use stdClass;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use UnexpectedValueException;
use function in_array;
use function json_encode;
use function MongoDB\BSON\fromJSON;
use function MongoDB\BSON\toPHP;
use function sprintf;
use function version_compare;

/**
 * Base class for spec test runners.
 *
 * @see https://github.com/mongodb/specifications
 */
class FunctionalTestCase extends BaseFunctionalTestCase
{
    use SetUpTearDownTrait;

    const TOPOLOGY_SINGLE = 'single';
    const TOPOLOGY_REPLICASET = 'replicaset';
    const TOPOLOGY_SHARDED = 'sharded';

    /** @var Context|null */
    private $context;

    private function doSetUp()
    {
        parent::setUp();

        $this->context = null;
    }

    private function doTearDown()
    {
        $this->context = null;

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
    public static function assertCommandMatches(stdClass $expected, stdClass $actual)
    {
        throw new LogicException(sprintf('%s does not assert CommandStartedEvents', static::class));
    }

    /**
     * Assert that the expected and actual command reply documents match.
     *
     * Note: Spec tests that do not assert command started events may throw an
     * exception in lieu of implementing this method.
     *
     * @param stdClass $expected Expected command reply document
     * @param stdClass $actual   Actual command reply document
     */
    public static function assertCommandReplyMatches(stdClass $expected, stdClass $actual)
    {
        throw new LogicException(sprintf('%s does not assert CommandSucceededEvents', static::class));
    }

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
     * @param int   $resultExpectation
     */
    protected function assertOutcomeCollectionData(array $expectedDocuments, $resultExpectation = ResultExpectation::ASSERT_SAME_DOCUMENT)
    {
        $outcomeCollection = $this->getOutcomeCollection($this->getContext()->outcomeReadOptions);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($expectedDocuments));
        $mi->attachIterator(new IteratorIterator($outcomeCollection->find([], ['sort' => ['_id' => 1]])));

        foreach ($mi as $documents) {
            list($expectedDocument, $actualDocument) = $documents;
            $this->assertNotNull($expectedDocument);
            $this->assertNotNull($actualDocument);

            switch ($resultExpectation) {
                case ResultExpectation::ASSERT_SAME_DOCUMENT:
                    $this->assertSameDocument($expectedDocument, $actualDocument);
                    break;

                case ResultExpectation::ASSERT_DOCUMENTS_MATCH:
                    $this->assertDocumentsMatch($expectedDocument, $actualDocument);
                    break;

                default:
                    $this->fail(sprintf('Invalid result expectation "%d" for %s', $resultExpectation, __METHOD__));
            }
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
            $minServerVersion = $req->minServerVersion ?? null;
            $maxServerVersion = $req->maxServerVersion ?? null;
            $topologies = $req->topology ?? null;

            if ($this->isServerRequirementSatisifed($minServerVersion, $maxServerVersion, $topologies)) {
                return;
            }
        }

        $serverVersion = $this->getServerVersion();
        $topology = $this->getTopology();

        $this->markTestSkipped(sprintf('Server version "%s" and topology "%s" do not meet test requirements: %s', $serverVersion, $topology, json_encode($runOn)));
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
        return toPHP(fromJSON($json));
    }

    /**
     * Return the test context.
     *
     * @return Context
     * @throws LogicException if the context has not been set
     */
    protected function getContext()
    {
        if (! $this->context instanceof Context) {
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

        if ($context->bucketName !== null) {
            $bucket = $context->getGridFSBucket($context->defaultWriteOptions);
            $bucket->drop();
        }

        $collection = null;
        if ($context->collectionName !== null) {
            $collection = $context->getCollection($context->defaultWriteOptions);
            $collection->drop();
        }

        if ($context->outcomeCollectionName !== null) {
            $outcomeCollection = $this->getOutcomeCollection($context->defaultWriteOptions);

            // Avoid redundant drop if the test and outcome collections are the same
            if ($collection === null || $outcomeCollection->getNamespace() !== $collection->getNamespace()) {
                $outcomeCollection->drop();
            }
        }
    }

    /**
     * Insert data fixtures into the test collection.
     *
     * @param array       $documents
     * @param string|null $collectionName
     */
    protected function insertDataFixtures(array $documents, $collectionName = null)
    {
        if (empty($documents)) {
            return;
        }

        $context = $this->getContext();
        $collection = $collectionName ? $context->selectCollection($context->databaseName, $collectionName) : $context->getCollection();

        $collection->insertMany($documents, $context->defaultWriteOptions);

        return;
    }

    private function getOutcomeCollection(array $collectionOptions = [])
    {
        $context = $this->getContext();

        // Outcome collection need not use the client under test
        return new Collection($this->manager, $context->databaseName, $context->outcomeCollectionName, $collectionOptions);
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
