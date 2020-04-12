<?php

/** @var yii\web\View $this */
/** @var common\modules\catalogs\models\Item $model */

use common\modules\catalogs\CatalogHelper;
use common\widgets\buttons\ButtonsWidget;

$this->registerMetaTag(['name' => 'description', 'content' => $model->description]);

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->catalog->name, 'url' => ['default/view', 'id' => $model->catalog->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-items-view">
    <?= ButtonsWidget::widget([
        'buttons' => [
            'items/create' => [
                'label' => 'Добавить запись',
                'options' => [
                    'class' => 'btn-primary'
                ],
                'urlParams' => [
                    'catalog' => $model->catalog->id
                ],
                'visible' => Yii::$app->user->can('catalogs.createItem')
            ],
            'items/update' => [
                'label' => 'Редактировать',
                'options' => [
                    'class' => 'btn-secondary'
                ],
                'urlParams' => [
                    'id' => $model->id
                ],
                'visible' => Yii::$app->user->can('catalogs.updateItem', ['post' => $model])
            ],
            'items/delete' => [
                'label' => 'Удалить',
                'options' => [
                    'class' => 'btn-danger',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')
                ],
                'urlParams' => [
                    'id' => $model->id
                ],
                'visible' => Yii::$app->user->can('catalogs.deleteItem', ['post' => $model])
            ]
        ],
        'options' => [
            'class' => 'text-right mb-3'
        ]
    ]); ?>

    <div class="item-view">
        <?= CatalogHelper::renderItem($model); ?>
    </div>
</div>
