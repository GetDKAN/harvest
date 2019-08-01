<?php

namespace Harvest\ETL\Transform;

class AddId extends Transform
{
    public function run($item): object
    {
        $copy = clone $item;
        if (isset($item->identifier)) {
            if ($item->identifier == "1234") {
                throw new \Exception("Identifier can not be 1234");
            }
            $copy->_id = $item->identifier;
        } else {
            throw new \Exception("Item does not have an identifier " . json_encode($item));
        }
        return $item;
    }
}
