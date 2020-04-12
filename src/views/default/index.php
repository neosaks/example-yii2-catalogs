<?php

/** @var yii\web\View $this */

use common\modules\catalogs\CatalogHelper;
use common\widgets\bootstrap4\ButtonDropdown;
use common\widgets\buttons\ButtonsWidget;

$this->title = 'Каталоги';

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-default-index">
    <?= ButtonsWidget::widget([
        'buttons' => [
            'create' => [
                'label' => 'Создать каталог',
                'visible' => Yii::$app->user->can('catalogs.createCatalog'),
                'options' => [
                    'class' => 'btn-primary'
                ]
            ],
            'images' => ButtonDropdown::widget([
                'label' => 'Изображения',
                'split' => true,
                'tagName' => 'a',
                'buttonOptions' => [
                    'class' => 'btn-secondary',
                    'href' => ['images/index']
                ],
                'dropdown' => [
                    'items' => [
                        [
                            'label' => 'Загрузить',
                            'url' => ['images/upload'],
                            'visible' => Yii::$app->user->can('catalogs.uploadImage')
                        ]
                    ]
                ],
                'options' => [
                    'style' => [
                        'display' => Yii::$app->user->can('articles.uploadImage') ? 'inline-flex' : 'none'
                    ]
                ]
            ])
        ],
        'options' => [
            'class' => 'text-right mb-3'
        ]
    ]); ?>

    <?= CatalogHelper::renderCatalogs(); ?>
</div>
