<?php

namespace Harvest;

use Drupal\dkan_api\Storage\DrupalNodeDataset;
use Harvest\Log\MakeItLog;
use Harvest\Storage\Hash;

class Reverter {
  use MakeItLog;

  public $sourceId;

  function __construct($sourceId) {
    $this->sourceId = $sourceId;
  }

  function run() {
    $this->log('DEBUG', 'revert', 'Reverting harvest ' . $this->sourceId);

    $hash_storage = new Hash();

    $uuids = $hash_storage->readIdsBySource($this->sourceId);

    $datastore_storage = new DrupalNodeDataset();

    $counter = 0;
    foreach ($uuids as $uuid) {
      $datastore_storage->remove($uuid);
      $counter++;
    }
    return $counter;
  }

}
