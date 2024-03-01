<?php

namespace pribolshoy\yii2repository\drivers;

class MysqlDriver extends BaseCacheDriver
{
    protected string $component = 'dbCache';

    public function get(string $key, array $params = [])
    {
        return $this->getComponent()->get($key);
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

