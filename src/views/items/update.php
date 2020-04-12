<?php

/** @var yii\web\View $this */
/** @var common\modules\catalogs\models\Item $model */
/** @var common\modules\catalogs\DataManager $manager */

$this->title = 'Редактирование записи';

$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->catalog->name, 'url' => ['default/view', 'id' => $model->catalog->id]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['items/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-items-update">
    <?= $this->render('_form', ['model' => $model, 'manager' => $manager]); ?>
</div>
