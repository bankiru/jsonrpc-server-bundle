<?php

namespace Bankiru\Api\JsonRpc;

use Bankiru\Api\JsonRpc\DependencyInjection\BankiruJsonRpcServerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BankiruJsonRpcServerBundle extends Bundle
{
    const JSONRPC_VERSION = '2.0';

    public function getContainerExtension()
    {
        return new BankiruJsonRpcServerExtension();
    }
}
