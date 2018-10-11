<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 11.10.18
 * Time: 10:32
 */

namespace App\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This is for https://github.com/tvwenger/maxfield
 */
class MaxFieldGenerator
{
    private $rootDir;
    private $executable;

    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir.'/public/maxfields';

        // Path to makePlan.py
        $this->executable = getenv('MAXFIELDS_EXEC');
    }

    public function generate(string $projectName, string $wayPointList)
    {
        $fileSystem = new Filesystem();

        try {
            $projectRoot = $this->rootDir.'/'.$projectName;
            $fileSystem->mkdir($projectRoot);
            $fileName = $projectRoot.'/'.$projectName.'.waypoints';
            $fileSystem->appendToFile($fileName, $wayPointList);

            // EXEC
            //  python makePlan.py -n 4 EXAMPLE.waypoints -d out/ -f output.pkl
//            $players = '-n';
            $command = 'python '.$this->executable.' '.$fileName.' -d '.$projectRoot.' -f output.pkl';
            exec($command);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }

//        echo exec('python ' . $executable . ' -h');
//        echo 'python '.$executable.'-h';

    }

}
