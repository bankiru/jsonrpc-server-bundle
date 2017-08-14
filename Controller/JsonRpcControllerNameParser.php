<?php

namespace Bankiru\Api\JsonRpc\Controller;

use Bankiru\Api\Rpc\Controller\RpcControllerNameParser;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class JsonRpcControllerNameParser extends RpcControllerNameParser
{
    /** {@inheritdoc} */
    protected function guessControllerClassName(BundleInterface $bundle, $controller)
    {
        return $bundle->getNamespace() . '\\JsonRpc\\' . $controller . 'Controller';
    }
}
