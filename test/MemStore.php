<?php

namespace HarvestTest;

use Contracts\Mock\Storage\Memory;
use Harvest\Storage\StorageInterface;

class MemStore extends Memory implements StorageInterface
{
    public function retrieveAll(): array
    {
        return array_keys(parent::retrieveAll());
    }
}
