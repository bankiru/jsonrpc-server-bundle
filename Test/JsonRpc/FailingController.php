<?php

namespace Bankiru\Api\JsonRpc\Test\JsonRpc;

class FailingController
{
    public function failureAction()
    {
        throw new \LogicException('This is the malfunction controller');
    }
}
