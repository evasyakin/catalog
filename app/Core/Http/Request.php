<?php
namespace Core\Http;

use Core\App;

class Request
{
    /** @var string url */
    public $url = '';
    /** @var string метод */
    public $method = '';
    /** @var array $_GET */
    public $query = [];
    /** @var array $_POST */
    public $post = [];
    /** @var array все параметры */
    public $props = [];


    // Установка данных

    /**
     * Установка url.
     * @param string url.
     * @return self
     */
    public function withUrl(string $url)
    {
        $url = urldecode($url);
        if (0 === mb_strpos($url, App::url())) {
            $url = mb_substr($url, strlen(App::url()));
        }
        $this->url = $url;
        return $this;
    }

    /**
     * Установка метода.
     * @param string метод.
     * @return self
     */
    public function withMethod(string $method)
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Установка $_GET.
     * @param array $_GET параметры
     * @return self
     */
    public function withQuery(array $props)
    {
        $this->query = $props;
        $this->props = array_merge($this->query, $this->post);
        return $this;
    }

    /**
     * Установка $_POST.
     * @param array $_POST параметры
     * @return self
     */
    public function withPost(array $props)
    {
        $this->post = $props;
        $this->props = array_merge($this->query, $this->post);
        return $this;
    }


    // Получение данных

    /**
     * Получение url запроса.
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Получение метода запроса.
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Проверка на соответствие методу запроса.
     * @param string проверяемый метод
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }

    /**
     * Получение пути url.
     * @return string
     */
    public function getPath(): string
    {
        return parse_url($this->url, PHP_URL_PATH);
    }

    /**
     * Получение свойств запроса вне зависимости от метода.
     * @param array|null имя свойств
     * @param bool|null использовать ли NULL для ненайденных свойств
     * @return array
     */
    public function getProps(array $names = null, bool $useNull = true): array
    {
        if ($names) {
            if ($useNull) {
                $res = [];
                foreach ($names as $name) {
                    $res[$name] = $this->props[$name] ?? null;
                }
                return $res;
            } else {
                return array_filter($this->props, function ($key) use ($names) {
                    return in_array($key, $names);
                }, ARRAY_FILTER_USE_KEY);
            }
        }
        return $this->props;
    }

    /**
     * Получение свойств запроса списком.
     * @param array имена своств
     * @return array
     */
    public function getPropsList(array $names)
    {
        return array_values($this->getProps($names));
    }

    /**
     * Получение свойства запроса вне зависимости от метода.
     * @return mixed
     */
    public function getProp(string $name)
    {
        return $this->props[$name] ?? null;
    }


    /**
     * Создание запроса из глобального окружения.
     * @return static
     */
    public static function createFromGlobals()
    {
        return (new static)
        ->withUrl($_SERVER['REQUEST_URI'])
        ->withMethod($_SERVER['REQUEST_METHOD'])
        ->withQuery($_GET)
        ->withPost($_POST);
    }
}
