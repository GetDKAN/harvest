<?php

namespace Harvest\ETL\Extract;

use Harvest\Log\MakeItLog;
use Harvest\Storage\Storage;

abstract class Extract implements IExtract {

  /**
   * {@inheritDoc}
   */
  public function run(): array
  {
    if (empty($this->getItemsFromCache())) {
      $this->setItemsToCache();
    }

    $items = $this->getItemsFromCache();

    if (empty($items)) {
      throw new \Exception("No Items were extracted.");
    }

    $copy = array_values($items);
    if (!is_object($copy[0])) {
      throw new \Exception("The items extracted are not php objects: {json_encode($copy[0])}");
    }

    return $items;
  }

  abstract public function setItemsToCache();

  abstract public function getItemsFromCache();
}
