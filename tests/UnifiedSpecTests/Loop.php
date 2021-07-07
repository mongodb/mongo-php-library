<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use Error;
use MongoDB\Model\BSONArray;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\IncompleteTest;
use PHPUnit\Framework\SkippedTest;
use PHPUnit\Framework\Warning;
use Throwable;

use function array_key_exists;
use function call_user_func;
use function microtime;
use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsString;
use function usleep;

final class Loop
{
    /** @var boolean */
    private static $allowIteration = true;

    /** @var integer */
    private static $sleepUsecBetweenIterations = 0;

    /** @var Context */
    private $context;

    /** @var array */
    private $operations = [];

    /** @var BSONArray */
    private $errorList;

    /** @var BSONArray */
    private $failureList;

    /** @var string */
    private $numSuccessfulOperationsEntityId;

    /** @var string */
    private $numIterationsEntityId;

    public function __construct(array $operations, Context $context, array $options = [])
    {
        assertContainsOnly(Operation::class, $operations);

        $this->operations = $operations;
        $this->context = $context;

        foreach (['storeErrorsAsEntity', 'storeFailuresAsEntity', 'storeSuccessesAsEntity', 'storeIterationsAsEntity'] as $option) {
            if (array_key_exists($option, $options)) {
                assertIsString($options[$option]);
            }
        }

        $errorListEntityId = $options['storeErrorsAsEntity'] ?? ($options['storeFailuresAsEntity'] ?? null);
        $failureListEntityId = $options['storeFailuresAsEntity'] ?? ($options['storeErrorsAsEntity'] ?? null);

        if (isset($errorListEntityId)) {
            $this->errorList = $this->initializeListEntity($errorListEntityId);
        }

        if (isset($failureListEntityId)) {
            $this->failureList = $this->initializeListEntity($failureListEntityId);
        }

        $this->numSuccessfulOperationsEntityId = $options['storeSuccessesAsEntity'] ?? null;
        $this->numIterationsEntityId = $options['storeIterationsAsEntity'] ?? null;
    }

    public function execute(): void
    {
        assertFalse($this->context->isInLoop(), 'Nested loops are unsupported');

        $numIterations = 0;
        $numSuccessfulOperations = 0;

        $callback = function () use (&$numSuccessfulOperations): void {
            foreach ($this->operations as $operation) {
                $operation->assert();
                $numSuccessfulOperations++;
            }
        };

        $this->context->setInLoop(true);

        try {
            while (self::$allowIteration) {
                $numIterations++;
                try {
                    call_user_func($callback);
                } catch (Throwable $e) {
                    /* Allow internal PHP errors and certain PHPUnit exceptions
                     * to interrupt the loop, as they are not expected here. */
                    if ($e instanceof Error || $e instanceof IncompleteTest || $e instanceof SkippedTest || $e instanceof Warning) {
                        throw $e;
                    }

                    $this->handleErrorOrFailure($e);
                }

                if (self::$sleepUsecBetweenIterations > 0) {
                    usleep(self::$sleepUsecBetweenIterations);
                }
            }
        } finally {
            $this->context->setInLoop(false);

            $entityMap = $this->context->getEntityMap();

            if (isset($this->numSuccessfulOperationsEntityId)) {
                $entityMap->set($this->numSuccessfulOperationsEntityId, $numSuccessfulOperations);
            }

            if (isset($this->numIterationsEntityId)) {
                $entityMap->set($this->numIterationsEntityId, $numIterations);
            }
        }
    }

    /**
     * Allow or prohibit loop operations from starting a new iteration.
     *
     * This function is primarily used by the Atlas testing workload executor.
     */
    public static function allowIteration(bool $allowIteration = true): void
    {
        self::$allowIteration = $allowIteration;
    }

    /**
     * Set time to sleep between iterations.
     *
     * This can be used to limit CPU usage during workload execution.
     */
    public static function setSleepUsecBetweenIterations(int $usec): void
    {
        assertGreaterThanOrEqual(0, $usec);

        self::$sleepUsecBetweenIterations = $usec;
    }

    private function handleErrorOrFailure(Throwable $e): void
    {
        /* The constructor will either initialize both lists or leave them both
         * unset. If unset, exceptions should not be logged and instead
         * interrupt the loop. */
        if (! isset($this->errorList, $this->failureList)) {
            throw $e;
        }

        /* Failures and errors are differentiated according to the logic in
         * PHPUnit\Framework\TestCase::runBare(). Other PHPUnit exceptions have
         * already been excluded by logic in execute(). */
        $list = $e instanceof AssertionFailedError ? $this->failureList : $this->errorList;

        $list->append([
            'error' => $e->getMessage(),
            'time' => microtime(true),
        ]);
    }

    private function initializeListEntity(string $id): BSONArray
    {
        $entityMap = $this->context->getEntityMap();

        if (! $entityMap->offsetExists($id)) {
            $entityMap->set($id, new BSONArray());
        }

        assertInstanceOf(BSONArray::class, $entityMap[$id]);

        return $entityMap[$id];
    }
}
