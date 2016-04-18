<?php
/**
 * User: scaytrase
 * Created: 2016-02-13 22:28
 */

namespace Bankiru\Api\JsonRpc\Test\JsonRpc;


class FailingController
{
    public function failureAction()
    {
        throw new \LogicException('This is the malfunction controller');
    }
}
