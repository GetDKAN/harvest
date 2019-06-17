<?php

namespace Harvest\ETL;


use Harvest\Storage\Storage;

class Factory {

  private $harvestPlan;
  private $itemStorage;
  private $hashStorage;

  public function __construct($harvest_plan, Storage $item_storage, Storage $hash_storage) {
    $this->harvestPlan = $harvest_plan;
    $this->itemStorage = $item_storage;
    $this->hashStorage = $hash_storage;
  }

  public function get($type) {
    if ($type == "extract") {
      $class = $this->harvestPlan->source->type;
      return new $class($this->harvestPlan, $this->itemStorage);
    }
    elseif ($type == "load") {
      $class = $this->harvestPlan->load->type;
      return  new $class($this->harvestPlan, $this->hashStorage);
    }
    elseif($type == "transforms") {
      $transforms = [];
      if ($this->harvestPlan->transforms) {
        foreach ($this->harvestPlan->transforms as $info) {
          $config = NULL;

          if (is_object($info)) {
            $info = (array) $info;
            $class = array_keys($info)[0];
          }
          else {
            $class = $info;
          }
          $transforms[] = $this->getOne($class, $this->harvestPlan);
        }
      }

      return $transforms;
    }
  }

  private function getOne($class, $config = NULL) {
    if (!$config) {
      $config = $this->harvestPlan;
    }
    return new $class($config);
  }

}
