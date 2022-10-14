-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Окт 14 2022 г., 14:19
-- Версия сервера: 10.2.30-MariaDB
-- Версия PHP: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `tests_catalog-1`
--

-- --------------------------------------------------------

--
-- Структура таблицы `brands`
--

CREATE TABLE `brands` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `brands`
--

INSERT INTO `brands` (`id`, `name`) VALUES
(1, '70mai'),
(2, ' iMart Store '),
(3, 'HP'),
(4, 'Acer');

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `url` char(127) NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `subcat_level` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `priority` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `url`, `parent_id`, `subcat_level`, `priority`) VALUES
(1, 'Электроника', 'elektronika', NULL, 2, 0),
(2, 'Автотовары', 'avtotovary', NULL, 2, 0),
(3, 'Автоэлектроника и навигация', 'avtotovary/avtoelektronika', 2, 0, 20),
(4, 'Другие аксессуары и доп. оборудование', 'avtotovary/avtoaksessuary-i-dopolnitelnoe-oborudovanie', 2, 1, 0),
(5, 'Электроника', 'avtotovary/avtoaksessuary-i-dopolnitelnoe-oborudovanie/elektronika', 4, 0, 0),
(6, 'Гарнитуры и наушники', 'elektronika/garnitury-i-naushniki', 1, 0, 0),
(7, 'Ноутбуки и компьютеры', 'elektronika/noutbuki-pereferiya', 1, 1, 10),
(8, 'Ноутбуки и ультрабуки', 'elektronika/noutbuki-pereferiya/noutbuki-ultrabuki', 7, 0, 10);

-- --------------------------------------------------------

--
-- Структура таблицы `categories_old`
--

CREATE TABLE `categories_old` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `url` char(127) NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `subcat_level` tinyint(2) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `categories_old`
--

INSERT INTO `categories_old` (`id`, `name`, `url`, `parent_id`, `subcat_level`) VALUES
(1, 'Электроника', 'elektronika', NULL, 3),
(2, 'Автотовары', 'avtotovary', NULL, 2),
(3, 'Автоэлектроника и навигация', 'avtoelektronika', 2, 0),
(4, 'Другие аксессуары и доп. оборудование', 'avtoaksessuary-i-dopolnitelnoe-oborudovanie', 2, 0),
(5, 'Электроника', 'elektronika', 4, 0),
(6, 'Гарнитуры и наушники', 'garnitury-i-naushniki', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `category_filters`
--

CREATE TABLE `category_filters` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `filter_id` int(10) UNSIGNED NOT NULL,
  `product_field` varchar(30) NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `priority` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `category_filters`
--

INSERT INTO `category_filters` (`id`, `category_id`, `filter_id`, `product_field`, `options`, `priority`) VALUES
(1, 8, 1, 'priceD', '[29994, 119992]', 0),
(2, 8, 2, 'discount', '', 0),
(3, 8, 4, 'brand_id', '[[3, \"HP\"], [4, \"Acer\"]]', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `category_parents`
--

CREATE TABLE `category_parents` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `position` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `virtual` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `category_parents`
--

INSERT INTO `category_parents` (`id`, `category_id`, `parent_id`, `position`, `virtual`) VALUES
(1, 4, 1, 0, 1),
(2, 3, 2, 0, 0),
(3, 4, 2, 0, 0),
(4, 5, 4, 0, 0),
(5, 6, 1, 0, 0),
(6, 7, 1, 0, 0),
(7, 8, 7, 0, 0),
(8, 3, 1, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `category_products`
--

CREATE TABLE `category_products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `category_products`
--

INSERT INTO `category_products` (`id`, `category_id`, `product_id`) VALUES
(1, 3, 1),
(2, 5, 1),
(3, 6, 2),
(4, 8, 3),
(5, 8, 4),
(6, 8, 5);

-- --------------------------------------------------------

--
-- Структура таблицы `filters`
--

CREATE TABLE `filters` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `type` tinyint(2) NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `priority` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `filters`
--

INSERT INTO `filters` (`id`, `name`, `type`, `options`, `priority`) VALUES
(1, 'Цена', 1, NULL, 0),
(2, 'Скидка', 2, '[[10, \"от 10% и выше\"], [30, \"от 30% и выше\"], [50, \"от 50% и выше\"], [70, \"от 70% и выше\"]]', 0),
(3, 'Срок доставки', 2, NULL, 0),
(4, 'Бренд', 3, NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `brand_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `price` int(10) UNSIGNED NOT NULL,
  `priceD` int(10) UNSIGNED DEFAULT NULL,
  `discount` tinyint(3) UNSIGNED DEFAULT 0,
  `description` varchar(250) DEFAULT NULL,
  `priority` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `brand_id`, `name`, `price`, `priceD`, `discount`, `description`, `priority`) VALUES
(1, 1, 'Видеорегистратор 70mai Midrive D08', 3054, NULL, 0, NULL, 0),
(2, 2, 'Беспроводные наушники Air Pro', 2255, NULL, 0, NULL, 0),
(3, 3, 'Ноутбук HP 255 G8 (Ryzen 3 5300U/8Gb/256Gb SSD/15.6\"FHD/UMA/Win10) 3V5H9EA', 69990, 50392, 28, NULL, 10),
(4, 4, 'Ноутбук ACER Extensa 15 EX215-22-R1QQ (Athlon 3050U/4Gb/128Gb SSD/15,6\"FHD/UMA/Win10) NX.EG9ER.019', 49990, 29994, 40, NULL, 0),
(5, 3, 'Ноутбук HP EliteBook x360 830 G6 (i5-8265U/16Gb/512Gb SSD/13.3\"FHD/UMA/Win10) 6XD34EA', 149990, 119992, 20, NULL, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `url` (`url`);

--
-- Индексы таблицы `categories_old`
--
ALTER TABLE `categories_old`
  ADD PRIMARY KEY (`id`),
  ADD KEY `url` (`url`);

--
-- Индексы таблицы `category_filters`
--
ALTER TABLE `category_filters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `filter_id` (`filter_id`);

--
-- Индексы таблицы `category_parents`
--
ALTER TABLE `category_parents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `category_products`
--
ALTER TABLE `category_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `filters`
--
ALTER TABLE `filters`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`brand_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `categories_old`
--
ALTER TABLE `categories_old`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `category_filters`
--
ALTER TABLE `category_filters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `category_parents`
--
ALTER TABLE `category_parents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `category_products`
--
ALTER TABLE `category_products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `filters`
--
ALTER TABLE `filters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
