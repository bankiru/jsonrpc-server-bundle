<?php
/**
 * User: scaytrase
 * Created: 2016-02-13 22:28
 */

namespace Bankiru\Api\JsonRpc\Test\JsonRpc;

use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\JsonRpc\Test\Entity\SampleEntity;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;

class TestController
{
    public function sampleAction(JsonRpcRequestInterface $request)
    {
        return new JsonRpcResponse($request->getId());
    }

    public function arrayAction($payload)
    {
        return [new SampleEntity($payload), 'data'];
    }

    public function notificationAction()
    {
        return null;
    }

    public function entityAction($payload)
    {
        return new SampleEntity($payload);
    }
}
