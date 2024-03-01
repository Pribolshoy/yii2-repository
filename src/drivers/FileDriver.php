<?php

namespace pribolshoy\yii2repository\drivers;

class FileDriver extends BaseCacheDriver
{
    protected string $component = 'cache';

    public function get(string $key, array $params = [])
    {
        $this->getComponent()->get($key);
    }

    public function set(string $key, $value, int $cache_duration = 0, array $params = []) :object
    {
        $this->getComponent()->set($key, $value);
        return $this;
    }

    public function delete(string $key, array $params = []) :object
    {
        return $this;
    }
}

