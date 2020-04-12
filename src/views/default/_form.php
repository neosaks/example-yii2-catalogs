<?php

/** @var yii\web\View $this */

use common\widgets\bootstrap4\ActiveForm;
use common\widgets\bootstrap4\Tabs;
use common\widgets\bootstrap4\Html;
use common\modules\catalogs\CatalogFormAsset;

CatalogFormAsset::register($this);
?>

<div class="catalogs-default-form">
    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->errorSummary($model); ?>

            <?= Tabs::widget([
                'options' => [
                    'class' => 'mb-3'
                ],
                'items' => [
                    [
                        'label' => 'Каталог',
                        'content' => $this->render('__form_main', [
                            'model' => $model,
                            'form' => $form
                        ])
                    ],
                    [
                        'label' => 'Изображение',
                        'content' => $this->render('__form_image', [
                            'model' => $model,
                            'form' => $form
                        ])
                    ],
                    [
                        'label' => 'Информация',
                        'content' => $this->render('__form_info', [
                            'model' => $model,
                            'form' => $form
                        ])
                    ],
                    [
                        'label' => 'Настройки',
                        'disabled' => true,
                        'content' => $this->render('__form_settings', [
                            'model' => $model,
                            'form' => $form
                        ])
                    ]
                ]
            ]); ?>

            <div class="text-right">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
                <?= Html::a('Закрыть', ['default/index'], ['class' => 'btn btn-secondary']); ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?= $this->render('__attributes_modal', [
        'modal' => 'catalogs-attribute-create',
        'catalog' => $model
    ]); ?>
</div>
