<?php

/** @var yii\web\View $this */
/** @var common\modules\catalogs\models\Catalog $catalog */

use common\modules\catalogs\models\Attribute;
use common\widgets\bootstrap4\Html;
use common\widgets\bootstrap4\Modal;
use common\widgets\bootstrap4\Button;
use common\widgets\bootstrap4\ActiveForm;

$attribute = new Attribute();
?>

<div class="attributes-modal">
    <?php $form = ActiveForm::begin(); ?>

    <?php Modal::begin([
        'id' => $modal,
        'title' => 'Добавление атрибута',
        'footer' => Html::submitButton('Сохранить', [
            'class' => 'btn btn-success'
        ]) . Button::widget([
            'label' => 'Закрыть',
            'options' => [
                'class' => 'btn-secondary',
                'type' => 'button',
                'data-dismiss' => 'modal'
            ]
        ])
    ]); ?>

        <?= $form->errorSummary($attribute); ?>

        <?= $form->field($attribute, 'type')
            ->dropdownList($attribute->getTypeList()); ?>

        <?= $form->field($attribute, 'name')
            ->textInput(); ?>

        <?= $form->field($attribute, 'placeholder')
            ->textInput(); ?>

        <?= $form->field($attribute, 'hint')
            ->textInput(); ?>

        <?= $form->field($attribute, 'description')
            ->textarea(['placeholder' => 'Описание']); ?>

        <?= $form->field($attribute, 'status')
            ->dropdownList([
                Attribute::STATUS_ACTIVE => 'Активный',
                Attribute::STATUS_DELETED => 'Отключен'
            ]); ?>

    <?php Modal::end(); ?>

    <?php ActiveForm::end(); ?>
</div>
