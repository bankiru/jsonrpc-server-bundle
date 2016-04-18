<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 11.02.2016
 * Time: 16:11
 */

namespace Bankiru\Api\JsonRpc;

use Bankiru\Api\JsonRpc\DependencyInjection\Compiler\JmsDriverPass;
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
    }
}
