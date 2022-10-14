<?php
namespace Catalog\Models;

use Catalog\Models\Product;
use Core\Orm\Model;

class Category extends Model
{
    public static $tableName = 'categories';

    protected $subCategories;
    protected $products;

    /**
     * Получение глобальных категорий.
     * @return array
     */
    public static function findGlobals(): array
    {
        return static::where('`parent_id` IS NULL')->many();
    }

    /**
     * Поиск категорий по url.
     * @param array urls
     * @return array
     */
    public static function findByUrl(string $url)
    {
        return static::where('`url` = ?', [$url])->one();
    }


    /**
     * Поиск категорий по urls.
     * @param array urls
     * @return array
     */
    public static function findByUrls(array $urls): array
    {
        return static::whereIn('`url`', $urls)->orderBy('`id` ASC')->many();
    }

    /**
     * Получение вложенных категорий.
     * @return array
     */
    public function subCategories(): array
    {
        if (null === $this->subCategories) {
            $this->subCategories = static::select('`categories`.*, `category_parents`.`virtual`')
            ->from('`category_parents`')
            ->join('JOIN `categories` ON `categories`.`id` = `category_parents`.`category_id`')
            ->where('`category_parents`.`parent_id` = ?', [$this->id])
            ->orderBy('`categories`.`priority` DESC')
            ->many();
        }
        return $this->subCategories;
    }

    /**
     * Получение товаров категории.
     * @return array
     */
    public function products(): array
    {
        if (null === $this->products) {
            $this->products = Product::findByCategory($this->id);
        }
        return $this->products;
    }
}
