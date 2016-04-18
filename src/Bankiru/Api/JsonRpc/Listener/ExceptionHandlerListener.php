<?php
/**
 * User: scaytrase
 * Created: 2016-02-14 13:32
 */

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\Exception\JsonRpcExceptionInterface;
use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\Rpc\Event\GetExceptionResponseEvent;
use ScayTrase\Api\JsonRpc\JsonRpcError;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;

final class ExceptionHandlerListener
{
    private $debug = false;

    /**
     * ExceptionHandlerListener constructor.
     *
     * @param bool $debug
     */
    public function __construct($debug) { $this->debug = (bool)$debug; }


    public function onJsonRpcException(GetExceptionResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof JsonRpcRequestInterface) {
            return;
        }

        $exception = $event->getException();
        if ($exception instanceof JsonRpcExceptionInterface) {
            $event->setResponse(new JsonRpcResponse($request->getId(), null, $exception->getJsonRpcError()));

            return;
        }

        $data = $this->debug ? (object)['trace' => $exception->getTrace()] : null;

        $error =
            new JsonRpcError(
                JsonRpcError::INTERNAL_ERROR,
                $event->getException()->getMessage(),
                $data
            );

        $jsonResponse = new JsonRpcResponse($request->getId(), null, $error);
        $event->setResponse($jsonResponse);
    }
}
