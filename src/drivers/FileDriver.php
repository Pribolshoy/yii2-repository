<?php

namespace pribolshoy\yii2repository\drivers;

class FileDriver extends BaseCacheDriver
{
    protected string $component = 'file';

    public function delete(string $key, array $params = []) :object
    {
        return $this;
    }
}

