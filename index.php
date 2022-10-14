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

// автозагрузчик
include_once APP_DIR . 'app/core/Loader.php';
(new Loader)->dir('app/')->run();

// подключаем вспомогательные функции
include_once APP_DIR . 'app/core/functions.php';

// Дебаг режим
define('DEBUG', env('DEBUG', false));

// роутинг
(new Router)
->viewsDir('views/')
->default('404.php')
->get('/', [CategoryController::class => 'list'])
->get('/catalog/', [CategoryController::class => 'list'])
->get('/catalog/(:any)', [CategoryController::class => 'one'])
->requestRouting();
