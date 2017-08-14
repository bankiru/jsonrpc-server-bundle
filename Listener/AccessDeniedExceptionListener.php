<?php

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\Exception\JsonRpcException;
use Bankiru\Api\Rpc\Event\GetExceptionResponseEvent;
use ScayTrase\Api\JsonRpc\JsonRpcError;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class AccessDeniedExceptionListener
{
    public function onRpcException(GetExceptionResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof JsonRpcRequestInterface) {
            return;
        }

        $exception = $event->getException();

        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        $event->setException(
            JsonRpcException::create(
                JsonRpcError::METHOD_NOT_FOUND,
                $exception->getMessage(),
                $exception->getTrace()
            )
        );
    }
}
