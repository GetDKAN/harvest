<?php

namespace Harvest\Extract;

use GuzzleHttp\Client;

class DataJson extends Extract {

  public function run(): array
  {
    $items = $this->storage->retrieveAll();

    $this->log('DEBUG', 'extract', 'Running DataJson extraction.');

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
    $this->log('DEBUG', 'extract', 'Caching DataJson files.');

    $json = $this->httpRequest($this->harvest_plan->source->uri);
    $data = json_decode($json);

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

  private function httpRequest($uri) {
    try {
      $client = new Client();
      $res = $client->get($uri);
      $data = (string) $res->getBody();
      return $data;
    }
    catch (\Exception $exception) {
      $this->log('ERROR', 'Extract', 'Error reading ' . $uri);
    }
  }

}
