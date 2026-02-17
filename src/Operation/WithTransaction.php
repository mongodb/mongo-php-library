<?php

namespace MongoDB\Operation;

use Exception;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Session;
use Throwable;

use function call_user_func;
use function time;

/** @internal */
final class WithTransaction
{
    /** @var callable */
    private $callback;

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
        $startTime = time();

        while (true) {
            $session->startTransaction($this->transactionOptions);

            try {
                call_user_func($this->callback, $session);
            } catch (Throwable $e) {
                // If this method returns, this means we're able to retry the entire transaction.
                $this->checkForRetryableError($session, $e, $startTime);

                continue;
            }

            if (! $session->isInTransaction()) {
                // Assume callback intentionally ended the transaction
                return;
            }

            // Commit the transaction and return if it was committed successfully
            if ($this->commitTransaction($session, $startTime)) {
                return;
            }
        }
    }

    /**
     * Checks if the given exception is an error that allows for retrying the connection
     *
     * This method is called when an error happens during the transaction callback. If the error is not retryable, or if
     * the time limit for retries has been exceeded, it re-throws the caught exception to break out of the transaction
     * loop. In other cases, it returns without throwing.
     */
    private function checkForRetryableError(Session $session, Throwable $e, int $startTime): void
    {
        if ($session->isInTransaction()) {
            $session->abortTransaction();
        }

        if (
            $e instanceof RuntimeException &&
            $e->hasErrorLabel('TransientTransactionError') &&
            ! $this->isTransactionTimeLimitExceeded($startTime)
        ) {
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
     * be retried. If a none-retryable error is encountered, or if the time limit for retries has been exceeded, it
     * throws the last exception encountered to break out of the transaction loop.
     *
     * @return bool Returns true if the transaction was successfully committed, or false if the transaction should be retried
     * @throws Throwable if an error occurs while committing the transaction that should not be retried
     */
    private function commitTransaction(Session $session, int $startTime): bool
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
                    // Restart the transaction, invoking the callback again
                    return false;
                }

                throw $e;
            }

            // Commit was successful, indicate to break out of the transaction loop
            return true;
        }
    }

    /**
     * Returns whether the time limit for retrying transactions in the convenient transaction API has passed
     *
     * @param int $startTime The time the transaction was started
     */
    private function isTransactionTimeLimitExceeded(int $startTime): bool
    {
        return time() - $startTime >= 120;
    }
}
