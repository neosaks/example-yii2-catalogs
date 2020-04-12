<?php

/** @var yii\web\View $this */
/** @var common\modules\catalogs\models\Item $model */
/** @var common\modules\catalogs\DataManager $manager */

use common\widgets\bootstrap4\ActiveForm;
use common\widgets\bootstrap4\Tabs;
use common\widgets\bootstrap4\Html;
?>

<div class="catalogs-items-form">
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
                        'label' => 'Запись',
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
                        'label' => 'Данные',
                        'content' => $this->render('__form_data', [
                            'manager' => $manager,
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
                <?php if ($model->getIsNewRecord()) : ?>
                    <?= Html::a(
                        'Закрыть',
                        ['default/view', 'id' => $manager->getCatalog()->id],
                        ['class' => 'btn btn-secondary']
                    ); ?>
                <?php else : ?>
                    <?= Html::a(
                        'Закрыть',
                        ['view','id' => $model->id],
                        ['class' => 'btn btn-secondary']
                    ); ?>
                <?php endif; ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
