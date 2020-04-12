<?php

/** @var yii\web\View $this */

$this->title = 'Создать каталог';

$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-default-create">
    <?= $this->render('_form', ['model' => $model]); ?>
</div>
