<?php
namespace Core\Router;

use Core\App;
use Core\Http\Request;
use Core\Router\Controller;
use Core\Router\Exceptions\RouterException;
use Core\Router\Exceptions\RoutingException;

if (!defined('VIEWS_DIR')) {
    define('VIEWS_DIR', 'views/');
}

if (!defined('CONTROLLER_CLASS')) {
    define('CONTROLLER_CLASS', Controller::class);
}

class Router
{
    /** @var array обработчики маршрутов */
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'ALL' => [],
    ];

    /** @var array маппинг алиасов */
    protected $aliases = [
        ':any' => '.*',
        ':int' => '[0-9]{1,}',
        ':id' => '[1-9]+\d*',
    ];

    /** @var mixed обработчик по умолчанию */
    protected $default;

    /** @var string директория отображений для обработчиков в виде пути к файлу */
    protected $viewsDir = VIEWS_DIR;

    /** @var string класс контроллера для конекста обработчиков в виде функций и файлов */
    protected $controllerClass = CONTROLLER_CLASS;

    /**
     * Установка обработчика по умолчанию.
     * @param mixed обработчик
     * @return self
     */
    public function default($handler)
    {
        $this->default = &$handler;
        return $this;
    }

    /**
     * Установка обработчика маршрута.
     * @param string метод
     * @param string путь
     * @param mixed обработчик
     * @return self
     */
    public function route(string $method, string $path, $handler)
    {
        // $path = str_replace('/', '\/', $path);
        $path = $this->preparePath($path);
        $this->routes[strtoupper($method)][$path] = &$handler;
        return $this;
    }

    /**
     * Установка обработчиков маршрута на метод.
     * @param string метод
     * @param array маппинг обработчик по путям
     * @return self
     */
    public function methodRoutes(string $method, array $routes)
    {
        foreach ($routes as $path => $handler) {
            $this->route($method, $path, $handler);
        }
        return $this;
    }

    /**
     * Установка обработчиков маршрутов.
     * @param array маршруты
     * @return self
     */
    public function routes(array $routes)
    {
        foreach ($routes as $method => $sub) {
            $this->methodRoutes($method, $sub);
        }
        return $this;
    }


    /**
     * Установка директории отображения.
     * Для реализации обработчиков в виде пути к файлу
     * @param string директория отображения
     * @return self
     * @throws RouterException
     */
    public function viewsDir(string $viewsDir)
    {
        $viewsDir = App::resolvePath($viewsDir);
        if (!is_readable($viewsDir)) {
            throw new RouterException("Views dir \"{$viewsDir}\" does not exists");
        }
        $this->viewsDir = $viewsDir;
        return $this;
    }

    /**
     * Установка контроллер класса.
     * Для контекста обработчиков в виде пути к файлу и замыканий
     * @param string имя класса
     * @return self
     * @throws RouterException
     */
    public function controllerClass(string $className)
    {
        if (!class_exists($className, true)) {
            throw new RouterException("Controller class \"{$className}\" does not exists");
        }
        $this->controllerClass = $className;
        return $this;
    }


    /**
     * Добавление алиаса.
     * @param string алиас
     * @param string замена
     * @return self
     */
    public function alias(string $alias, string $value)
    {
        $this->aliases[$alias] = $value;
        return $this;
    }

    /**
     * Добавленеи алиасов.
     * @param array
     * @return self
     */
    public function aliases(array $aliases)
    {
        foreach ($aliases as $alias => $value) {
            $this->alias($alias, $value);
        }
        return $this;
    }

    /**
     * Применение алиасов.
     * @param string значение
     * @return string значение с заменой алиасов
     */
    public function applyAliases(string $val): string
    {
        $aliases = array_merge($this->aliases, ['/' => '\/']);
        foreach ($aliases as $alias => $value) {
            $val = str_replace($alias, $value, $val);
        }
        return $val;
    }

     /**
     * Подготовка пути к проверке регуляркой.
     * @param string путь
     * @return string путь
     */
    public function preparePath(string $path): string
    {
        $path = $this->applyAliases($path);
        return "/^$path$/";
    }



    /**
     * Реализация обработчика.
     * @param mixed обработчик
     * @param array|null аргументы для обработчика
     * @return mixed
     * @throws RoutingException
     */
    protected function resolveHandler($handler, array $args = [])
    {
        if (is_string($handler)) {
            $filename = App::resolvePath($this->viewsDir . $handler);
            if (is_file($filename)) {
                $callback = function () use ($filename, $args) {
                    include $filename;
                };
                $this->bindToController($callback);
                return $callback();
            }
            throw new RoutingException("File \"{$filename}\" not found");
        }
        if (is_callable($handler)) {
            $this->bindToController($handler);
            return $handler(...$args);
        }
        if (is_array($handler)) {
            foreach ($handler as $class => $method) {
                if (!is_string($class)) {
                    $this->resolveHandler($method, $args);
                } else {
                    $this->resolveClassMethodHandler($class, $method, $args);
                }
            }
            return;
        }
        throw new RoutingException("Route handler must be a string, a callable or an array");
    }

    /**
     * Привязка обработчиков функций и файлов к классу контроллера.
     * @param \Closure
     */
    protected function bindToController(\Closure &$cb)
    {
        $controller = new $this->controllerClass;
        $controller->setViewsDir($this->viewsDir);
        $cb = $cb->bindTo($controller);
    }

    /**
     * Реализация обработчика в виде метода класса.
     * @param string имя класса
     * @param mixed метод
     * @param array|null аргументы для обработчика
     * @throws RoutingException
     */
    protected function resolveClassMethodHandler(string $class, $method, array $args = [])
    {
        if (!is_string($method)) {
            throw new RoutingException('Handler argument #2 (method) must be a string');
        }
        if (!class_exists($class, true)) {
            throw new RoutingException("Class \"{$class}\" does not exists");
        }
        $obj = new $class;
        if (!method_exists($obj, $method)) {
            throw new RoutingException("Class \"{$class}\" not has method \"{$method}\"");
        }
        call_user_func_array([$obj, $method], $args);
    }

    /**
     * Реализация обработчика по умолчанию.
     * @param RoutingException если была ошибка маршрутизации
     * @throws RoutingException
     */
    protected function resolveDefault(RoutingException $e = null)
    {
        if (null !== $this->default) {
            return $this->resolveHandler($this->default, $e ? [$e] : []);
        }
        throw new RoutingException(
            '404. Page Not Found. Router not has default handler'
            . (null === $e ? '' : (', previous: ' . $e->getMessage()))
        );
    }


    /**
     * Маршрутизация.
     * @param string метод
     * @param string путь
     * @return mixed обработчик
     * @throws RoutingException
     */
    public function routing(string $method, string $path)
    {
        $routes = array_merge($this->routes['ALL'], $this->routes[strtoupper($method)] ?? []);
        try {
            foreach ($routes as $_path => $handler) {
                if (preg_match($_path, $path, $matches)) {
                    array_shift($matches);
                    return $this->resolveHandler($handler, $matches);
                }
            }
        } catch (RoutingException $e) {
            $this->resolveDefault($e);
        }
        $this->resolveDefault();
    }

    /**
     * Роутинг по объекту зароса.
     * @param Request|null объект запроса
     */
    public function requestRouting(Request $request = null)
    {
        if (!$request) $request = App::request();
        return $this->routing($request->method, $request->getPath());
    }


    // Method aliases

    public function get(string $path, $handler)
    {
        return $this->route('get', $path, $handler);
    }

    public function post(string $path, $handler)
    {
        return $this->route('post', $path, $handler);
    }

    public function all(string $path, $handler)
    {
        return $this->route('all', $path, $handler);
    }
}
