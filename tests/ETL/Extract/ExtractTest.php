<?php


class ExtractTest extends \PHPUnit\Framework\TestCase
{
  public function testNoItems() {
    $this->expectExceptionMessage("No Items were extracted.");
    (new TestExtract())->run();
  }

  public function testNoObjects() {
    $item = json_encode("Hello World!!");
    $this->expectExceptionMessage("The items extracted are not php objects: {$item}");
    (new TestExtractNoObjects())->run();
  }
}

class TestExtract extends \Harvest\ETL\Extract\Extract {
  protected function getItems()
  {
    return [];
  }
}

class TestExtractNoObjects extends \Harvest\ETL\Extract\Extract {
  protected function getItems()
  {
    return ["Hello World!!"];
  }
}