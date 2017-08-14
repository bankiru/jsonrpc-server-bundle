<?php

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\Exception\InvalidRequestException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class HttpExceptionListener
{
    public function onJsonRpcException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof InvalidRequestException) {
            $event->setException(new BadRequestHttpException($exception->getMessage(), $exception));
        }
    }
}
