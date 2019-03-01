<?php

use Harvest\Harvester;

class HarvesterTest extends \PHPUnit\Framework\TestCase {

  public function testPlanValidation() {
    $this->expectExceptionMessage("{\"valid\":false,\"errors\":[{\"property\":\"load.type\",\"pointer\":\"\/load\/type\",\"message\":\"The property type is required\",\"constraint\":\"required\",\"context\":1}]}");

    $plan = $this->getPlan("badplan");
    new Harvester($plan, new MemStore(), new MemStore(), new MemStore());
  }

  public function testBasic() {
    $plan = $this->getPlan("plan");
    $item_store = new MemStore();
    $harvester = new Harvester($plan, $item_store, new MemStore(), new MemStore());
    $harvester->setLogger(new \Harvest\Log\Stdout(true, 'test', '1'));

    $results = $harvester->harvest();
    $this->assertEquals(10, $results['created']);
    $this->assertEquals(0, $results['updated']);
    $this->assertEquals(0, $results['skipped']);

    $this->assertEquals(10, count($item_store->retrieveAll()));

    $results = $harvester->harvest();
    $this->assertEquals(0, $results['created']);
    $this->assertEquals(0, $results['updated']);
    $this->assertEquals(0, $results['skipped']);
  }

  private function getPlan($name) {
    $path = __DIR__ . "/json/{$name}.json";
    $content = file_get_contents($path);
    return json_decode($content);
  }

}

class MemStore extends \Contracts\Mock\Storage\Memory implements \Harvest\Storage\Storage {

}