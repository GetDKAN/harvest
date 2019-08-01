<?php

namespace Harvest\ETL\Transform;

use Harvest\Log\MakeItLog;

abstract class Transform
{

    protected $harvestPlan;

    public function __construct($harvest_plan)
    {
        $this->harvestPlan = $harvest_plan;
    }

    abstract public function run($item);
}
