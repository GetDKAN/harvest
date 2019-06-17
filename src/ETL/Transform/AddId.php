<?php

namespace Harvest\ETL\Transform;

class AddId extends Transform {

  function run(&$items) {
    foreach ($items as $key => $item) {
      $item->_id = $key;
      $items[$key] = $item;
    }
  }

}