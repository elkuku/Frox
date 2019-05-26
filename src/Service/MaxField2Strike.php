<?php


namespace App\Service;

use App\Type\AgentInfoType;
use App\Type\Strike\StrikeLink;
use App\Type\Strike\StrikePortal;
use App\Type\Strike\StrikeTask;

class MaxField2Strike
{
    /**
     * @var VAPI
     */
    private $VAPI;

    /**
     * @var MaxFieldGenerator
     */
    private $maxFieldGenerator;

    private $opId = 0;

    /**
     * @var StrikePortal[]
     */
    private $portals = [];

    /**
     * @var AgentInfoType[]
     */
    private $agentInfos;

    public function __construct(MaxFieldGenerator $maxFieldGenerator, VAPI $VAPI)
    {
        $this->maxFieldGenerator = $maxFieldGenerator;
        $this->VAPI              = $VAPI;
    }

    public function generateOp(string $OpName, string $maxfieldName)
    {
        $this->collectData($maxfieldName)
            ->createOp($OpName)
            ->createKeyfarmingTasks()
            ->createLinkTasks();

        return sprintf('Op "%s" with ID #%d has been created.', $OpName, $this->opId);
    }

    private function collectData(string $maxfieldName)
    {
        $mfInfo = $this->maxFieldGenerator->getInfo($maxfieldName);

        $this->agentInfos = $mfInfo->agentsInfo;

        // Read portals file
        $portals = $this->maxFieldGenerator->parseWayPointsFile($maxfieldName);

        foreach ($portals as $portal) {
            $p = new StrikePortal();

            $p->name = $portal->getName();
            $p->lat  = $portal->getLat();
            $p->lon  = $portal->getLon();

            $this->portals[] = $p;
        }

        // Read keyprep file
        $wp = $mfInfo->keyPrep->getWayPoints();

        foreach ($wp as $waypoint) {
            foreach ($this->portals as $i => $portal) {
                if ($portal->name === $waypoint->name) {
                    $this->portals[$i]->num         = $waypoint->mapNo;
                    $this->portals[$i]->missingKeys = $waypoint->keysNeeded;

                    continue 2;
                }
            }
        }

        return $this;
    }

    private function createOp(string $opName): self
    {
        // @todo check OP name >2 <100
        $newOp       = new \stdClass();
        $newOp->name = $opName;
        $newOp->type = 'linkart';

        $response = $this->VAPI->post('op', json_encode($newOp));

        $content = json_decode($response->getContent(), false);

        $this->opId = $content->id;

        return $this;
    }

    private function createKeyfarmingTasks(): self
    {
        foreach ($this->portals as $portal) {
            if (0 === $portal->missingKeys) {
                continue;
            }

            $response = $this->VAPI->newTask($this->opId, $this->createKeyFarmingTask($portal));

            $a = $response->getContent();
        }

        return $this;
    }

    private function createLinkTasks(): self
    {
        foreach ($this->agentInfos as $agentInfo) {
            // log
            foreach ($agentInfo->links as $link) {
                $origin = $this->findPortalByNumber($link->originNum);
                $destination = $this->findPortalByNumber($link->destinationNum);

                $link = new StrikeLink(
                    $origin->name,
                    $origin->lat,
                    $origin->lon,
                    $destination->name,
                    $destination->lat,
                    $destination->lon
                );

                $response = $this->VAPI->newTask($this->opId, $this->createLinkTask($link));

                $a = $response->getContent();

            }
        }

        return $this;
    }


    private function createKeyFarmingTask(StrikePortal $portal): StrikeTask
    {
        $task = new StrikeTask();

        $task->todo   = StrikeTask::TASK_KEYFARM;
        $task->lat    = $portal->lat;
        $task->lon    = $portal->lon;
        $task->portal = $portal->name;
        $task->repeat = $portal->missingKeys;
        $task->name   = sprintf('FARM KEYS: %s (total: %d)', $portal->name, $portal->missingKeys);

        return $task;
    }

    private function createLinkTask(StrikeLink $link): StrikeTask
    {
        $task = new StrikeTask();

        $destination = new \stdClass();

        $destination->lat  = $link->destinationLat;
        $destination->lon  = $link->destinationLon;
        $destination->name = $link->destinationName;

        $task->todo = StrikeTask::TASK_LINK;
        $task->name = sprintf(
            'LINK %s TO %s',
            mb_substr($link->originName, 0, 40),
            mb_substr($link->destinationName, 0, 40)
        );

        $task->lat        = $link->originLat;
        $task->lon        = $link->originLon;
        $task->portal     = $link->originName;
        $task->linkTarget = [$destination];

        // @todo assigned to
        // $task->assignedTo

        return $task;
    }

    private function findPortalByNumber(int $destinationNum): StrikePortal
    {
        foreach ($this->portals as $portal) {
            if ($portal->num === $destinationNum) {
                return $portal;
            }
        }

        throw new \Exception('Unknown portal num: '.$destinationNum);
    }
}
