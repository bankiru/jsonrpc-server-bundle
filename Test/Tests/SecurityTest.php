<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\Rpc\Tests\Fixtures\Impl\Request;
use ScayTrase\Api\JsonRpc\JsonRpcError;

final class SecurityTest extends JsonRpcTestCase
{
    public function testPrivateMethodRequest()
    {
        $response = $this->sendJsonRpcRequest(new Request('prefix/security/private', []), '/test/');

        self::assertFalse($response->isSuccessful());
        self::assertEquals($response->getError()->getCode(), JsonRpcError::METHOD_NOT_FOUND);
    }

    public function testPublicMethodRequest()
    {
        $response = $this->sendJsonRpcRequest(new Request('prefix/security/public', []), '/test/');

        self::assertTrue($response->isSuccessful(), json_encode($response, JSON_PRETTY_PRINT));
    }
}
