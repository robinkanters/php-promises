<?php

namespace RobinKanters\Promises\Promise;

use RobinKanters\Promises\Interfaces\PromiseExceptionListenerInterface;

abstract class AbstractPromise
{
    /* @var PromiseExceptionListenerInterface[] */
    protected $exceptionCallbacks = [];

    public final function deliver()
    {
        try {
            $this->doDeliver();
        } catch (\Exception $e) {
            // catches all exceptions and forwards them to the listeners
            $this->callbackDeliverException($e);
        }
    }

    protected abstract function doDeliver();

    public function addExceptionCallback(PromiseExceptionListenerInterface $callback)
    {
        $this->exceptionCallbacks[] = $callback;
    }

    private function callbackDeliverException(\Exception $exception)
    {
        /* @var PromiseExceptionListenerInterface $exceptionCallback */
        foreach ($this->exceptionCallbacks as $exceptionCallback) {
            $exceptionCallback->onDeliveryException($this, $exception);
        }
    }
}