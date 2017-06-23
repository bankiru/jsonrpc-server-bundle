<?php

namespace Bankiru\Api\JsonRpc\Test\JsonRpc;

use Bankiru\Api\Rpc\Routing\Annotation\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Method("security/")
 */
final class SecurityController
{
    /**
     * @Method("public")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function publicAction()
    {
        return ['success' => true];
    }

    /**
     * @Method("private")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function privateAction()
    {
        return ['success' => true];
    }
}
