<?php

namespace HarvestTest;

class UtilTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $fake_dataset = "Not an object";
        $this->expectExceptionMessage("The dataset " . json_encode($fake_dataset) . " is not an object.");
        \Harvest\Util::getDatasetId($fake_dataset);
    }
}
