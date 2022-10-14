<?php
use Core\App;
use Core\Loader;
use Core\Router\Router;

use Catalog\Controllers\CategoryController;

// вывод ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// вспомогательные константы
define('APP_DIR', __DIR__ . '/');
define('DEBUG', true);

// автозагрузчик
include_once APP_DIR . 'app/core/Loader.php';
(new Loader)->dir('app/')->run();

// подключаем вспомогательные функции
include_once APP_DIR . 'app/core/functions.php';

// var_dump(App::request()->getProps(['a', 'b']));
// var_dump(App::request()->getPropsList(['a', 'b']));
// exit();

// $rows = App::db()->query('SELECT * FROM `categories`');
// echo $rows->rowCount() . '<hr>';
// foreach ($rows->assocArrayAll() as &$row) {
//     echo $row['name'] . '<br>';
// }

(new Router)
->viewsDir('views/')
->default('404.php')
->get('/catalog', [CategoryController::class => 'list'])
// ->get('/catalog/(:id)', [CategoryController::class => 'one'])
->get('/catalog/(:any)', [CategoryController::class => 'one'])
// ->get('/catalog/(:id)', function (int $id) { var_dump($id, $this);})
// ->get('/catalog/(:id)', '404.php')
->requestRouting();

exit();

// function showCategory(
//     $category,
//     bool $showSubs = false, bool $showProducts = false
// ) {
//     $title = showTitle($category);
//     if ($showSubs) $subs = $category->subCategories();
//     if ($showProducts) $products = $category->products();
//     if (($showSubs && !empty($subs)) || ($showProducts && !empty($products))) {
//         $out = '<details class="category" open><summary>'. $title .'</summary><div class="subs">';
//         if ($showSubs && !empty($subs)) {
//             $out .= showCategories($subs, $showSubs, $showProducts);
//         }
//         if ($showProducts && !empty($products)) {
//             $out .= showProducts($products);
//         }
//         $out .= '</div></details>';
//     } else {
//         $out = '<div class="category">'. $title .'</div>';
//     }
//     return $out;
// }

// function showCategories($categories, bool $showSubs = false, bool $showProducts = false) {
//     if (empty($categories)) return '';
//     $out = '<div class="categories">';
//     foreach ($categories as $category) {
//         $out .= showCategory($category, $showSubs, $showProducts);
//     }
//     return $out . '</div>';
// }

// function showProducts(array $products) {
//     if (empty($products)) return '';
//     $out = '<div class="products">';
//     foreach ($products as $product) {
//         $out .= showProduct($product);
//     }
//     return $out . '</div>';
// }

// function showProduct($product) {
//     return '<div class="product">' . showTitle($product) . '</div>';
// }

// function showTitle(object $object) {
//     $type = '';
//     if ($object instanceof \Catalog\Models\Category) $type = 'category';
//     else if ($object instanceof \Catalog\Models\Product) $type = 'product';
//     if (!empty($type)) $type = "<span class=\"type {$type}\">{$type}</span>";
//     return '<p class="title">'. $type .'<span class="id">'. $object->id .'</span>'
//     . '<span class="name">'. $object->name .'</span></p>';
// }

?>
<!-- <style type="text/css">
* {box-sizing: border-box; margin: 0; outline: none; padding: 0; vertical-align: baseline;}
body {font-family: 'Open Sans'; font-size: 16px; padding: 5px;}
.debug {background: #333; border-radius: 5px; color: #fff; font-size: 15px; margin: 5px 0; padding: 5px 10px;}
.debug b {color: #aaa; font-weight: normal; margin-right: 5px;}

summary {cursor: pointer;}
.title {align-items: center; display: flex;}
summary > .title {display: inline-flex;}
.category {}
.category .id {color: #555; font-size: 13px; margin-right: 2px;}
.category .id::before, .category .id::after {display: inline-block;}
.category .id::before {content: '[';}
.category .id::after {content: ']';}
.category .name {}
.type {background: #ddd; border-radius: 4px; font-size: 11px; margin-right: 5px; padding: 2px 6px;}
.type.category {background: #fa0; display: none;}
.type.product {background: #af0;}

details > .subs {margin-left: 15px;}

</style> -->

<?php

// $rows = \Catalog\Services\CategoryService::globalCategories();
// echo showCategories($rows, true, true);

// $cat = \Catalog\Models\Category::find(3);
// $product = \Catalog\Models\Product::find(1);
// var_dump($cat->products());

// foreach ($rows as $row) {
//     echo showCategory($row, true, true);
//     // var_dump($row->subCategories());
//     // foreach ($row as $key => $value) {
//     //     echo $value . ' | ';
//     // }
// }


// $rows = $db->arrayQuery('SELECT * FROM `categories`');
// echo $rows[1] . '<hr>';
// foreach ($rows[0] as &$row) {
//     echo $row['name'] . '<br>';
// }

// @[$rows, $count] = $db->arrayQuery('SELECT * FROM `categories`');
// echo $count . '<hr>';
// foreach ($rows as &$row) {
//     echo $row['name'] . '<br>';
// }


