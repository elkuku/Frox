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
    /**
     * @var InfoKeyPrepType
     */
    public $keyPrep;

    public $keyPrepTxt = '';

    public $ownershipPrep = '';

    /**
     * @var AgentInfoType[]
     */
    public $agentsInfo = [];

    public $frames = 0;

    public $links;
}
