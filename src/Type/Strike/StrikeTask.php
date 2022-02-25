<?php

namespace App\Type\Strike;

class StrikeTask
{
    public const TASK_DESTROY = 1;
    public const TASK_CAPTURE = 2;
    public const TASK_FLIP = 4;
    public const TASK_LINK = 8;
    public const TASK_KEYFARM = 9;
    public const TASK_MEET = 10;
    public const TASK_RECHARGE = 11;
    public const TASK_UPGRADE = 12;
    public const TASK_OTHER = 99;

    /**
     * Id of that task. Unique inside of an operation.    int    Set by the backend    X
     **/
    public int $id = 0;

    /**
     * op    ID of the Operation this task belongs to    int    Set by the backend    X
     **/

    /**
     * name    Name of that task    char    -    X
     **/
    public string $name = '';

    /**
     * owner    GID    int    Set by the backend    X
     **/

    /**
     * lat    Latitude    float    -    X
     **/
    public float $lat = 0.0;

    /**
     * lon    Longitude    float    -    X
     **/
    public float $lon = 0.0;

    /**
     * portal    Name of the Portal targeted in the task    char    -
     **/
    public string $portal = '';

    /**
     * portalID    ID (guid) of the Portal targeted in the task    char    -
     **/

    /**
     * start    The date and Time when it starts (number of milliseconds since midnight Jan 1, 1970)    datetime    Defaults to now() if not set by client    X
     * end    The date and Time when it ends (number of milliseconds since midnight Jan 1, 1970)    datetime    -
     * comment    A comment for the agent to read    char    -
     * previous    If this task needs another task to be completed before    int    Valid task-id
     * alternative    If this task is an alternative to another task    int    Valid task-id
     * priority    How important this task is (1 is most important)    int    1 to 5
     */

    /**
     * repeat    How often should this task be done?    int    -
     **/
    public int $repeat = 1;

    /**
     * todo    What should be done?    int    1: 'DESTROY', 2: 'CAPTURE', 4: 'FLIP', 8: 'LINK',
     * 9: 'KEYFARM', 10: 'MEET', 11: 'RECHARGE', 12: 'UPGRADE', 99: 'OTHER'    X
     **/
    public int $todo = 0;

    /**
     * linkTarget    If this is a task to link somewhere, give the name and coordinates of the
     * portal(s) to link to
     * Array    [{name: “char”, portalID: “char”, lat: float, lon: float}]
     * Required if to-do=8
     *
     * @var array<int, \stdClass> $linkTarget
     */
    public array $linkTarget;

    /*
    createdAt	When it was created	datetime	Set by backend	X
    updatedAt	When it was updated	datetime	Set by Backend	X
    accepted	Who accepted this task	Array (IGN/datetime)	Set by backend when the endpoint is called
    done	Who completed this task	Array (GID/datetime)	Set by backend when the endpoint is called
    assignedTo	If that task is assigned to a single agent.	GID	DEPRECATED
    assigned	Who is assigned to this task	GID	[gid,…]
    groupName	If that task meant for a group of agents (Note: add filter	char(255)	-
    status	Status of this task. Set by backend for specific actions. 'pending' after creation, 'acknowledge' after the task was accepted by an agent and current status is 'pending', 'done' after the task was marked as done. Other values are possible for custom states(maybe restrict that?) and the state could be changed manualy	string	-	-
    portalImage	Image url for portal	char	-
     */

}
