<?php

namespace Harvest\ETL\Transform;

use Harvest\Log\MakeItLog;

abstract class Transform {

  protected $harvestPlan;

  function __construct($harvest_plan) {
    $this->harvestPlan = $harvest_plan;
  }

  abstract function run(&$items);

}
