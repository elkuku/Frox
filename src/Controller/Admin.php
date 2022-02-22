<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Admin extends AbstractController
{
    #[Route(path: '/backup', name: 'backup')]
    public function backup(\Swift_Mailer $mailer): Response
    {
        $pattern = '#mysql://(.+)\:(.+)@127.0.0.1:3306/(.+)#';

        preg_match($pattern, $_ENV['DATABASE_URL'], $matches);
        $mailAddress = $_ENV['MAILER_FROM_MAIL'];

        if (4 !== \count($matches)) {
            throw new \UnexpectedValueException(
                'Error parsing the database URL.'
            );
        }

        $dbUser = $matches[1];
        $dbPass = $matches[2];
        $dbName = $matches[3];

        $cmd = sprintf(
            'mysqldump -u%s -p%s %s|gzip 2>&1',
            $dbUser,
            $dbPass,
            $dbName
        );

        ob_start();
        passthru($cmd, $retVal);
        $gzip = ob_get_clean();

        if ($retVal) {
            throw new \RuntimeException('Error creating DB backup: '.$gzip);
        }

        $fileName = date('Y-m-d').'_backup.gz';
        $mime = 'application/x-gzip';

        $message = (new \Swift_Message(
            'Frox! Backup', '<h3>Backup</h3>Date: '.date('Y-m-d'), 'text/html'
        ))
            ->attach(new \Swift_Attachment($gzip, $fileName, $mime))
            ->setFrom($mailAddress)
            ->setTo($mailAddress);

        $count = $mailer->send($message);

        if (!$count) {
            $this->addFlash(
                'danger',
                'There was an error sending the message...'
            );
        } else {
            $this->addFlash('success', 'Backup has been sent to your inbox.');
        }

        return $this->redirectToRoute('default');
    }
}
