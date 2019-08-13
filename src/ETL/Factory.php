<?php

namespace Harvest\ETL;

use Harvest\Storage\StorageInterface;
use Opis\JsonSchema\Validator;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Schema;

class Factory
{

    public $harvestPlan;
    public $itemStorage;
    public $hashStorage;

    public function __construct(
        $harvest_plan,
        $item_storage,
        $hash_storage
    ) {
        if (self::validateHarvestPlan($harvest_plan)) {
            $this->harvestPlan = $harvest_plan;
        }
        $this->itemStorage = $item_storage;
        $this->hashStorage = $hash_storage;
    }

    public function get($type)
    {

        if ($type == "extract") {
            $class = $this->harvestPlan->extract->type;

            if (!class_exists($class)) {
                throw new \Exception("Class {$class} does not exist");
            }

            return new $class($this->harvestPlan);
        } elseif ($type == "load") {
            $class = $this->harvestPlan->load->type;

            if (!class_exists($class)) {
                throw new \Exception("Class {$class} does not exist");
            }

            return  new $class($this->harvestPlan, $this->hashStorage, $this->itemStorage);
        } elseif ($type == "transforms") {
            $transforms = [];
            if (isset($this->harvestPlan->transforms)) {
                foreach ($this->harvestPlan->transforms as $info) {
                    $config = null;
                    $class = $info;

                    if (!class_exists($class)) {
                        throw new \Exception("Class {$class} does not exist");
                    }

                    $transforms[] = $this->getOne($class, $this->harvestPlan);
                }
            }

            return $transforms;
        }
    }

    private function getOne($class, $config = null)
    {
        if (!$config) {
            $config = $this->harvestPlan;
        }
        return new $class($config);
    }

    public static function validateHarvestPlan($harvest_plan)
    {
        if (!is_object($harvest_plan)) {
            throw new \Exception("Harvest plan must be a php object.");
        }

        $path_to_schema = __DIR__ . "/../../schema/schema.json";
        $json_schema = file_get_contents($path_to_schema);

        $data = $harvest_plan;
        $schema = Schema::fromJsonString($json_schema);
        $validator = new Validator();

      /** @var $result ValidationResult */
        $result = $validator->schemaValidation($data, $schema);

        if (!$result->isValid()) {
          /** @var $error ValidationError */
            $error = $result->getFirstError();
            throw new \Exception(
                "Invalid harvest plan. " . implode("->", $error->dataPointer()) .
                " " . json_encode($error->keywordArgs())
            );
        }

        return true;
    }
}
