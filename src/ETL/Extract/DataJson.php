<?php

namespace Harvest\ETL\Extract;

use Dkan\Datastore\Manager\Factory;
use GuzzleHttp\Client;
use Harvest\Storage\Storage;

class DataJson extends Extract {

  protected $harvest_plan;
  protected $storage;

  function __construct($harvest_plan, Storage $storage) {
    $this->harvest_plan = $harvest_plan;
    $this->storage = $storage;
  }

  public function getItemsFromCache() {
    $items = $this->storage->retrieveAll();
    return array_map("json_decode", $items);
  }


  public function setItemsToCache() {
    $file_location = $this->harvest_plan->source->uri;
    if (substr_count($file_location, "file://") > 0) {
      $json = file_get_contents($file_location);
    }
    else {
      $json = $this->httpRequest($file_location);
    }

    $data = json_decode($json);

    if ($data === null) {
      throw new \Exception("Error decoding JSON.");
    }

    if (!isset($data->dataset)) {
      throw new \Exception("data.json does not have a dataste property");
    }

    foreach ($data->dataset as $dataset) {
      $this->storage->store(json_encode($dataset), $this->getDatasetId($dataset));
    }
  }

  private function getDatasetId(object $dataset): string
  {
    if (filter_var($dataset->identifier, FILTER_VALIDATE_URL)) {
      $i = explode("/", $dataset->identifier);
      $id = end($i);
    }
    else {
      $id = $dataset->identifier;
    }
    return "{$id}";
  }

  private function httpRequest($uri) {
    try {
      $client = new Client();
      $res = $client->get($uri);
      $data = (string) $res->getBody();
      return $data;
    }
    catch (\Exception $exception) {
      throw new \Exception("Error reading {$uri}");
    }
  }

}
