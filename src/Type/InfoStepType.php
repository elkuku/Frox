<?php

namespace App\Type;

class InfoStepType
{
    public const TYPE_LINK = 1;
    public const TYPE_MOVE = 2;

    public $linkNum;

    public $action = '';

    public $agentNum = 0;

    public $originNum = 0;
    public $originName = '';
    public $destinationNum = 0;
    public $destinationName = '';
}
