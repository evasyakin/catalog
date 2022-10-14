<?php
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

use Catalog\Models\Category;
use Catalog\Models\Product;

class DemoRender
{
    public $cat;
    // public $cat_ids;
    public $products;

    public function run(Category &$cat, array $products = [])
    {
        $this->cat = &$cat;
        $this->products = &$products;
        // $out = '<div class="demo"><div class="demo-cats"><h4>Иерархия категорий</h4>'
        // . $this->showCat($cat) . '</div><div class="demo-products">'
        // .'<h4>Товары для иерархии <span class="prompt">(намеренное дублирование для удобного рендера иерархии)</span></h4>'
        // . $this->showProducts($products) . '</div>';
        // return $out . '</div>';
        echo '<div class="demo"><div class="demo-cats"><h4>Иерархия категорий</h4>'
        . $this->showCat($cat) . '</div><div class="demo-products">'
        .'<h4>Товары для иерархии <span class="prompt">(намеренное дублирование для удобного рендера иерархии)</span></h4><pre>';
        print_r($products);
        echo '</pre></div></div>';
    }

    public function showCat(Category &$cat, int $level = 0)
    {
        $title = $this->showTitle($cat, $level);
        if ($level < $this->cat->subcat_level) {
            $subs = $cat->subCategories();
        }
        if ($level <= $this->cat->subcat_level) {
            $products = $this->getCatProducts($cat->id);
        }
        if (!empty($subs) || !empty($products)) {
            $out = '<details class="category" open><summary>'. $title .'</summary><div class="subs">';
            if (!empty($subs)) {
                $level++;
                $out .= $this->showCats($subs, $level);
            }
            if (!empty($products)) {
                $out .= $this->showProducts($products, $level);
                // $out .= $this->showProducts($cat->id);
            }
            $out .= '</div></details>';
        } else {
            $out = '<div class="category">'. $title .'</div>';
        }
        return $out;
    }

    protected function showCats(array &$cats = null, int $level = 0)
    {
        if (empty($cats)) return '';
        $out = '<div class="categories">';
        foreach ($cats as $cat) {
            $out .= $this->showCat($cat, $level);
        }
        return $out .'</div>';
    }

    protected function getCatProducts(int $cat_id)
    {
        return array_filter($this->products, function (Product $product) use ($cat_id) {
            return $product->category_id === $cat_id;
        });
    }

    protected function showProducts(array $products = null, int $level = null) {
        if (empty($products)) return '';
        $out = '<div class="products">';
        foreach ($products as $product) {
            $out .= $this->showProduct($product, $level);
        }
        return $out .'</div>';
    }

    protected function showProduct(Product &$product, int $level = null) {
        return '<div class="product">'. $this->showTitle($product, $level) .'</div>';
    }

    protected function showTitle(object &$entity, int $level = null)
    {
        $type = '';
        $cat_id = null;
        if ($entity instanceof Category) {
            $type = (isset($entity->virtual) && $entity->virtual == 1) ? 'virtual' : 'category';
            if (null !== $level) {
                $level = "<span class=\"level\">{$level} level</span>";
            }
        } else if ($entity instanceof Product) {
            $type = 'product';
            if (null === $level) {
                $cat_id = '<span class="id cat_id">cat_id = '. $entity->category_id .'</span>';
            }
            $level = null;
        }
        if (!empty($type)) $type = "<span class=\"type {$type}\">{$type}</span>";

        return '<p class="title">'. $level . $type . $cat_id .'<span class="id">'. $entity->id .'</span>'
         .'<span class="name">'. $entity->name .'</span></p>';
    }
}

?>
<style type="text/css">
* {box-sizing: border-box; margin: 0; outline: none; padding: 0; vertical-align: baseline;}
body {font-family: 'Open Sans'; font-size: 16px; padding: 5px;}
.debug {background: #333; border-radius: 5px; color: #fff; font-size: 13px; margin: 5px 0; padding: 5px 10px;}
.debug b {color: #aaa; font-weight: normal; margin-right: 5px;}


summary {cursor: pointer;}
.title {align-items: center; display: flex;}
summary > .title {display: inline-flex;}

.level, .type {
    background: #ddd; border-radius: 4px; font-size: 11px; margin-right: 5px; padding: 2px 6px;
}
.type.category {background: #fa0; display: none;}
.type.virtual {background: #a0f; color: #fff;}
.type.product {background: #af0;}

.id {color: #555; font-size: 13px; margin-right: 2px;}
.id::before, .id::after {display: inline;}
.id::before {content: '[';}
.id::after {content: ']';}

.demo {display: flex;}
.demo h4 {margin-bottom: 5px;}
.demo h4 > .prompt {font-size: 12px; font-weight: normal;}
details > .subs {margin-left: 40px;}
.demo-products .type {display: none;}
.demo-products .cat_id {min-width: 70px;}

.demo-products > pre {font-size: 12px; max-height: calc(100vh - 200px); overflow: scroll;}

pre {
    background: #eee; border-radius: 5px;
    box-shadow: 0 0 10px 1px rgba(0,0,0,.2);
    font-size: 13px;
    margin: 20px auto; max-width: 500px; padding: 7px;
}

</style>

<!-- <pre><?= print_r($cat ?? null); ?></pre> -->
<pre><?= print_r($cat_ids ?? null); ?></pre>
<pre><?= print_r($products ?? null); ?></pre>

<?php
// var_dump($cat);
if (!empty($cat)) {
    $cats = [$cat];
}
if (!empty($cats)) {
    foreach ($cats as &$cat) {
        echo (new DemoRender)->run($cat, $products2);
    }
}
// echo $cat;
