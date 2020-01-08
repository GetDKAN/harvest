<?php

namespace Harvest;

class ResultInterpreter
{
    private $result;

    public function __construct(array $result)
    {
        $this->result = $result;
    }

    public function countCreated()
    {
        return $this->loadCount("NEW");
    }

    public function countUpdated()
    {
        return $this->loadCount("UPDATED");
    }

    public function countFailed()
    {
        $load_failures = $this->loadCount("FAILURE");
        $transform_failures = $this->transformFailures();
        return $load_failures + $transform_failures;
    }

    public function countProcessed()
    {

        $ids = [];

        if (isset($this->result['status']['load'])) {
            $ids = array_merge($ids, array_keys($this->result['status']['load']));
        }

        if (isset($this->result['status']['transform'])) {
            foreach (array_keys($this->result['status']['transform']) as $transformer) {
                $ids = array_merge($ids, array_keys($this->result['status']['transform'][$transformer]));
            }
        }

        $ids = array_unique($ids);

        return count($ids);
    }

    private function loadCount($status)
    {
        $count = 0;
        if (!isset($this->result['status']['load'])) {
            return $count;
        }

        foreach ($this->result['status']['load'] as $identifier => $stat) {
            if ($stat == $status) {
                $count++;
            }
        }

        return $count;
    }

    private function transformFailures()
    {
        $count = 0;

        if (!isset($this->result['status']['transform'])) {
            return $count;
        }

        foreach ($this->result['status']['transform'] as $transformer => $results) {
            foreach ($results as $result) {
                if ($result == "FAILURE") {
                    $count++;
                }
            }
        }

        return $count;
    }
}
