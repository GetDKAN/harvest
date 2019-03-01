<?php

namespace Harvest\Transform;

use Harvest\Log\MakeItLog;

abstract class Transform {
  use MakeItLog;

  protected $harvestPlan;

  function __construct($harvest_plan) {
    $this->harvestPlan = $harvest_plan;
  }

  abstract function run(&$items);

}
