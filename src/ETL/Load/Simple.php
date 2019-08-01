<?php

namespace Harvest\ETL\Load;

class Simple extends Load
{
    protected function saveItem($item)
    {
        $id = $item->identifier;
        if (!isset($item->accessLevel)) {
            throw new \Exception("Access level is required");
        }
        $this->itemStorage->store(json_encode($item), $id);
    }

    public function removeItem($id)
    {
        $this->itemStorage->remove($id);
    }
}
