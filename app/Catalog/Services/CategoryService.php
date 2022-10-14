<?php
namespace Catalog\Services;

use Catalog\Models\Category;
use Catalog\Models\Filter;

class CategoryService
{
    /**
     * Получение глобальных категорий.
     * @return array
     */
    public static function globalCategories()
    {
        return Category::findGlobals();
    }

    /**
     * Получение категории с вложенными категориями по seo url.
     * @param strin url
     * @return array [категория, id найденных категорий]
     * @throws \RuntimeException
     */
    public static function categoryByUrl(string $url)
    {
        if ('/' === mb_substr($url, mb_strlen($url) - 1)) {
            $url = mb_substr($url, 0, mb_strlen($url)- 1);
        }
        $cat = Category::findByUrl($url);
        if (!$cat) throw new \RuntimeException('Not found category by url');
        
        $cat_ids = static::categoryRecoursive($cat, $cat->subcat_level);
        $filters = Filter::findByCategory($cat->id);
        // $parents = static::categoryParents($url);
        return [$cat, $cat_ids, $filters];
    }

    /**
     * Вспомогательный метод для рекурсивной подгрузки вложенных директорий
     * до установленного уровня.
     * @param Category категория
     * @param int оставшиеся уровни поиска
     * @return array id найденных категорий
     */
    protected static function categoryRecoursive(Category &$cat, int $level = 0): array
    {
        $cat_ids = [$cat->id];
        if ($level > 0) {
            $level--;
            foreach ($cat->subCategories() as &$sub) {
                $cat_ids = array_merge($cat_ids, static::categoryRecoursive($sub, $level));
            }
        }
        return $cat_ids;
    }

    /**
     * Получение иерархии родительских категорий.
     */
    protected static function categoryParents(string $url)
    {
        $parts = explode('/', trim($url, '/'));
        array_pop($parts);
        foreach ($parts as $i => &$part) {
            $part = implode('/', array_slice($parts, 0, $i + 1));
        }
        $cats = Category::findByUrls($parts);
        echo '<pre>';
        var_dump($cats);
        echo '</pre>';
    }



    // public static function categoryByUrls(array $urls)
    // {
    //     $cats = Category::findByUrls($urls);
    //     $findCat = function ($url) use (&$cats) {
    //         foreach ($cats as $i => $cat) {
    //             if ($cat->url === $url) {
    //                 array_splice($cats, $i, 1);
    //                 return $cat;
    //             }
    //         }
    //         return null;
    //     };
    //     $res = [];
    //     foreach ($urls as $i => $url) {
    //         $cat = $findCat($url);
    //         if (!$cat) {
    //             throw new \RuntimeException('Invalid categories hierarchy');
    //         }
    //         if ($i > 0) {
    //             if ($cat->parent_id !== $res[$i - 1]->id) {
    //                 $parent_id = $res[$i - 1]->id;
    //                 // доолнительный запрос категории
    //                 $cat = Category::build(function ($builder) use ($parent_id, $url) {
    //                     $builder->where(
    //                         '`parent_id` = ? AND `url` = ?', [$parent_id, $url]
    //                     )->limit(1);
    //                 });
    //                 if ($cat->parent_id !== $parent_id) {
    //                     throw new \RuntimeException('Invalid categories hierarchy');
    //                 }
    //             }
    //         }
    //         $res[] = $cat;

    //     }
    //     return $res;
    // }
}
