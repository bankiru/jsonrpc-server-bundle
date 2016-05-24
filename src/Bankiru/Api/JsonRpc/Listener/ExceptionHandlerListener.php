<?php
/**
 * User: scaytrase
 * Created: 2016-02-14 13:32
 */

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\Exception\JsonRpcException;
use Bankiru\Api\JsonRpc\Exception\JsonRpcExceptionInterface;
use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\Rpc\Event\GetExceptionResponseEvent;
use Bankiru\Api\Rpc\Exception\InvalidMethodParametersException;
use Bankiru\Api\Rpc\Routing\Exception\MethodNotFoundException;
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
        if ($exception instanceof InvalidMethodParametersException) {
            $exception =
                JsonRpcException::create(
                    JsonRpcError::INVALID_PARAMS,
                    $exception->getMessage(),
                    $exception->getTrace());
        } elseif ($exception instanceof MethodNotFoundException) {
            $exception =
                JsonRpcException::create(
                    JsonRpcError::METHOD_NOT_FOUND,
                    $exception->getMessage(),
                    $exception->getTrace());
        }

        if ($exception instanceof JsonRpcExceptionInterface) {
            $error = $exception->getJsonRpcError();
            if (!$this->debug) {
                $error = new JsonRpcError($error->getCode(), $error->getMessage());
            }

            $event->setResponse(new JsonRpcResponse($request->getId(), null, $error));

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
