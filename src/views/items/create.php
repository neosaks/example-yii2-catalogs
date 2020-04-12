<?php

/** @var yii\web\View $this */
/** @var common\modules\catalogs\models\Item $model */
/** @var common\modules\catalogs\DataManager $manager */

$this->title = 'Добавление записи';

$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['default/index']];

$this->params['breadcrumbs'][] = $model->catalog
    ? ['label' => $model->catalog->name, 'url' => ['default/view', 'id' => $model->catalog->id]]
    : ['label' => 'Записи', 'url' => ['index']];

    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-items-create">
    <?= $this->render('_form', ['model' => $model, 'manager' => $manager]); ?>
</div>
