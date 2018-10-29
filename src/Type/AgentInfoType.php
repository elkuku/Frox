<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 11.10.18
 * Time: 14:39
 */

namespace App\Type;

class AgentInfoType
{
    public $agentNumber = 0;

    public $keysInfo = '';
    public $linksInfo = '';

    /**
     * @var AgentLinkType[]
     */
    public $links = [];
    public $keys = [];
}
