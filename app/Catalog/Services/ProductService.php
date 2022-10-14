<?php
namespace Catalog\Services;

use Catalog\Models\Filter;
use Catalog\Models\Product;

class ProductService
{
    /**
     * Поиск товаров по категориям.
     * Для вывода товаров с учётом вложенных категорий страницы.
     * @param int[] id категорий
     * @return Product[]
     */
    public static function productsByCategories(array $category_ids): array
    {
        return Product::findByCategories($category_ids);
    }

    /**
     * Поиск товаров по категориям с дублированием товаров.
     * Для демонстрации иерархии категорий и товаров.
     * @param int[] id категорий
     * @return Product[]
     */
    public static function productsByCategoriesWithDuplicates(array $category_ids): array
    {
        return Product::select('`products`.*, `category_products`.`category_id`')
        ->from('`category_products`')
        ->join('JOIN `products` ON `category_products`.`product_id` = `products`.`id`')
        ->whereIn('`category_products`.`category_id`', $category_ids)
        ->orderBy('`products`.`priority` DESC')
        ->many();
    }


    public static function productsList(array $category_ids, array $filters = null): array
    {
        $qb = Product::select('`products`.*, `category_products`.`category_id`')
        ->from('`category_products`')
        ->join('JOIN `products` ON `category_products`.`product_id` = `products`.`id`')
        ->whereIn('`category_products`.`category_id`', $category_ids);

        // Apply filters
        if (!empty($filters)) {
            foreach ($filters as &$filter) {
                if (empty($filter->value)) continue;
                if ($filter->type === Filter::TYPE_RANGE) {
                    $sql = '`products`.`' . $filter->product_field .'`';
                    $sql .= count($filter->value) < 2 ? ' > ?' : ' BETWEEN ? AND ?';
                    $qb->where($sql, $filter->value);
                } else if ($filter->type === Filter::TYPE_RADIO) {
                    $sql = '`products`.`'. $filter->product_field . '` >= ?';
                    $qb->where($sql, $filter->value);
                } else if ($filter->type === Filter::TYPE_CHECKBOX) {
                    $qb->whereIn(
                        '`products`.`' . $filter->product_field .'`',
                        $filter->value
                    );
                }
            }
        }
        
        return $qb->orderBy('`products`.`priority` DESC')->many();
    }
}
