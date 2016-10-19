<?php

namespace Bankiru\Api\JsonRpc;

use Bankiru\Api\JsonRpc\DependencyInjection\Compiler\JmsDriverPass;
use Bankiru\Api\JsonRpc\DependencyInjection\Compiler\JmsSerializerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JsonRpcBundle extends Bundle
{
    const VERSION = '2.0';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new JmsDriverPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $container->addCompilerPass(new JmsSerializerPass());
    }
}
