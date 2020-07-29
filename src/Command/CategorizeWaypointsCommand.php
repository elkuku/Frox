<?php

namespace App\Command;

use App\Repository\CategoryRepository;
use App\Repository\WaypointRepository;
use App\Service\WayPointHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class CategorizeWaypointsCommand extends Command
{
    protected static $defaultName = 'CategorizeWaypoints';

    private EntityManagerInterface $entityManager;
    private WaypointRepository $waypointRepository;
    private CategoryRepository $categoryRepository;
    private WayPointHelper $wayPointHelper;

    private array $searchWords
        = [
            // Church
            2 => ['iglesia'],
            // Ball games
            6 => ['cancha'],
            // 2 => [],
        ];

    public function __construct(
        EntityManagerInterface $entityManager,
        WaypointRepository $waypointRepository,
        CategoryRepository $categoryRepository,
        WayPointHelper $wayPointHelper
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->waypointRepository = $waypointRepository;
        $this->wayPointHelper = $wayPointHelper;
        $this->categoryRepository = $categoryRepository;
    }

    protected function configure()
    {
        $this->setDescription('Categorize waypoints');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);
        $questionHelper = $this->getHelper('question');

        $waypoints = $this->waypointRepository->findAll();

        $nones = 0;
        foreach ($waypoints as $waypoint) {
            if (!$waypoint->getCategory()
                || 'None' === $waypoint->getCategory()->getName()
            ) {
                // $io->text('NONE');
                foreach ($this->searchWords as $catId => $searchWords) {
                    foreach ($searchWords as $searchWord) {
                        // var_dump($waypoint->getName());
                        if (false !== stripos($waypoint->getName(), $searchWord)
                        ) {
                            $category = $this->categoryRepository->findOneBy(
                                ['id' => $catId]
                            );
                            // $io->text($waypoint->getName().': cat - '.$category->getName());
                            $question = new ConfirmationQuestion(
                                $waypoint->getName().' === "'
                                .$category->getName().'"?'
                            );

                            if ($questionHelper->ask(
                                $input,
                                $output,
                                $question
                            )
                            ) {
                                $waypoint->setCategory($category);
                                $this->entityManager->persist($waypoint);
                                $this->entityManager->flush();
                                $io->text('ja');
                                // return Command::SUCCESS;
                            } else {
                                $io->text('neee');
                            }
                        } else {
                            $nones++;
                        }
                    }
                }
            } else {
                // Waypoint has cat

                // $io->text($waypoint->getCategory());
                // var_dump($waypoint->getCategory());

            }
        }

        $io->text('Nones: '.$nones);

        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success(
            'You have a new command! Now make it your own! Pass --help to see your options.'
        );

        return 0;
    }
}
