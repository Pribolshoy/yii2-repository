<?php

namespace pribolshoy\yii2repository\drivers;

use pribolshoy\repository\drivers\AbstractCacheDriver;

abstract class BaseCacheDriver extends AbstractCacheDriver
{
    protected string $component = 'redis';

    protected ?object $container = null;

    protected ?object $container_class = null;

    /**
     * Get object of Yii2 container.
     *
     * @return object|null
     * @throws \Exception
     */
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

    /**
     * Get component from container.
     *
     * @return object|null
     * @throws \Exception
     */
    protected function getComponent()
    {
        return $this->getContainer()->{$this->component} ?? null;
    }

    /**
     * Get cache by key and params.
     *
     * @param string $key
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public function get(string $key, array $params = [])
    {
        return $this->getComponent()->get($key);
    }

    /**
     * Set cache by key and params.
     *
     * @param string $key
     * @param $value
     * @param int $cache_duration
     * @param array $params
     *
     * @return object
     * @throws \Exception
     */
    public function set(string $key, $value, int $cache_duration = 0, array $params = []) :object
    {
        $this->getComponent()->set($key, $value);
        return $this;
    }

    /**
     * Delete cache by key and params.
     *
     * @param string $key
     * @param array $params
     *
     * @return object
     */
    public function delete(string $key, array $params = []) :object
    {
        return $this;
    }
}

