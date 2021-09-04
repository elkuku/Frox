<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 11.10.18
 * Time: 14:14
 */

namespace App\Type\MaxFields;

use App\Type\AgentInfoType;
use App\Type\InfoKeyPrepType;

class MaxFieldType
{
    public InfoKeyPrepType $keyPrep;
    public string $keyPrepTxt = '';
    public string $ownershipPrep = '';

    /**
     * @var AgentInfoType[]
     */
    public array $agentsInfo = [];

    public int $frames = 0;

    /**
     * @var \App\Type\AgentLinkType[]
     */
    public array $links;

    public array $steps;
}
