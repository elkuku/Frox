<?php

namespace App\Service;

use App\Type\Strike\StrikeTask;
use Circle\RestClientBundle\Services\RestClient;
use Symfony\Component\HttpFoundation\Response;

class VAPI
{
    public $apiBaseUrl = 'https://tasks.enl.one/api';

    /**
     * @var RestClient
     */
    private $restClient;

    private $apiKey;

    public function __construct(RestClient $restClient, $apiKey)
    {
        $this->restClient = $restClient;
        $this->apiKey = $apiKey;
    }

    public function newTask(int $opId, StrikeTask $task)
    {
        $command = "op/$opId/task";
        $payload = json_encode($task);

        return $this->restClient->post($this->generateURL($command), $payload);
    }

    public function get(string $command): Response
    {
        return $this->restClient->get($this->generateURL($command));
    }

    public function post(string $command, $payload): Response
    {
        return $this->restClient->post($this->generateURL($command), $payload);
    }

    private function generateURL($command): string
    {
        return "$this->apiBaseUrl/$command?apikey=$this->apiKey";
    }
}
