<?php

namespace pribolshoy\yii2repository\drivers;

/**
 * Class RedisDriver
 *
 * Redis have many different ways to fetch information,
 * then we can use strategies for setting specific way.
 * Also class have some auto detection of right ways.
 *
 * Strategies can be sets by params property.
 *
 * Available strategies:
 * getValue - Get the value of a key (analog get($key)).
 * getHValue - Get the value of a hash field (analog hget($key, $field)).
 * getHValues - Get the values of all the given hash fields (analog hmget($key, ...$fields)).
 * getAllHash - Get all the values in a hash (analog hvals($key)).
 *
 * @package pribolshoy\yii2repository\drivers
 */
class RedisDriver extends BaseCacheDriver
{
    protected string $component = 'redis';

    public function get(string $key, array $params = [])
    {
        // default strategy
        $strategy = $params['strategy'] ?? 'getValue';
        $fields = $params['fields'] ?? [];

        if ($fields && $strategy !== 'getHValues') {
            $strategy = 'getHValues';
        } else if (preg_match('#:#i', $key) && $strategy !== 'getHValue') {
            $strategy = 'getHValue';
        }

        if (!method_exists($this, $strategy)) {
            throw new \RuntimeException("Метод $strategy не существует в " . __CLASS__);
        }
        return $this->{$strategy}($key, $params) ?? [];
    }

    /**
     * Get all the values in a hash.
     *
     * @param string $key ключ вида somekey, без вложенностей через :
     * @param array $params
     *
     * @return array
     */
    protected function getAllHash(string $key, array $params = [])
    {
        if ($items = $this->getComponent()->hvals($key)) {
            foreach ($items as $item) {
                $data[] = unserialize($item);
            }
        }

        return $data ?? [];
    }

    /**
     * Get the value of a key.
     *
     * @param string $key somekey or somekey:somefield
     * @param array $params
     *
     * @return array|mixed
     */
    protected function getValue(string $key, array $params = [])
    {
        $data = $this->getComponent()->get($key);
        return $data ? unserialize($data) : [];
    }

    /**
     * Get the value of a hash field.
     *
     * @param string $key
     * @param array $params
     *
     * @return array|mixed
     */
    protected function getHValue(string $key, array $params = [])
    {
        $key_parts = explode(':', $key);
        $field = array_pop($key_parts);
        $key = implode(':', $key_parts);

        $data = $this->getComponent()->hget($key, $field);
        return $data ? unserialize($data) : [];
    }

    /**
     * Get the values of all the given hash fields.
     *
     * @param string $key
     * @param array $params
     *
     * @return array
     */
    protected function getHValues(string $key, array $params = [])
    {
        if ($fields = $params['fields'] ?? []) {
            if ($items = $this->getComponent()->hmget($key, ...$fields)) {
                $items = array_filter($items);
                foreach ($items as $item) {
                    $data[] = unserialize($item);
                }
            }
        }

        return $data ?? [];
    }

    /**
     * @param string $key
     * @param $value
     * @param int $cache_duration
     * @param array $params
     *
     * @return object
     */
    public function set(string $key, $value, int $cache_duration = 0, array $params = []) :object
    {
        // по умолчанию хеш таблица
        $strategy = $params['strategy'] ?? 'hset';

        if (!method_exists($this, $strategy)) {
            throw new \RuntimeException("Метод $strategy не существует в " . __CLASS__);
        }

        return $this->{$strategy}($key, $value, $cache_duration, $params);
    }

    protected function setex(string $key, $value, int $cache_duration = 0, array $params = []) :object
    {
        $this->getComponent()->setex($key, $cache_duration, serialize($value));
        return $this;
    }

    protected function hset(string $key, $value, int $cache_duration = 0, array $params = []) :object
    {
        // только если есть разделение на двоеточие
        if (preg_match('#:#', $key)) {
            $keyParts = explode(':', $key);
            $count = count($keyParts);
            $field = $keyParts[$count-1];
            unset($keyParts[$count-1]);
            $key = implode(':', $keyParts);

            $this->getComponent()->hset($key, $field, serialize($value));
            $this->getComponent()->expireat($key, time() + $cache_duration);
        } else {
            $this->setex($key, $value, $cache_duration, $params);
        }
        return $this;
    }

    public function delete(string $key, array $params = []) :object
    {
        // по умолчанию хеш таблица
        $strategy = $params['strategy'] ?? 'hdel';

        if (!method_exists($this, $strategy)) {
            throw new \RuntimeException("Метод $strategy не существует в " . __CLASS__);
        }

        return $this->{$strategy}($key, $params);
    }

    protected function del(string $key, array $params = []) :object
    {
        $this->getComponent()->del($key);
        return $this;
    }

    protected function hdel(string $key, array $params = []) :object
    {
        // только если есть разделение на двоеточие
        if (preg_match('#:#', $key)) {
            $keyParts = explode(':', $key);
            $count = count($keyParts);
            $field = $keyParts[$count-1];
            unset($keyParts[$count-1]);
            $key = implode(':', $keyParts);

            if ($field == '*') {
                $this->del($key);
            } else {
                $this->getComponent()->hdel($key, $field);
            }

        } else {
            $this->del($key, $params);
        }
        return $this;
    }
}

