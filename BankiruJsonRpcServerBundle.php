<?php

namespace Bankiru\Api\JsonRpc;

use Bankiru\Api\JsonRpc\DependencyInjection\BankiruJsonRpcServerExtension;
use Bankiru\Api\JsonRpc\DependencyInjection\Compiler\JmsDriverPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BankiruJsonRpcServerBundle extends Bundle
{
    const VERSION = '2.0';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new JmsDriverPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }

    public function getContainerExtension()
    {
        return new BankiruJsonRpcServerExtension();
    }
}
