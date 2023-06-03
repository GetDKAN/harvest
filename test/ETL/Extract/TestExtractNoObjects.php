<?php

namespace HarvestTest\ETL\Extract;

use Harvest\ETL\Extract\Extract;

class TestExtractNoObjects extends Extract
{
    protected function getItems(): array
    {
        return ["Hello World!!"];
    }
}
