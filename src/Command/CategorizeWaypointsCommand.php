<?php

namespace App\Command;

use App\Repository\CategoryRepository;
use App\Repository\WaypointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class CategorizeWaypointsCommand extends Command
{
    protected static $defaultName = 'CategorizeWaypoints';

    private EntityManagerInterface $entityManager;
    private WaypointRepository $waypointRepository;
    private CategoryRepository $categoryRepository;

    private array $searchWords
        = [
            // Church
            2  => ['iglesia', 'capilla', 'salon del reino', 'casa de oracion'],
            // Playground
            3  => ['juegos infantiles', 'zona de juegos'],
            // Mural
            4  => ['mural'],
            // Ball games
            6  => [
                'cancha',
                'coliseo deportivo',
                'centro deportivo',
                'coliseo de deportes',
                'estadio',
                'polideportivo',
            ],
            // Monument
            7  => ['escultura', 'monumento', 'busto'],
            // Shrine
            8  => ['gruta'],
            // Sign
            9  => ['placa', 'letrero'],
            // Structure
            10 => ['glorieta'],
            // Building
            11 => [
                'biblioteca',
                'fiscalia',
                'municipio',
                'terminal de transportes',
                'mercado municipal',
            ],
            // Park
            12 => ['parque'],
        ];

    public function __construct(
        EntityManagerInterface $entityManager,
        WaypointRepository $waypointRepository,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->waypointRepository = $waypointRepository;
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
                $found = false;
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

                            if (true)
                                //     $questionHelper->ask(
                                //     $input,
                                //     $output,
                                //     $question
                                // )
                                // )
                            {
                                $waypoint->setCategory($category);
                                $this->entityManager->persist($waypoint);
                                $io->text(
                                    $waypoint->getId().' '.$waypoint->getName()
                                    .' === "'
                                    .$category->getName().'"?'
                                );
                                $found = true;
                                // return Command::SUCCESS;
                            } else {
                                $io->text('neee');
                            }
                        }
                    }
                }

                if (!$found) {
                    $io->text($waypoint->getName());
                    $nones++;
                }
            } else {
                // Waypoint has cat

                // $io->text($waypoint->getCategory());
                // var_dump($waypoint->getCategory());

            }
        }

        $this->entityManager->flush();

        $io->text('Nones: '.$nones);

        $io->success(
            'You have a new command! Now make it your own! Pass --help to see your options.'
        );

        return Command::SUCCESS;
    }
}
