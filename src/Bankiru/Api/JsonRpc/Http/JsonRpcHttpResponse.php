<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 11.02.2016
 * Time: 15:53
 */

namespace Bankiru\Api\JsonRpc\Http;

use Bankiru\Api\JsonRpc\JsonRpcBundle;
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

        if (!is_array($jsonRpc)) {
            parent::__construct($this->formatJsonRpcResponse($jsonRpc), $status, $headers);

            return;
        }

        $data = [];
        foreach ($jsonRpc as $response) {
            $data[] = $this->formatJsonRpcResponse($response);
        }

        parent::__construct($data, $status, $headers);
    }

    /**
     * @param $jsonRpc
     *
     * @return array
     */
    private function formatJsonRpcResponse(JsonRpcResponseInterface $jsonRpc)
    {
        $data = [
            'jsonrpc' => JsonRpcBundle::VERSION,
            'id'      => $jsonRpc->getId(),
        ];

        if ($jsonRpc->isSuccessful()) {
            $data['result'] = $jsonRpc->getBody();

            return $data;
        } else {
            $error                    = $jsonRpc->getError();
            $data['error']['code']    = $error->getCode();
            $data['error']['message'] = $error->getMessage();
            $data['error']['data']    = $error instanceof JsonRpcErrorInterface ? $error->getData() : null;

            return $data;
        }
    }
}
