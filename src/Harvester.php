<?php

namespace Harvest;

use Harvest\Log\MakeItLog;
use Harvest\Storage\Run;
use Harvest\Storage\Storage;

class Harvester {
  use MakeItLog;

  private $harvestPlan;
  private $runStorage;

  private $factory;

  public function __construct($harvest_plan, Storage $item_storage, Storage $hash_storage, Storage $run_storage) {
    $this->runStorage = $run_storage;
    $this->validateHarvestPlan($harvest_plan);
    $this->factory = new EtlWorkerFactory($harvest_plan, $item_storage, $hash_storage);
  }

  public function harvest() {
    $items = $this->extract();
    $items = $this->transform($items);
    $results = $this->load($items);


    $this->runStorage->store(json_encode($results), $this->harvestPlan->identifier);

    return $results;
  }

  private function extract() {
    $extract = $this->factory->get('extract');

    if ($this->logger) {
      $extract->setLogger($this->logger);
    }

    $items = $extract->run();
    return $items;
  }

  private function transform($items) {
    $transforms = $this->factory->get("transforms");
    foreach ($transforms as $transform) {
      if ($this->logger) {
        $transform->setLogger($this->logger);
      }
      $transform->run($items);
    }
    return $items;
  }

  private function load($items) {
    $load = $this->factory->get('load');
    if ($this->logger) {
      $load->setLogger($this->logger);
    }
    return $load->run($items);
  }

  private function validateHarvestPlan($harvest_plan) {

    $path_to_schema = __DIR__ . "/../schema/schema.json";
    $json_schema = file_get_contents($path_to_schema);
    $schema = json_decode($json_schema);

    $validator = new \JsonSchema\Validator;
    $validator->validate($harvest_plan, $schema);

    $is_valid = $validator->isValid();

    if (!$is_valid) {
      throw new \Exception(json_encode(['valid' => $is_valid, 'errors' => $validator->getErrors()]));
    }
    $this->harvestPlan = $harvest_plan;

  }

}
