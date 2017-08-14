<?php

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\NormalizerInterface;
use Bankiru\Api\JsonRpc\Specification\RichJsonRpcRequest;
use Bankiru\Api\Rpc\Event\ViewEvent;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;

final class NormalizingListener
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

        $content = $event->getResponse();
        if (!is_scalar($content) && null !== $content) {
            $content = $this->normalizer->normalize($content, $request->getAttributes()->get('_context'));
        }

        $event->setResponse($content);
    }
}
