<?php
/**
 * User: scaytrase
 * Created: 2016-02-14 14:39
 */

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\JsonRpc\Test\Tests\Fixtures\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Client;

abstract class JsonRpcTestCase extends WebTestCase
{
    protected static function getKernelClass()
    {
        return Kernel::class;
    }

    /**
     * @param        $endpoint
     * @param Client $client
     * @param        $requests
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    protected function sendRequest(Client $client = null, $endpoint, $requests)
    {
        if (null === $client) {
            $client = static::createClient();
        }

        $client->request('POST', $endpoint, [], [], [], json_encode($requests));

        return $client->getResponse();
    }
}
