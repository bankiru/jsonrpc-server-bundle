<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 16.02.2016
 * Time: 14:50
 */

namespace Bankiru\Api\JsonRpc\Test\JsonRpc;

use Bankiru\Api\Rpc\Routing\Annotation\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AnnotationController
 *
 * @package Bankiru\Api\JsonRpc\Test\JsonRpc
 * @Method("annotation")
 */
class AnnotationController extends Controller
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
