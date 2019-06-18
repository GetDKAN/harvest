<?php

namespace Harvest;

use Harvest\ETL\Transform\Transform;
use Harvest\ETL\Factory;

class Harvester {
  const HARVEST_LOAD_NEW_ITEM = 0;
  const HARVEST_LOAD_UPDATED_ITEM = 1;
  const HARVEST_LOAD_UNCHANGED = 2;

  private $factory;

  public function __construct(Factory $factory) {
    $this->factory = $factory;
  }

  public function harvest() {
    $items = $this->extract();

    if (is_string($items)) {
      $result['status']['extract'] = "FAILURE";
      $result['errors']['extract'] = $items;
      return $result;
    }

    $result['status']['extract'] = "SUCCESS";
    $result['status']['extracted_items_ids'] = array_keys($items);


    $result['status']['transform'] = [];

    $transformed_items = [];
    $transformers = $this->factory->get("transforms");
    if ($transformers) {
      /** @var  $transform Transform */
      foreach ($items as $identifier => $item) {
        $transformed_item = clone $item;
        foreach ($transformers as $transformer) {
          $transformer_name = get_class($transformer);
          $transformed_item = $this->transform($transformer, $transformed_item);

          if (is_string($transformed_item)) {
            $result['status']['transform'][$transformer_name][$identifier] = "FAILURE";
            $result['errors']['transform'][$transformer_name][$identifier] = $transformed_item;
            break;
          }
          else {
            $result['status']['transform'][$transformer_name][$identifier] = "SUCCESS";
          }
        }

        if (!is_string($transformed_item)) {
          $transformed_items[$identifier] = $transformed_item;
        }
      }
    }

    if (empty($transformed_items)) {
      return $result;
    }

    $result['status']['load'] = [];

    foreach ($transformed_items as $identifier => $item) {
      $status = $this->load($item);
      if (!is_string($status)) {
        $result['status']['load'][$identifier] = $this->loadStatusToString($status);
      }
      else {
        $result['errors']['load'][$identifier] = $status;
        $result['status']['load'][$identifier] = "FAILURE";
      }
    }

    return $result;
  }

  private function extract() {
    $extract = $this->factory->get('extract');
    try {
      $items = $extract->run();
    }
    catch(\Exception $e) {
      return $e->getMessage();
    }

    return $items;
  }

  private function transform($transformer, $item) {
    $transformed = clone $item;

    try {
      $transformed = $transformer->run($transformed);
    }
    catch(\Exception $e) {
      return $e->getMessage();
    }

    return $transformed;
  }

  private function load($item) {
    try {
      $load = $this->factory->get('load');
      return $load->run($item);
    }
    catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  private function loadStatusToString($status) {
    if ($status === self::HARVEST_LOAD_NEW_ITEM) {
      return "NEW";
    }
    else if ($status === self::HARVEST_LOAD_UPDATED_ITEM) {
      return "UPDATED";
    }
    else if ($status === self::HARVEST_LOAD_UNCHANGED) {
      return "UNCHANGED";
    }
  }
}
