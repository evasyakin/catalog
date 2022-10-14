<?php
/**
 * Блок с информацией.
 */
?>

<div class="info">
    <header class="info-header">
        <h2 class="info-title"><?= $title ?? 'No title' ?></h2>
<?php if (!empty($prompt)): ?>
        <div class="info-prompt"><?= $prompt ?></div>
<?php endif; ?>
    </header>
<!--     <nav class="info-tabs">
        <div class="info-tab">Json</div>
        <div class="info-tab">Print_r</div>
        <div class="info-tab">Visual</div>
    </nav> -->
    <div class="info-data">
<?php if (!empty($pre)): ?>
        <pre><?php print_r($pre) ?></pre>
<?php elseif (!empty($html)): ?>
        <div class="info-html"><?= $html ?></div>
<?php endif; ?>
    </div>
</div>
