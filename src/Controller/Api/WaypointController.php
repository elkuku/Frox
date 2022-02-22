<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/zapi/waypoints")
 */
class WaypointController extends AbstractController
{
    /**
     * @Route("/", name="api_waypoints")
     */
    public function getList(): JsonResponse
    {
        $list = [
            'hydra:member'     => ['a', 'b', 'c'],
            'hydra:totalItems' => 123,
            'hydra:view'       => [],
            'a'                => '',
        ];

        return $this->json($list);
    }
}
