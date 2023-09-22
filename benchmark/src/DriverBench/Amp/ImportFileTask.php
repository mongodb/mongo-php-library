<?php

namespace MongoDB\Benchmark\DriverBench\Amp;

use Amp\Cancellation;
use Amp\Parallel\Worker\Task;
use Amp\Sync\Channel;
use MongoDB\Benchmark\DriverBench\ParallelMultiFileImportBench;

final class ImportFileTask implements Task
{
    public function __construct(
        private array $files,
    ) {
    }

    public function run(Channel $channel, Cancellation $cancellation): mixed
    {
        ParallelMultiFileImportBench::importFile($this->files);

        return $this->files;
    }
}
