<?php

namespace App\Type;

class InfoStepType
{
    public const TYPE_LINK = 1;
    public const TYPE_MOVE = 2;

    public int $linkNum;

    public int $action = 0;

    public int $agentNum = 0;

    public int $originNum = 0;
    public string $originName = '';
    public int $destinationNum = 0;
    public string $destinationName = '';
}
