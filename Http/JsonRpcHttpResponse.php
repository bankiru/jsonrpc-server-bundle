<?php

namespace Bankiru\Api\JsonRpc\Http;

use Bankiru\Api\JsonRpc\BankiruJsonRpcServerBundle;
use ScayTrase\Api\JsonRpc\JsonRpcErrorInterface;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonRpcHttpResponse extends JsonResponse
{
    /**
     * JsonRpcHttpResponse constructor.
     *
     * @param JsonRpcResponseInterface|JsonRpcResponseInterface[] $jsonRpc
     * @param int                                                 $status
     * @param array                                               $headers
     */
    public function __construct($jsonRpc = null, $status = 200, array $headers = [])
    {
        if (null === $jsonRpc) {
            parent::__construct(null, $status, $headers);

            return;
        }

        if (is_array($jsonRpc)) {
            parent::__construct(array_map([$this, 'formatJsonRpcResponse'], $jsonRpc), $status, $headers);

            return;
        }

        parent::__construct($this->formatJsonRpcResponse($jsonRpc), $status, $headers);
    }

    /**
     * @param $jsonRpc
     *
     * @return array
     */
    private function formatJsonRpcResponse(JsonRpcResponseInterface $jsonRpc)
    {
        $data = [
            'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
            'id'      => $jsonRpc->getId(),
        ];

        if ($jsonRpc->isSuccessful()) {
            $data['result'] = $jsonRpc->getBody();

            return $data;
        }

        $error                    = $jsonRpc->getError();
        $data['error']['code']    = $error->getCode();
        $data['error']['message'] = $error->getMessage();
        $data['error']['data']    = $error instanceof JsonRpcErrorInterface ? $error->getData() : null;

        return $data;
    }
}
