<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 11.10.18
 * Time: 10:32
 */

namespace App\Service;

use App\Entity\Waypoint;
use App\Type\AgentInfoType;
use App\Type\MaxFieldType;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
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

    public function generate(string $projectName, string $wayPointList, int $playersNum)
    {
        $fileSystem = new Filesystem();

        try {
            $projectRoot = $this->rootDir.'/'.$projectName;
            $fileSystem->mkdir($projectRoot);
            $fileName = $projectRoot.'/'.$projectName.'.waypoints';
            $fileSystem->appendToFile($fileName, $wayPointList);

            // EXEC
            //  python makePlan.py -n 4 EXAMPLE.waypoints -d out/ -f output.pkl
            $players = ' -n '.$playersNum;
//            $google = '-g';
//            $google_api_key = '-a '.$apiKey;
            $command = 'python '.$this->executable.' '.$fileName.' -d '.$projectRoot.' -f output.pkl'.$players;
            exec($command);
        } catch (IOExceptionInterface $exception) {
            echo 'An error occurred while creating your directory at '.$exception->getPath();
        }
    }

    public function getContentList(string $item): array
    {
        $list = [];

        foreach (new \DirectoryIterator($this->rootDir.'/'.$item) as $fileInfo) {
            if ($fileInfo->isFile()) {
                $list[] = $fileInfo->getFilename();
            }
        }

        sort($list);

        return $list;
    }

    public function getInfo(string $item): MaxFieldType
    {
        $info = new MaxFieldType();

        $numPlayers = preg_match('#([\d]+)pl-#', $item, $matches) ? $matches[1] : 1;

        $info->keyPrep = $this->getTextFileContents($item, 'keyPrep.txt');
        $info->ownershipPrep = $this->getTextFileContents($item, 'ownershipPrep.txt');
        $info->agentsInfo = $this->getAgentsInfo($item, $numPlayers);

        return $info;
    }

    private function getAgentsInfo(string $item, int $numAgents = 1): array
    {
        $count = 1;
        $agentsInfo = [];

        try {
            start:
            $info = new AgentInfoType();
            $info->agentNumber = $count;
            $fileName = sprintf('keys_for_agent_%d_of_%d.txt', $count, $numAgents);
            $info->keysInfo = $this->getTextFileContents($item, $fileName);
            $fileName = sprintf('links_for_agent_%d_of_%d.txt', $count, $numAgents);
            $info->linksInfo = $this->getTextFileContents($item, $fileName);
            $agentsInfo[] = $info;
            $count++;
            goto start;

        } catch (FileNotFoundException $e) {
            // Finished.
        }

        return $agentsInfo;
    }

    public function getTextFileContents(string $item, string $fileName): string
    {
        $path = $this->rootDir.'/'.$item.'/'.$fileName;

        if (false === file_exists($path)) {
            throw new FileNotFoundException('File not found.');
        }

        return file_get_contents($path);
            }

    public function getList(): array
    {
        $list = [];

        foreach (new \DirectoryIterator($this->rootDir) as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                $list[] = $fileInfo->getFilename();
            }
        }

        sort($list);

        return $list;
    }

    /**
     * @param Waypoint[] $wayPoints
     */
    public function convertWayPointsToMaxFields(array $wayPoints): string
    {
        $maxFields = [];

        foreach ($wayPoints as $wayPoint) {
            $points = $wayPoint->getLat().','.$wayPoint->getLon();
            $maxFields[] = $wayPoint->getName().';https://'.getenv('INTEL_URL').'?ll='.$points.'&z=1&pll='.$points;
        }

        return implode("\n", $maxFields);
    }


}
