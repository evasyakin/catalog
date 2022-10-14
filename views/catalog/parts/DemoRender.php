<?php

use Catalog\Models\Category;
use Catalog\Models\Product;
use Core\App;

class DemoRender
{
    public $cat;
    // public $cat_ids;
    public $products;

    public $showProducts;

    public function run(Category $cat = null, array $products = [], bool $showProducts = false)
    {
        if (empty($cat) && empty($products)) return 'Empty cat && products';
        if (empty($cat) && !empty($products)) return $this->showProducts($products);
        $this->cat = &$cat;
        $this->products = &$products;
        $this->showProducts = $showProducts;
        // $out = '<div class="demo"><div class="demo-cats"><h4>Иерархия категорий</h4>'
        // . $this->showCat($cat) . '</div><div class="demo-products">'
        // .'<h4>Товары для иерархии <span class="prompt">(намеренное дублирование для удобного рендера иерархии)</span></h4>'
        // . $this->showProducts($products) . '</div>';
        // return $out . '</div>';
        // echo '<div class="demo"><div class="demo-cats"><h4>Иерархия категорий</h4>'
        // . $this->showCat($cat) . '</div><div class="demo-products">'
        // .'<h4>Товары для иерархии <span class="prompt">(без DISTINCT для удобной связи)</span></h4><pre>';
        // print_r($products);
        // echo '</pre></div></div>';
        return '<div class="demoRender">'. $this->showCat($cat) .'</div>';
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
            if (true === $this->showProducts && !empty($products)) {
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
            return isset($product->category_id) && $product->category_id === $cat_id;
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
        return '<div class="product">'. $this->showTitle($product, $level) 
        . '<div class="product-data">'
        . '<span class="discount">-'. $product->discount .'%</span>'
        . '<span class="priceD">'. $product->priceD .'</span>'
        . '<del class="price">'. $product->price .'</del>'
        . '</div></div>';
    }

    protected function showTitle(object &$entity, int $level = null)
    {
        $type = '';
        $cat_id = null;
        $href = '';
        if ($entity instanceof Category) {
            $type = (isset($entity->virtual) && $entity->virtual == 1) ? 'virtual' : 'category';
            if (null !== $level) {
                $level = "{$level}/{$this->cat->subcat_level}";
                $level = "<span class=\"level\">{$level} level</span>";
            }
            $href = App::url() . '/catalog/' . $entity->url;
        } else if ($entity instanceof Product) {
            $type = 'product';
            if (null === $level && isset($entity->category_id)) {
                $cat_id = '<span class="id cat_id">cat_id = '. $entity->category_id .'</span>';
            }
            $level = null;
        }

        if (!empty($type)) $type = "<span class=\"type {$type}\">{$type}</span>";

        return '<p class="title">'
        . $level . $type . $cat_id 
        .'<span class="id">'. $entity->id .'</span>'
        .'<span class="name">'
        . (empty($href) ? '' : '<a href="'. $href . '">')
        . $entity->name 
        . (empty($href) ? '' : '</a>')
        .'</span></p>';
    }
}
