<?php

namespace App\Service;

use App\Type\Strike\StrikeTask;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class VAPI
{
    public string $apiBaseUrl = 'https://tasks.enl.one/api';

    public function __construct(
        private string $apiKey,
        private HttpClientInterface $restClient
    ) {
    }

    public function newTask(int $opId, StrikeTask $task): ResponseInterface
    {
        $command = "op/$opId/task";
        $payload = json_encode($task, JSON_THROW_ON_ERROR);

        return $this->restClient->request(
            'POST', $this->generateURL($command),
            [
                'body' => $payload,
            ]
        );
    }

    public function get(string $command): ResponseInterface
    {
        return $this->restClient->request('GET', $this->generateURL($command));
    }

    public function post(string $command, string $payload): ResponseInterface
    {
        return $this->restClient->request(
            'POST',
            $this->generateURL($command),
            ['body' => $payload]
        );
    }

    private function generateURL(string $command): string
    {
        return "$this->apiBaseUrl/$command?apikey=$this->apiKey";
    }
}
