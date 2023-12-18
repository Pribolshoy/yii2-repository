<?php

namespace pribolshoy\yii2repository\drivers;

class MysqlDriver extends BaseCacheDriver
{
    protected string $component = 'dbCache';

    public function delete(string $key, array $params = []) :object
    {
        return $this;
    }
}

