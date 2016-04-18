<?php
/**
 * User: scaytrase
 * Created: 2016-02-14 12:41
 */

namespace Bankiru\Api\JsonRpc\Controller;

use Bankiru\Api\Rpc\Controller\RpcControllerNameParser;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Class JsonRpcControllerNameParser
 * @package Bankiru\Api\JsonRpc\Controller
 */
final class JsonRpcControllerNameParser extends RpcControllerNameParser
{
    /** {@inheritdoc} */
    protected function guessControllerClassName(BundleInterface $bundle, $controller)
    {
        return $bundle->getNamespace().'\\JsonRpc\\'.$controller.'Controller';
    }
}
