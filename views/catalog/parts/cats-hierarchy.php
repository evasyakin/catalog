<?php

include_once __DIR__ . '/DemoRender.php';

// ob_start();
// $html = ob_get_clean();

if (empty($cat)) return;
if (empty($products)) $products = [];
$html = (new DemoRender)->run($cat, $products, $showProducts ?? false);
$html2 = (new DemoRender)->run(null, $products);

$this->view('catalog/parts/info.php', [
    'title' => $cat_title ?? null,
    'prompt' => $cat_prompt ?? null,
    'html' => $html,
]);

$this->view('catalog/parts/info.php', [
    'title' => $products_title ?? null,
    'prompt' => $products_prompt ?? null,
    // 'pre' => $products,
    'html' => $html2,
]);
