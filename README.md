# catalog

## Сделал
1. Определение категории по url (отказался от частичного seo url, но закоментированный код в CateoryController остался)
2. Получение иерархии категорий для меню (там ещё можно добавить улучшение - группировать запросы поиска подкатегорий по уровням: запрос через where in + разбор на строне ph в нужне subcats)
3. Виртуальные категории
4. Поиск товаров категории, учитывая вложенность и базовые фильтры (ссылаются на поля товара)
5. Базовые фильтры, опции в базовых фильтрах категории (подразумевается их обновление при сохранении товара/категории), типы фильтров
6. Приоритезация (эо может быть рейтинг/настройка) категорий, товаров, фильтров

## Не успел доделать

1. Уникальные фильтры категорий (выбрал вариант отделения от базовых)
2. Значения полей товаров для уникальных фильтров категорий

--

## Ещё моменты

- Не делал вывод в json, потому что с ним сложнее проверять результат
- Если у категории есть фильтры, то отображаю форму, так было удобней тестить + так больше понимания о хранении опций фильтров
- В конце есть ещё вспомогательный вывод товаров внутри иерархии каталогов и товары категории без фильтров

## По данным

- Базовые фильтры пока установлены только в категории [8] "Ноутбуки и ультрабуки"

## Адреса

- `/` - Корень категорий
- `/category/\<cateory.url\>` - Категория
