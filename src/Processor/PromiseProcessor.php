<?php

namespace RobinKanters\Promises\Processor;

use RobinKanters\Promises\Promise\AbstractPromise;
use RobinKanters\Promises\Promise\PromiseQueueInterface;

class PromiseProcessor
{
    const MAX_CONCURRENT_TASKS = 30;
    const WAIT_TIMEOUT = 10;

    public function processQueue(PromiseQueueInterface $queue, $maxConcurrent = self::MAX_CONCURRENT_TASKS, $pidWaitTimeout = self::WAIT_TIMEOUT)
    {
        while ($queue->length() > 0) {
            $promises = $queue->take($maxConcurrent);

            $this->deliverPromises($promises, $pidWaitTimeout);
        }
    }

    /**
     * @param $promises
     * @param $pidWaitTimeout
     */
    protected function deliverPromises($promises, $pidWaitTimeout)
    {
        $pids = [];
        foreach ($promises as $promise) {
            $pid = $this->tryDeliverAsync($promise, $pidWaitTimeout);
            if (!is_null($pid)) $pids[] = $pid;
        }

        $this->waitPids($pids);
        echo PHP_EOL;
    }

    private function tryDeliverAsync(AbstractPromise $promise, $pidWaitTimeout)
    {
        $pid = -1;

        if (function_exists('pcntl_fork'))
            $pid = pcntl_fork();

        if ($this->forkFailed($pid)) {
            // could not fork, deliver synchronously
            $promise->deliver();

            return null;
        }

        if ($pid) // parent
            return $pid;
        else // child
            $this->deliverPromiseAsync($promise, $pidWaitTimeout);

        return null;
    }

    /**
     * @param $pid
     *
     * @return bool
     */
    protected function forkFailed($pid)
    {
        return $pid == -1;
    }

    /**
     * Deliver the promise with a timeout of $timeout seconds.
     *
     * @param AbstractPromise $promise
     * @param int             $timeout amount of seconds after which to exit the child process.
     */
    private function deliverPromiseAsync(AbstractPromise $promise, $timeout)
    {
        try {
            pcntl_alarm($timeout);

            $promise->deliver();

            exit(0); // this is only for child forks
        } catch (\Exception $e) {
            exit(-1); // this is only for child forks
        }
    }

    /**
     * Wait for PIDs to exit.
     *
     * @param int[] $pids PIDs to wait for.
     *
     * @return void
     */
    protected function waitPids($pids)
    {
        foreach ($pids as $pid) while (true) {
            $code = pcntl_waitpid($pid, $status, WNOHANG);

            if ($code <> 0) break;
        }
    }
}
