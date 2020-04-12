<?php

/** @var yii\web\View $this */

use common\modules\catalogs\CatalogHelper;

$this->title = 'Записи';

$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-items-index">
    <?= CatalogHelper::renderItems(); ?>
</div>
