<?php

namespace RobinKanters\Promises\Promise;

class PromiseQueue implements PromiseQueueInterface
{
    /* @var AbstractPromise[] */
    private $queue = [];

    /**
     * @param AbstractPromise[] $promises
     */
    public function addAll(array $promises)
    {
        array_walk($promises, function (AbstractPromise $p) {
            $this->add($p);
        });
    }

    public function add(AbstractPromise $promise)
    {
        array_push($this->queue, $promise);
    }

    /**
     * @param int $amount How many items to take.
     *
     * @return \Generator|AbstractPromise[]
     */
    public function take($amount)
    {
        $i = 0;
        while (++$i <= $amount && $item = $this->takeOne()) {
            if (is_null($item)) break;
            yield $item;
        }
    }

    /* @return AbstractPromise */
    public function takeOne()
    {
        return array_pop($this->queue);
    }

    /* @return int */
    public function length()
    {
        return count($this->queue);
    }
}
