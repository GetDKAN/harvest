<?php

namespace Harvest\Extract;

use Harvest\Log\MakeItLog;
use Harvest\Storage\Storage;

abstract class Extract {

  use MakeItLog;

  protected $harvest_plan;
  protected $storage;


  function __construct($harvest_plan, Storage $storage) {
    $this->harvest_plan = $harvest_plan;
    $this->storage = $storage;
  }

  abstract public function run();

  abstract public function cache();


  /*
  protected function writeToFile($id, $item) {
    try {
      $harvestFolder = $this->folder . '/' . $this->sourceId;
      if (!file_exists($harvestFolder)) {
        mkdir($harvestFolder, 0777, true);
      }
      $file =  $harvestFolder . '/' . $id . '.json';
      $handle = fopen($file, 'w');
      if ( !$handle ) {
        throw new Exception('File open failed.');
      }
      fwrite($handle, $item);
      fclose($handle);
    } catch ( Exception $e ) {
      // Let's log.

    }
  }*/

}
