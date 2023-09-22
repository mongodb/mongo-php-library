<?php

namespace MongoDB\Benchmark\DriverBench\Amp;

use Amp\Cancellation;
use Amp\Parallel\Worker\Task;
use Amp\Sync\Channel;
use MongoDB\Benchmark\DriverBench\ParallelMultiFileExportBench;

final class ExportFileTask implements Task
{
    public function __construct(
        private string|array $files,
        private array $filter = [],
        private array $options = [],
    ) {
    }

    public function run(Channel $channel, Cancellation $cancellation): mixed
    {
        ParallelMultiFileExportBench::exportFile($this->files, $this->filter, $this->options);

        return $this->files;
    }
}
