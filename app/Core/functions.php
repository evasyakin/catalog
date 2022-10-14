<?php
/**
 * Вспомогательные функции.
 * @author Egor Vasyakin <egor@evas-php.com>
 */

use core\Env;

/**
 * Получение значения ENV свойства или значения по умолчанию.
 * @param string имя свойства
 * @param mixed|null значение по умолчанию
 */
function env(string $name, $default = null) {
    return Env::get($name, $default);
}

/**
 * Дебаг данных.
 * @param mixed данные
 * @param string|null тип
 */
function debug($data, string $type = null) {
    if (defined('DEBUG') && true === DEBUG) {
        echo '<div class="debug">' . (is_null($type) ? '' : "<b>{$type} debug:</b>") 
        . json_encode($data, JSON_UNESCAPED_UNICODE) . '</div>';
    }
}
