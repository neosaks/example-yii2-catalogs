<?php

/** @var yii\web\View $this */

$this->title = 'Редактирование каталога';

$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-default-update">
    <?= $this->render('_form', ['model' => $model]); ?>
</div>
