<?php

namespace pribolshoy\yii2repository;

use yii\helpers\ArrayHelper;

/**
 * Class ARServiceTrait
 *
 * This trait is for implementation of abstract methods
 * from AbstractService.
 * For using by Yii2 ActiveRecords objects.
 *
 * @package pribolshoy\yii2repository
 */
trait ARServiceTrait
{
    public function getItemPrimaryKey($item)
    {
        return $item->getPrimaryKey();
    }

    public function hasItemAttribute($item, string $name) :bool
    {
        return $item->hasAttribute($name);
    }

    public function getItemAttribute($item, string $name)
    {
        if ($this->hasItemAttribute($item, $name)) {
            return $item->getAttribute($name);
        }

        return null;
    }

    protected function sort(array $items)
    {
        if ($this->sorting) {
            foreach ($this->sorting as $key => $direction) {
                ArrayHelper::multisort($items, $key, $direction);
            }
        }

        return $items;
    }
}

