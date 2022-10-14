<?php
use Core\App;
use Catalog\Models\Filter;


function setChecked($filter, $option = null) {
    if (null === $option) {
        return empty($filter->value) || !in_array($option, $filter->value) ? ' checked' : '';
    } else {
        return !empty($filter->value) && in_array($option, $filter->value) ? ' checked' : '';
    }
}

$filter_field_names = [];
?>
<link rel="stylesheet" type="text/css" href="<?= \Core\App::url() ?>/public/style.css">
<!-- <script type="text/javascript" src="<?= \Core\App::url() ?>/public/info-blocks.js"></script> -->

<!-- <pre><?= print_r($cat ?? null); ?></pre> -->
<!-- <pre><?= print_r($cat_ids ?? null); ?></pre> -->
<!-- <pre><?= print_r($products ?? null); ?></pre> -->

<form class="filters" method="GET" action="<?= App::url() . App::request()->getPath() ?>">
<?php if (!empty($filters)): foreach ($filters as $filter): 
    $options = json_decode($filter->options);
    $default_options = json_decode($filter->default_options);
    $options = array_merge_recursive($options ?? [], $default_options ?? []);

    $filter_field_names[] = $filter->product_field;
    // array_map(fn($val) => strval($val), arr1)
    // echo '<hr>';
    // var_dump($filter->value);
    // echo '<br>';
    // var_dump($options);
?>
    <div>
        <span class="filter-name"><?= $filter->name ?></span>
<?php if ($filter->type === Filter::TYPE_RANGE): ?>
        <label>
            <span>От</span>
            <input type="text" name="<?= $filter->product_field ?>" value="<?= $filter->value[0] ?? $options[0] ?>">
        </label>
        <label>
            <span>До</span>
            <input type="text" name="<?= $filter->product_field ?>" value="<?= $filter->value[1] ?? $options[1] ?>">
        </label>
<?php elseif ($filter->type === Filter::TYPE_RADIO): ?>
        <label>
            <input type="radio" name="<?= $filter->product_field ?>" value=""<?= setChecked($filter, null) ?>>
            <span>Нет</span>
        </label>
<?php foreach ($options as $option): ?>
        <label>
            <input type="radio" name="<?= $filter->product_field ?>" value="<?= $option[0] ?>"<?= setChecked($filter, $option[0]) ?>>
            <span><?= $option[1] ?></span>
        </label>
<?php endforeach; ?> 
<?php elseif ($filter->type === Filter::TYPE_CHECKBOX): ?>
<?php foreach ($options as $option): ?>
        <label>
            <input type="checkbox" name="<?= $filter->product_field ?>" value="<?= $option[0] ?>"<?= setChecked($filter, $option[0]) ?>>
            <span><?= $option[1] ?></span>
        </label>
<?php endforeach; ?> 
<?php endif; ?>
    </div>

<?php endforeach; endif; ?> 
    <button type="submit">Submit</button>
</form>
<script type="text/javascript">

function processForm(form, cb) {
    fieldNames.forEach(fieldName => {
        let field = form[fieldName];
        if (field instanceof RadioNodeList) {
            if (!field[0]) return;
            field.forEach(sub => {
                if ('text' === sub.type && sub.value) {
                    cb(sub);
                }
                else if (['radio', 'checkbox'].includes(sub.type) && sub.checked && sub.value) {
                    cb(sub);
                }
            });
        }
    });
}

function setChecked(sub) {
    // console.log(sub);
    if (['radio', 'checkbox'].includes(sub.type)) {
        sub.checked = true;
    }
}

let fieldNames = JSON.parse('<?= json_encode($filter_field_names) ?>');
let form = document.querySelector('form');

processForm(form, setChecked);

form.onsubmit = function (e) {
    e.preventDefault();

    function pushQueryValue(sub) {
        if (!values[sub.name]) values[sub.name] = [];
        values[sub.name].push(sub.value);
    }

    function buildQuery() {
        let query = [];
        Object.keys(values).forEach(key => {
            let value = values[key].join(';');
            query.push(`${key}=${value}`);
        });
        return(query.length > 0) ? '?' + query.join('&') : '';
    }

    let values = {};

    processForm(this, pushQueryValue);

    console.log(values);

    let query = buildQuery();
    // console.log(form.action + query);
    window.location.href = encodeURI(form.action + query);
}
// form.onsubmit = function (e) {
//     // let data = {};
//     let query = [];
//     fieldNames.forEach(fieldName => {
//         // console.log(fieldName, this[fieldName]);
//         let field = this[fieldName];
//         if (field instanceof RadioNodeList) {
//             let value = [];
//             if (!field[0]) return;
//             field.forEach(sub => {
//                 if ('text' === sub.type && sub.value) {
//                     value.push(sub.value);
//                 }
//                 else if (['radio', 'checkbox'].includes(sub.type) && sub.checked && sub.value) {
//                     value.push(sub.value);
//                 }

//             });
//             if (value.length < 1) return;
//             // data[field[0].name] = value.join(';');
//             value = value.join(';');
//             query.push(`${field[0].name}=${value}`);
//         }
//     });
//     e.preventDefault();
//     // console.log(data);
//     // let query = [];
//     // 
//     if (query.length > 0) {
//         query = '?' + query.join('&');
//     } else {
//         query = '';
//     }
//     // console.log(form.action + query);
//     window.location.href = encodeURI(form.action + query);
// };
</script>

<main class="info-blocks">
<?php
// // var_dump($cat);
// if (!empty($cat)) {
//     $cats = [$cat];
// }
// if (!empty($cats)) {
//     foreach ($cats as &$cat) {
//         echo (new DemoRender)->run($cat, $products2);
//     }
// }

// echo urldecode('sort=popular&page=1&priceU=200000%3B41400000');
// echo 'sort=popular&page=1&priceU=200000;41400000';

$this->view('catalog/parts/cats-hierarchy.php', [
    'cat_title' => 'Иерархия категорий',
    'cat_prompt' => 'Для участия в меню',
    'cat' => $cat,
    'products_title' => 'Товары страницы каталога',
    'products_prompt' => 'с DISTINCT, поиск cat_id IN (' . implode(',', $cat_ids) . ')',
    'products' => $products,
]);

$this->view('catalog/parts/cats-hierarchy.php', [
    'cat_title' => 'Иерархия категорий',
    'cat_prompt' => 'С товарами для наглядности',
    'cat' => $cat,
    'products_title' => 'Товары для иерархии',
    'products_prompt' => 'без DISTINCT для примера слева',
    'products' => $products2,
    'showProducts' => true,
]);

$this->view('catalog/parts/info.php', [
    'title' => 'Фильтры категории',
    'pre' => $filters,
]);

// $this->view('catalog/parts/info.php', [
//     'title' => 'Иерархия категорий',
//     'prompt' => 'Для участия в меню',
//     'pre' => $cat,
// ]);
// $this->view('catalog/parts/info.php', [
//     'title' => 'Товары страницы каталога',
//     'prompt' => 'с DISTINCT, поиск cat_id IN (' . implode(',', $cat_ids) . ')',
//     'pre' => $products,
// ]);

?>
</main>
