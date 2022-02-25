<?php

namespace App\Service;

use App\Type\Strike\StrikeLink;
use App\Type\Strike\StrikePortal;
use App\Type\Strike\StrikeTask;
use Elkuku\MaxfieldParser\MaxfieldParser;
use Elkuku\MaxfieldParser\Type\AgentInfo;

class MaxField2Strike
{
    private int $opId = 0;

    /**
     * @var StrikePortal[]
     */
    private array $portals = [];

    /**
     * @var AgentInfo[]
     */
    private array $agentInfos;

    public function __construct(
        private MaxfieldParser $maxfieldParser,
        private VAPI $VAPI,
        private StrikeLogger $logger
    ) {
    }

    public function generateOp(string $OpName, string $maxfieldName): string
    {
        $this->logger->add('Start creating OP '.$OpName);

        $this->collectData($maxfieldName)
            ->createOp($OpName)
            ->createKeyfarmingTasks()
            ->createLinkTasks();

        $this->logger->add('OP created successfully!!');

        return sprintf(
            'OP "%s" with ID #%d has been created.',
            $OpName,
            $this->opId
        );
    }

    private function collectData(string $maxfieldName): self
    {
        $mfInfo = $this->maxfieldParser->parse($maxfieldName);

        $this->agentInfos = $mfInfo->agentsInfo;

        // Read portals file
        $portals = $this->maxfieldParser->getWayPoints($maxfieldName);

        foreach ($portals as $portal) {
            $p = new StrikePortal();

            $p->name = $portal->name;
            $p->lat = $portal->lat;
            $p->lon = $portal->lon;

            $this->portals[] = $p;
        }

        // Read keyprep file
        $wp = $mfInfo->keyPrep->getWayPoints();

        foreach ($wp as $waypoint) {
            foreach ($this->portals as $i => $portal) {
                if ($portal->name === $waypoint->name) {
                    $this->portals[$i]->num = $waypoint->mapNo;
                    $this->portals[$i]->missingKeys = $waypoint->keysNeeded;

                    continue 2;
                }
            }
        }

        return $this;
    }

    private function createOp(string $opName): self
    {
        $this->logger->add('Creating STRIKE OP...', false);

        // @todo check OP name >2 <100
        $newOp = new \stdClass();
        $newOp->name = $opName;
        $newOp->type = 'linkart';

        $response = $this->VAPI->post('op', (string)json_encode($newOp));

        $content = json_decode($response->getContent(), false);

        if (isset($content->id)) {
            $this->opId = $content->id;

            $this->logger->add('OK - ID: '.$content->id);
        }

        return $this;
    }

    private function createKeyfarmingTasks(): self
    {
        $total = count($this->portals);
        $count = 1;
        foreach ($this->portals as $portal) {
            $this->logger->add(
                sprintf('Creating Keyfarming task %d of %d...', $count, $total),
                false
            );
            $count++;

            if (0 === $portal->missingKeys) {
                $this->logger->add('No keys required.');
                continue;
            }

            $response = $this->VAPI->newTask(
                $this->opId,
                $this->createKeyFarmingTask($portal)
            );

            $a = $response->getContent();

            $this->logger->add('OK');
        }

        return $this;
    }

    private function createLinkTasks(): self
    {
        foreach ($this->agentInfos as $agentInfo) {
            $this->logger->add(
                'Creating Tasks for agent '.$agentInfo->agentNumber
            );

            $total = count($agentInfo->links);
            $count = 1;

            foreach ($agentInfo->links as $link) {
                $this->logger->add(
                    sprintf('Creating Link task %d of %d...', $count, $total),
                    false
                );
                $count++;

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

                $response = $this->VAPI->newTask(
                    $this->opId,
                    $this->createLinkTask($link)
                );

                $a = $response->getContent();

                $this->logger->add('OK');
            }
        }

        return $this;
    }

    private function createKeyFarmingTask(StrikePortal $portal): StrikeTask
    {
        $task = new StrikeTask();

        $task->todo = StrikeTask::TASK_KEYFARM;
        $task->lat = $portal->lat;
        $task->lon = $portal->lon;
        $task->portal = $portal->name;
        $task->repeat = $portal->missingKeys;
        $task->name = sprintf(
            'FARM KEYS: %s (total: %d)',
            $portal->name,
            $portal->missingKeys
        );

        return $task;
    }

    private function createLinkTask(StrikeLink $link): StrikeTask
    {
        $task = new StrikeTask();

        $destination = new \stdClass();

        $destination->lat = $link->destinationLat;
        $destination->lon = $link->destinationLon;
        $destination->name = $link->destinationName;

        $task->todo = StrikeTask::TASK_LINK;
        $task->name = sprintf(
            'LINK %s TO %s',
            mb_substr($link->originName, 0, 40),
            mb_substr($link->destinationName, 0, 40)
        );

        $task->lat = $link->originLat;
        $task->lon = $link->originLon;
        $task->portal = $link->originName;
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
