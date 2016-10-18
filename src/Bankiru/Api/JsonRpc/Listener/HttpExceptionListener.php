<?php

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\Exception\InvalidRequestException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HttpExceptionListener
{
    private $debug = false;

    /**
     * ExceptionHandlerListener constructor.
     *
     * @param bool $debug
     */
    public function __construct($debug)
    {
        $this->debug = (bool)$debug;
    }

    public function onJsonRpcException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof InvalidRequestException) {
            $event->setException(new BadRequestHttpException($exception->getMessage(), $exception));
        }
    }
}
