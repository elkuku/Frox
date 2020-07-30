<?php

namespace App\Command;

use App\Repository\WaypointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class FindDupesCommand extends Command
{
    protected static $defaultName = 'finddupes';

    private EntityManagerInterface $entityManager;
    private WaypointRepository $waypointRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        WaypointRepository $waypointRepository
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->waypointRepository = $waypointRepository;
    }

    protected function configure():void
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument(
                'arg1',
                InputArgument::OPTIONAL,
                'Argument description'
            )
            ->addOption(
                'option1',
                null,
                InputOption::VALUE_NONE,
                'Option description'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $io = new SymfonyStyle($input, $output);
        $waypoints = $this->waypointRepository->findAll();
        $progressBar = new ProgressBar($output, count($waypoints));

        $helper = $this->getHelper('question');

        $choices = [
            'Remove A',
            'Remove B',
            'Change name A with B',
            'Change name B with A',
            'Skip',
        ];

        $question = new ChoiceQuestion(
            'Please select [Skip]',
            $choices,
            4
        );
        $question->setErrorMessage('Color %s is invalid.');

        $removals = 0;

        foreach ($waypoints as $waypoint) {
            foreach ($waypoints as $test) {
                if ($test->getLat() === $waypoint->getLat()
                    && $test->getLon() === $waypoint->getLon()
                    && $test->getId() !== $waypoint->getId()
                ) {
                    $io->text(
                        [
                            '',
                            sprintf(
                                'A: %s - %d',
                                $waypoint->getName(),
                                $waypoint->getId()
                            ),
                            sprintf(
                                'B: %s - %d',
                                $test->getName(),
                                $test->getId()
                            ),
                        ]
                    );

                    if ($waypoint->getName() !== $test->getName()) {
                        $io->warning('Name mismatch!');
                        $choice = $helper->ask($input, $output, $question);

                        if ($choice === $choices[0]) {
                            $io->text('@todo remove a');
                        } elseif ($choice === $choices[1]) {
                            $this->entityManager->remove($test);
                            $this->entityManager->flush();
                            $removals++;
                        } elseif ($choice === $choices[2]) {
                            $waypoint->setName($test->getName());
                            $this->entityManager->persist($waypoint);
                            $this->entityManager->flush();
                        } elseif ($choice === $choices[3]) {
                            $io->text('@todo change b with a');
                        }
                    } else {
                        $this->entityManager->remove($test);
                        $this->entityManager->flush();
                        $removals++;
                    }
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        if ($removals) {
            $io->warning(
                sprintf('%d duplicates have been removed.', $removals)
            );
        } else {
            $io->success('Database is clean :)');
        }

        return Command::SUCCESS;
    }
}
