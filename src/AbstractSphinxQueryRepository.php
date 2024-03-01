<?php

namespace pribolshoy\yii2repository;

use pribolshoy\repository\AbstractCachebleRepository;
use pribolshoy\yii2repository\helpers\ARHelper;
use yii\db\Expression;

/**
 * Class AbstractSphinxQueryRepository
 *
 * Реализует поиск товаров через Sphinx.
 *
 * @package pribolshoy\yii2repository
 */
abstract class AbstractSphinxQueryRepository extends AbstractCachebleRepository
{
    use ARHelper;

    protected ?string $driver_path = "\\pribolshoy\\yii2repository\\drivers\\";

    /** @var string каталог в сфинксе */
    public ?string $catalog = null;

    public bool $show_meta = false;

    protected ?string $model_class = 'yii\sphinx\Query';

    protected array $query = [];

    public function __construct($params = [], $model_class = null) {
        if ($model_class) {
            throw new \RuntimeException("Установка своей модели в этом классе запрещена");
        }
        parent::__construct($params, $model_class);
    }

    /**
     * @return $this
     * @throws \pribolshoy\exceptions\RepositoryException
     */
    protected function makeQueryBuilder() :object
    {
        $this->model = $this->getModel();
        return $this;
    }

    /**
     * Возвращает экземпляр Expression который используется для поиска.
     * Переопределяется в наследнике.
     *
     * @param string $search
     * @return Expression
     */
    protected function getExpression(string $search) :Expression
    {
        return new Expression(':field', [
            'field' => '(@field ' . $search . ')'
        ]);
    }

    public function getTableName(): string
    {
        return $this->catalog;
    }

    protected function addQueries()
    {
        if ($this->existsFilter('search')) {
            $expression = $this->getExpression($this->getFilter('search'));
            $this->model->match($expression);
        }
        return $this;
    }

    protected function fetch(): object
    {
        $this->getTotal();
        $this->addLimitAndOffset();
        $this->items = $this->model->select('*')
            ->addOptions($this->query)
            ->from($this->catalog)
            ->showMeta($this->show_meta)
            ->all() ?? [];

        return $this;
    }

    /**
     * Turn it off
     * @return AbstractSphinxQueryRepository
     */
    protected function getTotal()
    {
        return $this;
    }

    protected function addLimitAndOffset() :object
    {
        $this->model
            ->limit($this->filter['limit'])
            ->offset($this->filter['offset'] ?? 0);
        return $this;
    }

    public function getHashName(bool $refresh = false, bool $use_params = true, bool $save_to = true) :string
    {
        // если он уже задан и нет флага "обновить"
        if ($this->hash_name && !$refresh) {
            return $this->hash_name;
        } else {
            $hash_name = $this->getTableName();
            if ($use_params && $this->filter) {
                // таблица
                $hash_name = $hash_name . ':' . $this->getHashFromArray($this->getFilters(), true);
            }
            if ($save_to) $this->hash_name = $hash_name = trim($hash_name, '&');
        }

        return $hash_name ?? '';
    }
}

