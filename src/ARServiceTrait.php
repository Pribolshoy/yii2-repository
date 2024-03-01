<?php

namespace pribolshoy\yii2repository;

use yii\helpers\ArrayHelper;

/**
 * Class ARServiceTrait
 * This trait is for implementation of abstract methods
 * from AbstractCachebleService.
 * For using by Yii2 ACtiveRecords objects
 *
 * @package pribolshoy\yii2repository
 */
trait ARServiceTrait
{
    public function getItemPrimaryKey($item)
    {
        if ($result = parent::getItemPrimaryKey($item)) {
            return $result;
        }

        if (is_object($item)) {
            return $item->getPrimaryKey();
        }

        return null;
    }

    public function sort(array $items): array
    {
        if ($this->sorting) {
            foreach ($this->sorting as $key => $direction) {
                ArrayHelper::multisort($items, $key, $direction);
            }
        }

        return $items;
    }
}

