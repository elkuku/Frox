<?php

namespace App\Controller;

use App\Entity\Waypoint;
use App\Form\WaypointFormType;
use App\Helper\Paginator\PaginatorTrait;
use App\Repository\AgentRepository;
use App\Repository\ProvinceRepository;
use App\Repository\WaypointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgentsController extends AbstractController
{
    /**
     * @Route("/agent_data", name="agent_data")
     */
    public function map(AgentRepository $agentRepository): JsonResponse
    {
        $agents = $agentRepository->getAgentData();

        return $this->json($agents);
    }
}
