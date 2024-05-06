<?php

namespace HarvestTest;

use PHPUnit\Framework\TestCase;
use Harvest\Util;

class UtilTest extends TestCase
{
    public function test(): void
    {
        $fake_dataset = "Not an object";
        $this->expectExceptionMessage("The dataset " . json_encode($fake_dataset) . " is not an object.");
        Util::getDatasetId($fake_dataset);
    }
}
