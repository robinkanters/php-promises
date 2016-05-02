<?php

namespace RobinKanters\Promises\Promise;

interface PromiseQueueInterface
{
    /* @return AbstractPromise */
    public function takeOne();

    /**
     * @param int $amount How many items to take.
     *
     * @return \Generator|AbstractPromise[]
     */
    public function take($amount);

    /* @return int */
    public function length();
}