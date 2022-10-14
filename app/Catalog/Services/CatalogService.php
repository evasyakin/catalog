<?php
/**
 * @deprecated 2022-10-10
 */
namespace Catalog\Services;

use Catalog\Models\Category;
use Core\App;

class CatalogService
{
    public static function globalCategories()
    {
        return Category::findGlobals();
    }

    public static function category(
        int $category_id, 
        int $subLevel = 0, int $productsLevel = 0
    ) {
        $cat = Category::find($category_id);
        if ($subLevel > 0) {
            $cat->subCategories();
        }
        if ($productsLevel > 0) {
            $cat->products();
        }
        return $cat;
    }
}
