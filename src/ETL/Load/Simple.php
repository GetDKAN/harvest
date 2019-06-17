<?php

namespace Harvest\ETL\Load;


class Simple extends Load {
  protected function saveItem($item)
  {
    $id = $item->identifier;
    $this->storage->store(json_encode($item), $id);
  }

}