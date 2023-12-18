<?php

namespace pribolshoy\yii2repository;

use pribolshoy\repository\AbstractCachebleService;

/**
 * Class AbstractARService
 *
 * @package pribolshoy\yii2repository
 */
abstract class AbstractARService extends AbstractCachebleService
{
    use ARServiceTrait;
}

