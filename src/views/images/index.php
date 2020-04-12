<?php

/** @var yii\web\View $this */

use common\widgets\buttons\ButtonsWidget;
use common\widgets\images\ImagesWidget;

$this->title = 'Изображения';

$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-images-index">
    <?= ButtonsWidget::widget([
        'buttons' => [
            'upload' => [
                'label' => 'Загрузить',
                'options' => [
                    'class' => 'btn-secondary'
                ],
                'visible' => Yii::$app->user->can('catalogs.uploadImage')
            ]
        ],
        'options' => [
            'class' => 'text-right mb-3'
        ]
    ]); ?>

    <?= ImagesWidget::widget([
        'dataProvider' => $dataProvider,
        'visibleButtons' => [
            'delete' => function ($model) {
                return Yii::$app->user->can('catalogs.deleteImage', ['post' => $model]);
            }
        ],
    ]); ?>
</div>
