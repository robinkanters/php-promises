<?php

use RobinKanters\Promises\Promise\AbstractPromise;
use RobinKanters\Promises\Promise\PromiseQueue;

class PromiseQueueTest extends PHPUnit_Framework_TestCase
{
    /* @var ReflectionProperty */
    private $queueField;

    public function setUp()
    {
        parent::setUp();
        $this->queueField = new \ReflectionProperty('\\RobinKanters\\Promises\\Promise\\PromiseQueue', 'queue');
        $this->queueField->setAccessible(true);
    }

    public function testAdd()
    {
        $promise = $this->getMockPromise();

        $queue = new PromiseQueue();
        $queue->add($promise);

        $actualQueue = $this->queueField->getValue($queue);

        $this->assertEquals([$promise], $actualQueue);
    }

    public function testAddAll()
    {
        $promises = [
            $this->getMockPromise(),
            $this->getMockPromise(),
            $this->getMockPromise(),
        ];

        $queue = new PromiseQueue();
        $queue->addAll($promises);

        $actualQueue = $this->queueField->getValue($queue);

        $this->assertEquals($promises, $actualQueue);
    }

    /**
     * @return AbstractPromise
     */
    protected function getMockPromise()
    {
        return $this->getMockForAbstractClass('\\RobinKanters\\Promises\\Promise\\AbstractPromise');
    }
}
