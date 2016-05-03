<?php

namespace RobinKanters\Promises\Interfaces;

use RobinKanters\Promises\Promise\AbstractPromise;

interface PromiseExceptionListenerInterface
{
    /**
     * @param AbstractPromise $promise   The promise that threw the exception.
     * @param \Exception      $exception The thrown exception.
     */
    public function onDeliveryException(AbstractPromise $promise, \Exception $exception);
}
