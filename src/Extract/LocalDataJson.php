<?php

namespace Harvest\Extract;

use GuzzleHttp\Client;

class LocalDataJson extends Extract {

  public function run(): array
  {
    $items = $this->storage->retrieveAll();

    $this->log('DEBUG', 'extract', 'Running LocalDataJson extraction.');

    if (empty($items)) {
      $this->cache();
      $items = $this->storage->retrieveAll();
    }

    foreach($items as $key => $item) {
      $items[$key] = json_decode($item);
    }
    
    return $items;
  }

  public function cache() {
    $this->log('DEBUG', 'extract', 'Caching LocalDataJson files.');
    
    $file = file_get_contents($this->harvest_plan->source->uri);
    $data = json_decode($file);

    if ($data->dataset) {

      foreach ($data->dataset as $dataset) {

        if (filter_var($dataset->identifier, FILTER_VALIDATE_URL)) {
          $i = explode("/", $dataset->identifier);
          $id = end($i);
        }
        else {
          $id = $dataset->identifier;
        }
        $this->storage->store(json_encode($dataset), $id);

      }
    }
  }
}
