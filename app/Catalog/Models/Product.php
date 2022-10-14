<?php
namespace Catalog\Models;

use Core\Orm\Model;

class Product extends Model
{
    public static $tableName = 'products';

    /**
     * Поиск товаров категории.
     * @param int id категории
     * @return array
     */
    public static function findByCategory(int $category_id): array
    {
        return static::select('`products`.*')->from('`category_products`')
        ->join('JOIN `products` ON `category_products`.`product_id` = `products`.`id`')
        ->where('`category_id` = ?', [$category_id])
        ->orderBy('`products`.`priority` DESC')
        ->many();
    }

    /**
     * Поиск товаров категорий.
     * @param array id категорий
     * @return array
     */
    public static function findByCategories(array $category_ids): array
    {
        return static::select('DISTINCT `products`.*')->from('`category_products`')
        ->join('JOIN `products` ON `category_products`.`product_id` = `products`.`id`')
        ->whereIn('`category_id`', $category_ids)
        ->orderBy('`products`.`priority` DESC')
        ->many();
    }
}
