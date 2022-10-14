<?php
namespace Catalog\Models;

use Catalog\Models\Category;
use Core\Orm\Model;

class Filter extends Model
{
    const TYPE_RANGE = 1;
    const TYPE_RADIO = 2;
    const TYPE_CHECKBOX = 3;

    public static $tableName = 'filters';

    public $value;

    /**
     * Поиск фильтров категории.
     * @param int id категории
     * @return Filter[]
     */
    public static function findByCategory(int $category_id): array
    {
        return static::select('`filters`.`name`, `filters`.`type`')
        ->addSelect('`filters`.`options` as `default_options`')
        ->addSelect('`category_filters`.*')
        ->from('`category_filters`')
        ->join('JOIN `filters` ON `category_filters`.`filter_id` = `filters`.`id`')
        ->where('`category_filters`.`category_id` = ?', [$category_id])
        ->orderBy('`category_filters`.`priority` DESC')
        ->many();
    }
}
// [[10, "от 10% и выше"], [30, "от 30% и выше"], [50, "от 50% и выше"], [70, "от 70% и выше"]]
// [[3, "HP"], [4, "Acer"]]
