<?php

namespace pribolshoy\yii2repository;

use pribolshoy\repository\AbstractCachebleService;
use yii\helpers\ArrayHelper;

/**
 * Class AbstractARService
 *
 * @package pribolshoy\yii2repository
 */
abstract class AbstractARService extends AbstractCachebleService
{
    use ARServiceTrait;
}

