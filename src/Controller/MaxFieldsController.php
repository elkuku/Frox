<?php

namespace App\Controller;

use App\Repository\WaypointRepository;
use App\Service\MaxField2Strike;
use App\Service\MaxFieldGenerator;
use App\Service\StrikeLogger;
use Knp\Snappy\Pdf;
use Swift_Attachment;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MaxFieldsController
 *
 * @Route("max-fields")
 */
class MaxFieldsController extends AbstractController
{
    /**
     * @Route("/", name="max_fields")
     */
    public function index(MaxFieldGenerator $maxFieldGenerator): Response
    {
        return $this->render(
            'max_fields/index.html.twig',
            [
                'list'            => $maxFieldGenerator->getList(),
                'maxfieldVersion' => $maxFieldGenerator->getMaxfieldVersion(),
            ]
        );
    }

    /**
     * @Route("/show/{item}", name="max_fields_result")
     */
    public function display(MaxFieldGenerator $maxFieldGenerator, string $item): Response
    {
        return $this->render(
            'max_fields/result.html.twig',
            [
                'item'            => $item,
                'info'            => $maxFieldGenerator->getInfo($item),
                'list'            => $maxFieldGenerator->getContentList($item),
                'maxfieldVersion' => $maxFieldGenerator->getMaxfieldVersion(),
            ]
        );
    }

    /**
     * @Route("/export", name="export-maxfields")
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

        $wayPoints = $repository->findBy(['id' => $points]);
        $maxField = $maxFieldGenerator->convertWayPointsToMaxFields($wayPoints);

        $buildName = $request->request->get('buildName');
        $playersNum = (int)$request->request->get('players_num') ?: 1;
        $options = [
            'skip_plots'      => $request->request->getBoolean('skip_plots'),
            'skip_step_plots' => $request->request->getBoolean('skip_step_plots'),
        ];

        $timeStamp = date('Y-m-d');
        $projectName = $playersNum.'pl-'.$timeStamp.'-'.$buildName;

        $maxFieldGenerator->generate($projectName, $maxField, $playersNum, $options);

        return $this->render(
            'max_fields/result.html.twig',
            [
                'item'            => $projectName,
                'info'            => $maxFieldGenerator->getInfo($projectName),
                'list'            => $maxFieldGenerator->getContentList($projectName),
                'maxfieldVersion' => $maxFieldGenerator->getMaxfieldVersion(),

            ]
        );
    }

    /**
     * @Route("/send_mail", name="maxfields-send-mail")
     */
    public function sendMail(MaxFieldGenerator $maxFieldGenerator, MailerInterface $mailer, Request $request, Pdf $pdf)
    {
        $agent = $request->get('agent');
        $email = $request->get('email');
        $item = $request->get('item');

        try {
            $info = $maxFieldGenerator->getInfo($item);

            $linkList = $pdf
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

            $keyList = $pdf
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

            $email = (new TemplatedEmail())
                ->from($_ENV['MAILER_FROM_MAIL'])
                ->to($email)
                ->subject('MaxFields Plan '.$item)
                ->attach($linkList, 'link-list.pdf', 'application/pdf')
                ->attach($keyList, 'key-list.pdf', 'application/pdf')
                ->htmlTemplate('max_fields/email.html.twig')
                ->context(
                    [
                        'img_portal_map' => $item.'/portal_map.png',
                        'img_link_map'   => $item.'/link_map.png',
                        'item'           => $item,
                        'agent'          => $agent,
                        'info'           => $info,
                    ]
                );

            $mailer->send($email);
            $data = [
                'status'  => 'ok',
                'message' => 'Message has been sent.',
            ];
        } catch (\Exception $exception) {
            $data = [
                'status'  => 'error',
                'message' => 'error sending mail: '.$exception->getMessage(),
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/send_mail2", name="maxfields-send-mail2")
     */
    public function sendMail2(
        MaxFieldGenerator $maxFieldGenerator,
        \Swift_Mailer $mailer,
        Request $request,
        Pdf $pdf
    ): JsonResponse {
        $agent = $request->get('agent');
        $email = $request->get('email');
        $item = $request->get('item');

        try {
            $info = $maxFieldGenerator->getInfo($item);

            $linkList = $pdf
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

            $keyList = $pdf
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
                ->setFrom($_ENV['MAILER_FROM_MAIL'])
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
            );

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

    /**
     * @Route("/gpx/{item}", name="max_fields_gpx")
     */
    public function getGpx(MaxFieldGenerator $maxFieldGenerator, string $item): void
    {
        $gpx = $maxFieldGenerator->getGpx($item);

        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="'.$item.'.gpx"');

        echo $gpx;

        exit();
    }

    /**
     * @Route("/delete/{item}", name="max_fields_delete")
     */
    public function delete(MaxFieldGenerator $maxFieldGenerator, string $item): Response
    {
        try {
            $maxFieldGenerator->remove($item);

            $this->addFlash('success', sprintf('%s has been removed.', $item));
        } catch (IOException $exception) {
            $this->addFlash('warning', $exception->getMessage());
        }

        return $this->render(
            'max_fields/index.html.twig',
            [
                'list'            => $maxFieldGenerator->getList(),
                'maxfieldVersion' => $maxFieldGenerator->getMaxfieldVersion(),
            ]
        );
    }

    /**
     * @Route("/maxfield2strike", name="maxfields-maxfield2strike")
     */
    public function maxfield2strike(MaxField2Strike $maxField2Strike, Request $request)
    {
        $opName = $request->query->get('opName');
        $maxfieldName = $request->query->get('maxfieldName');

        //        $restClient = $this->container->get('circle.restclient');

        $result = $maxField2Strike->generateOp($opName, $maxfieldName);
        $data = [
            'status'  => 'ok',
            'message' => $result,
        ];

        return $this->json($data);
    }

    /**
     * @Route("/log", name="maxfields-log")
     */
    public function getLog(StrikeLogger $logger)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent($logger->getLog());

        return $response;
    }
}
