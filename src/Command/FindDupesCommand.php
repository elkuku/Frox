<?php

namespace App\Command;

use App\Repository\WaypointRepository;
use App\Service\WayPointHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FindDupesCommand extends Command
{
    protected static $defaultName = 'finddupes';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var WaypointRepository
     */
    private $waypointRepository;

    /**
     * @var WayPointHelper
     */
    private $wayPointHelper;

    public function __construct(EntityManagerInterface $entityManager, WaypointRepository $waypointRepository, WayPointHelper $wayPointHelper)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->waypointRepository = $waypointRepository;
        $this->wayPointHelper = $wayPointHelper;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $errorCount = 0;
        $warningCount = 0;

        $waypoints = $this->waypointRepository->findAll();

        $progressBar = new ProgressBar($output, count($waypoints));

        foreach ($waypoints as $waypoint) {
            foreach ($waypoints as $test) {
                if ($test->getLat() === $waypoint->getLat()
                    && $test->getLon() === $waypoint->getLon()
                    && $test->getId() !== $waypoint->getId()
                ) {
                    $io->error($waypoint->getId().' has dupe: '.$test->getId());
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
    }
}
