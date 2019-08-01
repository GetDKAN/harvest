<?php

namespace HarvestTest\ETL\Extract;

use PHPUnit\Framework\TestCase;

class ExtractTest extends TestCase
{
    public function testNoItems()
    {
        $this->expectExceptionMessage("No Items were extracted.");
        (new TestExtract())->run();
    }

    public function testNoObjects()
    {
        $item = json_encode("Hello World!!");
        $this->expectExceptionMessage("The items extracted are not php objects: {$item}");
        (new TestExtractNoObjects())->run();
    }
}
