<?php

namespace Bankiru\Api\JsonRpc\Test\JsonRpc;

use Bankiru\Api\Rpc\Routing\Annotation\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Method("annotation")
 */
final class AnnotationController extends Controller
{
    /**
     * @return array
     * @Method("/sub")
     */
    public function subAction()
    {
        return [];
    }
}
