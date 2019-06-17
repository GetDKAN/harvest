<?php

namespace Harvest\ETL\Load;

use Harvest\Log\MakeItLog;
use Harvest\Storage\Storage;
use Harvest\Util;

abstract class Load {

  const NEW_ITEM = 0;
  const UPDATED_ITEM = 1;
  const SAME_ITEM = 2;

  protected $harvestPlan;
  protected $storage;

  private $results = [
    'created' => 0,
    'updated' => 0,
    'skipped' => 0
  ];

  abstract protected function saveItem($item);

  function __construct($harvest_plan, Storage $storage) {
    $this->harvestPlan = $harvest_plan;
    $this->storage = $storage;
  }

  public function run($items) {

    foreach ($items as $item) {
      try {
        $state = $this->itemState($item);

        if ($state == self::NEW_ITEM || $state == self::UPDATED_ITEM) {

          $this->saveItem($item);

          $identifier = $item->identifier;

          if ($state == self::NEW_ITEM) {
            $this->results['created']++;
          }
          else {
            $this->results['updated']++;
          }

          $hash = Util::generateHash($item);
          $object = (object) ['harvest_plan_id' => $this->harvestPlan->identifier, "hash" => $hash];
          $this->storage->store(json_encode($object), $identifier);
        }
      }
      catch (\Exception $e) {
        $this->log("ERROR", "Harvest:Load:SaveItem", $e->getMessage());
        $this->results['skipped']++;
      }
    }

    return $this->results;
  }

  private function itemState($item) {
    if (isset($item->identifier)) {
      $identifier = $item->identifier;

      $json = $this->storage->retrieve($identifier);

      $hash = NULL;
      if (isset($json)) {
        $data = json_decode($json);
        $hash = $data->hash;
      }

      if (isset($hash)) {
        $new_hash = Util::generateHash($item);
        if ($hash == $new_hash) {
          return self::SAME_ITEM;
        }
        else {
          return self::UPDATED_ITEM;
        }
      }
      else {
        return self::NEW_ITEM;
      }
    }
    else {
      throw new \Exception("Item does not have an identifier " . json_encode($item));
    }

  }

}
