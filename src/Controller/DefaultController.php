<?php

namespace App\Controller;

use App\Repository\WaypointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(WaypointRepository $repository): Response
    {
        return $this->render(
            'default/index.html.twig',
            [
                'numWayPoints' => \count($repository->findAll()),
            ]
        );
    }

    /**
     * @Route("/map", name="map")
     */
    public function map(): Response
    {
        return $this->render('default/map.html.twig');
    }

    /**
     * @Route("/map2", name="map2")
     */
    public function map2(): Response
    {
        return $this->render('default/map2.html.twig');
    }

    /**
     * @Route("/provinces", name="provinces")
     */
    public function provinces(): Response
    {
        return $this->render('default/provinces.html.twig');
    }

    /**
     * @Route("/backup", name="backup")
     *
     * @return Response
     */
    public function backup(\Swift_Mailer $mailer): Response
    {
        $pattern = '#mysql://(.+)\:(.+)@127.0.0.1:3306/(.+)#';

        preg_match($pattern, getenv('DATABASE_URL'), $matches);

        if (4 !== \count($matches))
        {
            throw new \UnexpectedValueException('Error parsing the database URL.');
        }

        $dbUser = $matches[1];
        $dbPass = $matches[2];
        $dbName = $matches[3];

        $cmd = sprintf('mysqldump -u%s -p%s %s|gzip 2>&1', $dbUser, $dbPass, $dbName);

        ob_start();
        passthru($cmd, $retVal);
        $gzip = ob_get_clean();

        if ($retVal)
        {
            throw new \RuntimeException('Error creating DB backup: ' . $gzip);
        }

        $fileName = date('Y-m-d') . '_backup.gz';
        $mime     = 'application/x-gzip';

        $message = (new \Swift_Message('Frox! Backup', '<h3>Backup</h3>Date: ' . date('Y-m-d'), 'text/html'))
            ->attach(new \Swift_Attachment($gzip, $fileName, $mime))
            ->setFrom(getenv('MAILER_FROM_MAIL'))
            ->setTo(getenv('MAILER_FROM_MAIL'));

        $count = $mailer->send($message);

        if (!$count)
        {
            $this->addFlash('danger', 'There was an error sending the message...');
        }
        else
        {
            $this->addFlash('success', 'Backup has been sent to your inbox.');
        }

        return $this->redirectToRoute('default');
    }
}
