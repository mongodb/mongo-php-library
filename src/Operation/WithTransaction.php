<?php

namespace MongoDB\Operation;

use Closure;
use Exception;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Session;
use Throwable;

use function call_user_func;
use function floor;
use function hrtime;
use function min;
use function random_int;
use function usleep;

/** @internal */
final class WithTransaction
{
    /** Initial backoff time in ms */
    private const BACKOFF_INITIAL = 5;

    /** Maximum backoff time in ms */
    private const BACKOFF_MAX = 500;

    /** Default transaction timeout in seconds */
    private const MAX_TIME = 120;

    /** @var callable */
    private $callback;

    /**
     * Used to inject a custom jitter generator for tests
     *
     * @var (Closure():float)|null
     */
    private ?Closure $jitterGenerator = null;

    /**
     * @see Session::startTransaction for supported transaction options
     *
     * @param callable $callback           A callback that will be invoked within the transaction
     * @param array    $transactionOptions Additional options that are passed to Session::startTransaction
     */
    public function __construct(callable $callback, private array $transactionOptions = [])
    {
        $this->callback = $callback;
    }

    /**
     * Execute the operation in the given session
     *
     * This helper takes care of retrying the commit operation or the entire
     * transaction if an error occurs.
     *
     * If the commit fails because of an UnknownTransactionCommitResult error, the
     * commit is retried without re-invoking the callback.
     * If the commit fails because of a TransientTransactionError, the entire
     * transaction will be retried. In this case, the callback will be invoked
     * again. It is important that the logic inside the callback is idempotent.
     *
     * In case of failures, the commit or transaction are retried until 120 seconds
     * from the initial call have elapsed. After that, no retries will happen and
     * the helper will throw the last exception received from the driver.
     *
     * @see Client::startSession
     *
     * @param Session $session A session object as retrieved by Client::startSession
     * @throws RuntimeException for driver errors while committing the transaction
     * @throws Exception for any other errors, including those thrown in the callback
     */
    public function execute(Session $session): void
    {
        $startTime = hrtime(true);
        $transactionAttempt = 0;

        while (true) {
            $transactionAttempt++;
            $session->startTransaction($this->transactionOptions);

            try {
                call_user_func($this->callback, $session);
            } catch (Throwable $e) {
                // If this method returns, this means we're able to retry the entire transaction.
                $this->checkForRetryableError($session, $e, $startTime, $transactionAttempt);

                continue;
            }

            if (! $session->isInTransaction()) {
                // Assume callback intentionally ended the transaction
                return;
            }

            // Commit the transaction and return if it was committed successfully
            if ($this->commitTransaction($session, $startTime, $transactionAttempt)) {
                return;
            }
        }
    }

    /**
     * Handles the backoff logic for retrying transactions according to the backpressure spec
     *
     * This method will throw if backing off would cause the total transaction time to exceed the limit. In other cases,
     * it will simply sleep for the appropriate amount of time and return, allowing withTransaction to continue the
     * transaction loop.
     *
     * @param Throwable $e                  The exception that's causing the backoff. This exception will be thrown if we're unable to back off
     * @param int       $startTime          The time the transaction loop was started
     * @param int       $transactionAttempt The current transaction attempt number, used to compute the backoff time according to the backpressure spec
     */
    private function backoff(Throwable $e, int $startTime, int $transactionAttempt): void
    {
        $backoffMs = $this->computeBackoffMs($transactionAttempt);

        // If backing off for the computed time would exceed the total transaction time limit, do not back off and
        // throw the original error to break out of the transaction loop instead
        if ($this->isTransactionTimeLimitExceeded($startTime, $backoffMs)) {
            throw $e;
        }

        usleep($backoffMs * 1000);
    }

    /**
     * Checks if the given exception is an error that allows for retrying the transaction
     *
     * This method is called when an error happens during the transaction callback. If the error is not retryable, or if
     * the time limit for retries has been exceeded, it re-throws the caught exception to break out of the transaction
     * loop. In other cases, it returns without throwing.
     */
    private function checkForRetryableError(Session $session, Throwable $e, int $startTime, int $transactionAttempt): void
    {
        if ($session->isInTransaction()) {
            $session->abortTransaction();
        }

        if (
            $e instanceof RuntimeException &&
            $e->hasErrorLabel('TransientTransactionError') &&
            ! $this->isTransactionTimeLimitExceeded($startTime)
        ) {
            // Before retrying the transaction, back off according to the backpressure spec. This will throw if we're
            // unable to back off.
            $this->backoff($e, $startTime, $transactionAttempt);

            return;
        }

        throw $e;
    }

    /**
     * Attempts to commit the transaction until it either succeeds, encounters a non-retryable error, or exceeds the
     * time limit for retries
     *
     * This method attempts to commit the transaction, retrying the commit if an unknown commit result was encountered.
     * If the transaction was committed successfully, it returns true. If a transient transaction error has occurred and
     * the time limit for retries has not been exceeded, it returns false to indicate that the entire transaction should
     * be retried. If a non-retryable error is encountered, or if the time limit for retries has been exceeded, it
     * throws the last exception encountered to break out of the transaction loop.
     *
     * @return bool Returns true if the transaction was successfully committed, or false if the transaction should be retried
     * @throws Throwable if an error occurs while committing the transaction that should not be retried
     */
    private function commitTransaction(Session $session, int $startTime, int $transactionAttempt): bool
    {
        while (true) {
            try {
                $session->commitTransaction();
            } catch (RuntimeException $e) {
                if (
                    $e->getCode() !== 50 /* MaxTimeMSExpired */ &&
                    $e->hasErrorLabel('UnknownTransactionCommitResult') &&
                    ! $this->isTransactionTimeLimitExceeded($startTime)
                ) {
                    // Retry committing the transaction
                    continue;
                }

                if (
                    $e->hasErrorLabel('TransientTransactionError') &&
                    ! $this->isTransactionTimeLimitExceeded($startTime)
                ) {
                    // Before restarting the transaction, attempt to back off. This will throw if we're unable to back
                    // off.
                    $this->backoff($e, $startTime, $transactionAttempt);

                    // Indicate that we can retry the transaction
                    return false;
                }

                throw $e;
            }

            // Commit was successful, indicate to break out of the transaction loop
            return true;
        }
    }

    private function computeBackoffMs(int $transactionAttempt): int
    {
        return (int) floor($this->getJitter() * min(self::BACKOFF_INITIAL * (1.5 ** ($transactionAttempt - 1)), self::BACKOFF_MAX));
    }

    private function getJitter(): float
    {
        if ($this->jitterGenerator !== null) {
            return ($this->jitterGenerator)();
        }

        // Jitter is a random float from [0, 1]
        // 2 ** 53 is the largest integer that can be represented in a float without losing precision
        return random_int(0, 2 ** 53) / 2 ** 53;
    }

    /**
     * Returns whether the time limit for retrying transactions in the convenient transaction API has passed
     *
     * @param int $startTime The time the transaction loop was started
     * @param int $backoffMs The amount of time that will be spent backing off before the next retry attempt, in milliseconds
     */
    private function isTransactionTimeLimitExceeded(int $startTime, int $backoffMs = 0): bool
    {
        // hrtime returns nanoseconds, so convert the backoff and maximum time accordingly
        return hrtime(true) + ($backoffMs * 1e6) - $startTime >= self::MAX_TIME * 1e9;
    }
}
