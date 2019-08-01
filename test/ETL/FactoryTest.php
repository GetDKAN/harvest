<?php

namespace HarvestTest\ETL;

use HarvestTest\MemStore;
use Harvest\ETL\Factory;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testExtract()
    {
        $this->expectExceptionMessage("Class NoClass does not exist");
        $this->getFactory()->get('extract');
    }

    public function testTransform()
    {
        $this->expectExceptionMessage("Class NoClass does not exist");
        $this->getFactory()->get('transforms');
    }

    public function testLoad()
    {
        $this->expectExceptionMessage("Class NoClass does not exist");
        $this->getFactory()->get('load');
    }

    public function testPlanCheck()
    {
        $this->expectExceptionMessage("Harvest plan must be a php object.");
        new Factory("hello", new MemStore(), new MemStore());
    }

    private function getFactory()
    {
        return new Factory($this->getPlan("badplan2"), new MemStore(), new MemStore());
    }

    private function getPlan($name)
    {
        $path = __DIR__ . "/../json/{$name}.json";
        $content = file_get_contents($path);
        return json_decode($content);
    }
}
