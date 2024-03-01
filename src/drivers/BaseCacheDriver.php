<?php

namespace pribolshoy\yii2repository\drivers;

use pribolshoy\repository\drivers\AbstractCacheDriver;

abstract class BaseCacheDriver extends AbstractCacheDriver
{
    protected string $component = 'redis';

    protected ?object $container = null;

    protected ?object $container_class = null;

    protected function getContainer() :?object
    {
        if (is_null($this->container)) {
            if (is_null($this->container_class))
                throw new \Exception('Не задан контейнер для драйверов кеша');

            if (is_callable($this->container_class)) {
                $this->container = call_user_func($this->container_class);
            } else {
                $class = $this->container_class;
                $this->container = new $class();
            }
        }

        return $this->container ?? null;
    }

    protected function getComponent()
    {
        return $this->getContainer()->{$this->component} ?? null;
    }

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

