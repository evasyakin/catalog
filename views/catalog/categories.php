<link rel="stylesheet" type="text/css" href="<?= \Core\App::url() ?>/public/style.css">
<?php
include_once __DIR__ . '/parts/DemoRender.php';

if (empty($cats)) die('Empty categories');
echo (new DemoRender)->showCats($cats);

