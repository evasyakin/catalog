<?php
namespace Catalog\Controllers;

use Catalog\Services\CategoryService;
use Catalog\Services\ProductService;
use Core\App;
use Core\Router\Controller;

class CategoryController extends Controller
{
    public function list()
    {
        $cats = CategoryService::globalCategories();
        $this->view('catalog/categories.php', compact('cats'));
    }

    public function one(string $url)
    {
        // var_dump(App::request()->getProp('price'));
        @[$cat, $cat_ids, $filters] = CategoryService::categoryByUrl($url);

        // check filters
        foreach ($filters as $filter) {
            $filter_value = App::request()->getProp($filter->product_field);
            if (!empty($filter_value)) {
                $filter->value = explode(';', $filter_value);
            }
        }

        $products = ProductService::productsList($cat_ids, $filters);

        // $products = ProductService::productsByCategories($cat_ids);
        $products2 = ProductService::productsByCategoriesWithDuplicates($cat_ids);
        

        $this->view('catalog/category.php', compact(
            'cat', 'cat_ids', 'products', 'filters',
            'products2'
        ));
        // echo $cat;
        // echo json_encode($cat, JSON_UNESCAPED_UNICODE);
    }
}
