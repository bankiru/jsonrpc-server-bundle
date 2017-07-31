<?php

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\NormalizerInterface;
use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\JsonRpc\Specification\RichJsonRpcRequest;
use Bankiru\Api\Rpc\Event\ViewEvent;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;

final class ViewListener
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * ViewListener constructor.
     *
     * @param NormalizerInterface $normalizer
     */
    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function onPlainResponse(ViewEvent $event)
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        // No need to perform JSON-RPC serialization here
        if (!$request instanceof RichJsonRpcRequest || $request->isNotification()) {
            return;
        }

        // Response is already properly formatted
        if ($response instanceof JsonRpcResponseInterface) {
            return;
        }

        $response = new JsonRpcResponse(
            $request->getId(),
            $this->normalizer->normalize(
                $event->getResponse(),
                $request->getAttributes()->get('_context')
            )
        );

        $event->setResponse($response);
    }

}
