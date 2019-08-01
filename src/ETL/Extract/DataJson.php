<?php

namespace Harvest\ETL\Extract;

use GuzzleHttp\Client;
use Harvest\Util;

class DataJson extends Extract
{

    protected $harvest_plan;

    public function __construct($harvest_plan)
    {
        $this->harvest_plan = $harvest_plan;
    }

    public function getItems()
    {
        $file_location = $this->harvest_plan->extract->uri;
        if (substr_count($file_location, "file://") > 0) {
            $json = file_get_contents($file_location);
        } else {
            $json = $this->httpRequest($file_location);
        }

        $data = json_decode($json);

        if ($data === null) {
            throw new \Exception("Error decoding JSON.");
        }

        if (!isset($data->dataset)) {
            throw new \Exception("data.json does not have a dataste property");
        }

        $datasets = [];
        foreach ($data->dataset as $dataset) {
            $datasets[Util::getDatasetId($dataset)] = $dataset;
        }
        return $datasets;
    }

    private function httpRequest($uri)
    {
        try {
            $client = new Client();
            $res = $client->get($uri);
            $data = (string) $res->getBody();
            return $data;
        } catch (\Exception $exception) {
            throw new \Exception("Error reading {$uri}");
        }
    }
}
