<?php

namespace App\Type;

class AgentInfoType
{
    public int $agentNumber = 0;

    public string $keysInfo = '';
    public string $linksInfo = '';

    /**
     * @var AgentLinkType[]
     */
    public array $links = [];
    public InfoKeyPrepType $keys;
}
