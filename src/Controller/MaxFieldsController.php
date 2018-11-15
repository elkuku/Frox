<?php

namespace App\Controller;

use App\Repository\WaypointRepository;
use App\Service\MaxFieldGenerator;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MaxFieldsController extends Controller
{
    /**
     * @Route("/max-fields", name="max_fields")
     */
    public function index(MaxFieldGenerator $maxFieldGenerator): Response
    {
        return $this->render(
            'max_fields/index.html.twig',
            [
                'controller_name' => 'MaxFieldsController',
                'list'            => $maxFieldGenerator->getList(),
            ]
        );
    }

    /**
     * @Route("/max-fields/{item}", name="max_fields_result")
     */
    public function display(MaxFieldGenerator $maxFieldGenerator, string $item): Response
    {
//        return $this->render(
//            'max_fields/link-list.html.twig',
//            [
//                'item' => $item,
//                'info' => $maxFieldGenerator->getInfo($item),
//                'list' => $maxFieldGenerator->getContentList($item),
//                'agent' =>1
//            ]
//        );
        return $this->render(
            'max_fields/result.html.twig',
            [
                'item' => $item,
                'info' => $maxFieldGenerator->getInfo($item),
                'list' => $maxFieldGenerator->getContentList($item),
            ]
        );
    }

    /**
     * @Route("/export_maxfields", name="export-maxfields")
     */
    public function generateMaxFields(
        WaypointRepository $repository,
        MaxFieldGenerator $maxFieldGenerator,
        Request $request
    ): Response {
        $points = $request->request->get('points');

        if (!$points) {
            throw new NotFoundHttpException('No waypoints selected.');
        }

        $wayPoints   = $repository->findBy(['id' => $points]);
        $maxField    = $maxFieldGenerator->convertWayPointsToMaxFields($wayPoints);
        $buildName   = $request->request->get('buildName');
        $playersNum  = (int)$request->request->get('players_num') ?: 1;
        $timeStamp   = date('Y-m-d');
        $projectName = $playersNum.'pl-'.$timeStamp.'-'.$buildName;

        $maxFieldGenerator->generate($projectName, $maxField, $playersNum);

        return $this->render(
            'max_fields/result.html.twig',
            [
                'item' => $projectName,
                'info' => $maxFieldGenerator->getInfo($projectName),
                'list' => $maxFieldGenerator->getContentList($projectName),

            ]
        );
    }

    /**
     * @Route("/maxfields_send_mail", name="maxfields-send-mail")
     */
    public function sendMail(
        MaxFieldGenerator $maxFieldGenerator,
        \Swift_Mailer $mailer,
        Request $request
    ): JsonResponse {
        $agent = $request->get('agent');
        $email = $request->get('email');
        $item  = $request->get('item');

        try {
            $info = $maxFieldGenerator->getInfo($item);

            $linkList = $this->get('knp_snappy.pdf')
                ->getOutputFromHtml(
                    $this->renderView(
                        'max_fields/link-list.html.twig',
                        [
                            'info'  => $info,
                            'agent' => $agent,
                        ]
                    ),
                    ['encoding' => 'utf-8']
                );

            $keyList = $this->get('knp_snappy.pdf')
                ->getOutputFromHtml(
                    $this->renderView(
                        'max_fields/pdf-keys.html.twig',
                        [
                            'info'  => $info,
                            'agent' => $agent,
                        ]
                    ),
                    ['encoding' => 'utf-8']
                );

            $message = (new \Swift_Message('MaxFields Plan '.$item))
                ->setFrom(getenv('MAILER_FROM_MAIL'))
                ->setTo($email)
                ->attach(new Swift_Attachment($linkList, 'link-list.pdf', 'application/pdf'))
                ->attach(new Swift_Attachment($keyList, 'key-list.pdf', 'application/pdf'));

            $data = [
                'img_portal_map' => $message->embed(
                    \Swift_Image::fromPath($maxFieldGenerator->getImagePath($item, 'portalMap.png'))
                ),
                'img_link_map'   => $message->embed(
                    \Swift_Image::fromPath($maxFieldGenerator->getImagePath($item, 'linkMap.png'))
                ),
                'item'           => $item,
                'agent'          => $agent,
                'info'           => $info,
            ];

            $message->setBody(
                $this->renderView(
                    'max_fields/email.html.twig',
                    $data
                ),
                'text/html'
            )/*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
            ;

            $count = $mailer->send($message);

            $data = [
                'status'  => 'ok',
                'message' => $count.' message(s) sent.',
            ];
        } catch (\Exception $exception) {
            $data = [
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ];
        }

        return $this->json($data);
    }
}
