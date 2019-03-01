<?php

namespace Harvest\Transform;

class JsonDecode extends Transform
{
  function run(&$items)
  {
    foreach ($items as $key => $item) {
      $items[$key] = json_decode($item);
    }
  }

}