<?php

use Harvest\Harvester;

class HarvesterTest extends \PHPUnit\Framework\TestCase {

  public function testPlanValidation() {
    $this->expectExceptionMessage("{\"valid\":false,\"errors\":[{\"property\":\"load.type\",\"pointer\":\"\/load\/type\",\"message\":\"The property type is required\",\"constraint\":\"required\",\"context\":1}]}");
    $plan = $this->getPlan("badplan");
    new Harvester($plan, new MemStore(), new MemStore(), new MemStore());
  }

  public function testBasicData() {
    return [
      ["file://" . __DIR__ . "/json/data.json"],
      ["http://demo.getdkan.com/data.json"]
    ];
  }

  /**
   * @dataProvider testBasicData
   */
  public function testBasic($uri) {
    $plan = $this->getPlan("plan");
    $plan->source->uri = $uri;
    $item_store = new MemStore();
    $harvester = new Harvester($plan, $item_store, new MemStore(), new MemStore());

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

  public function testBadUri() {
    $uri = "httpp://asdfnde.exo/data.json";
    $this->expectExceptionMessage('Error reading httpp://asdfnde.exo/data.json');

    $plan = $this->getPlan("plan");
    $plan->source->uri = $uri;
    $item_store = new MemStore();
    $harvester = new Harvester($plan, $item_store, new MemStore(), new MemStore());
    $harvester->harvest();
  }

  private function getPlan($name) {
    $path = __DIR__ . "/json/{$name}.json";
    $content = file_get_contents($path);
    return json_decode($content);
  }
}

class MemStore extends \Contracts\Mock\Storage\Memory implements \Harvest\Storage\Storage {

}