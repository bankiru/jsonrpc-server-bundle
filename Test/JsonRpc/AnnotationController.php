<?php

namespace Bankiru\Api\JsonRpc\Test\JsonRpc;

use Bankiru\Api\Rpc\Routing\Annotation\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AnnotationController
 *
 * @package Bankiru\Api\JsonRpc\Test\JsonRpc
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
