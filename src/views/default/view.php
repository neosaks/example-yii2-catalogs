<?php

/** @var yii\web\View $this */

use common\modules\catalogs\CatalogHelper;
use common\widgets\buttons\ButtonsWidget;

if ($model->keywords) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $model->keywords]);
}

$this->registerMetaTag(['name' => 'description', 'content' => $model->description]);

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-default-view">
    <?= ButtonsWidget::widget([
        'buttons' => [
            'items/create' => [
                'label' => 'Добавить запись',
                'options' => [
                    'class' => 'btn-primary'
                ],
                'urlParams' => [
                    'catalog' => $model->id
                ],
                'visible' => Yii::$app->user->can('catalogs.createItem')
            ],
            'update' => [
                'label' => 'Редактировать',
                'options' => [
                    'class' => 'btn-secondary'
                ],
                'urlParams' => [
                    'id' => $model->id
                ],
                'visible' => Yii::$app->user->can('catalogs.updateCatalog', ['post' => $model])
            ],
            'delete' => [
                'label' => 'Удалить',
                'options' => [
                    'class' => 'btn-danger',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')
                ],
                'urlParams' => [
                    'id' => $model->id
                ],
                'visible' => Yii::$app->user->can('catalogs.deleteCatalog', ['post' => $model])
            ]
        ],
        'options' => [
            'class' => 'text-right mb-3'
        ]
    ]); ?>

    <div class="catalog-view">
        <?= CatalogHelper::renderCatalog($model); ?>
    </div>
</div>
