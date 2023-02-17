<?php

namespace HarvestTest;

use Harvest\Harvester;

class HarvesterTest extends \PHPUnit\Framework\TestCase
{

    public function testPlanValidation()
    {
        $this->expectExceptionMessage("Invalid harvest plan. load {\"missing\":\"type\"}");
        $plan = $this->getPlan("badplan");
        $this->getHarvester($plan, new MemStore(), new MemStore());
    }

    public function basicData()
    {
        return [
        ["file://" . __DIR__ . "/json/data.json"],
        ["https://demo.getdkan.org/data.json"]
        ];
    }

  /**
   * @dataProvider basicData
   */
    public function testBasic($uri)
    {
        $plan = $this->getPlan("plan");
        $plan->extract->uri = $uri;
        $item_store = new MemStore();
        $hash_store = new MemStore();

        $harvester = $this->getHarvester($plan, $item_store, $hash_store);

        $result = $harvester->harvest();

        $interpreter = new \Harvest\ResultInterpreter($result);

        $this->assertEquals(10, $interpreter->countCreated());
        $this->assertEquals(0, $interpreter->countUpdated());
        $this->assertEquals(0, $interpreter->countFailed());
        $this->assertEquals(10, $interpreter->countProcessed());
        $this->assertEquals(10, count($item_store->retrieveAll()));

        $result = $harvester->harvest();

        $interpreter = new \Harvest\ResultInterpreter($result);

        $this->assertEquals(0, $interpreter->countCreated());
        $this->assertEquals(0, $interpreter->countUpdated());
        $this->assertEquals(0, $interpreter->countFailed());
        $this->assertEquals(10, $interpreter->countProcessed());
        $this->assertEquals(10, count($item_store->retrieveAll()));

        if (substr_count($uri, "file://") > 0) {
            $plan->extract->uri = str_replace("data.json", "data2.json", $uri);
            $harvester = $this->getHarvester($plan, $item_store, $hash_store);

            $result = $harvester->harvest();
            $interpreter = new \Harvest\ResultInterpreter($result);

            $this->assertEquals(1, $interpreter->countCreated());
            $this->assertEquals(1, $interpreter->countUpdated());
            $this->assertEquals(2, $interpreter->countFailed());
            $this->assertEquals(10, $interpreter->countProcessed());
            $this->assertEquals(11, count($item_store->retrieveAll()));
        }

        $harvester->revert();

        if (substr_count($uri, "file://") > 0) {
            $expected = 1;
        } else {
            $expected = 0;
        }

        $this->assertEquals($expected, count($item_store->retrieveAll()));
    }

    public function testBadUri()
    {
        $uri = "httpp://asdfnde.exo/data.json";

        $plan = $this->getPlan("plan");
        $plan->extract->uri = $uri;

        $harvester = $this->getHarvester($plan, new MemStore());
        $result = $harvester->harvest();
        $this->assertEquals("FAILURE", $result['status']['extract']);
    }

    private function getPlan($name)
    {
        $path = __DIR__ . "/json/{$name}.json";
        $content = file_get_contents($path);
        return json_decode($content);
    }

    private function getHarvester($plan, $item_store = null, $hash_store = null)
    {

        if (!isset($item_store)) {
            $item_store = new MemStore();
        }

        if (!isset($hash_store)) {
            $hash_store = new MemStore();
        }

        $factory = new \Harvest\ETL\Factory($plan, $item_store, $hash_store);
        return new Harvester($factory);
    }
}
