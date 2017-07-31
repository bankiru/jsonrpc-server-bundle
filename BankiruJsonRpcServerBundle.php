<?php

namespace Bankiru\Api\JsonRpc;

use Bankiru\Api\JsonRpc\Adapters\JMS\Compiler\RelationHandlerPass;
use Bankiru\Api\JsonRpc\DependencyInjection\BankiruJsonRpcServerExtension;
use Bankiru\Api\JsonRpc\DependencyInjection\Compiler\SymfonyAdapterConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BankiruJsonRpcServerBundle extends Bundle
{
    const JSONRPC_VERSION = '2.0';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SymfonyAdapterConfigurationPass());
        $container->addCompilerPass(new RelationHandlerPass());
    }

    public function getContainerExtension()
    {
        return new BankiruJsonRpcServerExtension();
    }
}
