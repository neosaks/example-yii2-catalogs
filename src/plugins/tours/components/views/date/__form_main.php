<?php

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var common\modules\catalogs\plugins\tours\models\Date $model */

use common\widgets\datepicker\DatePicker;

?>

<div class="form-row">
    <div class="col-sm-12">
        <?= $form->field($model, 'item_id')->dropDownList($itemList, [
            'prompt' => 'Выберите элемент'
        ]); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'begin_at')
            ->widget(DatePicker::class)
            ->textInput(); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'end_at')
            ->widget(DatePicker::class)
            ->textInput(); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'price')->textInput(); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'capacity')->textInput(); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'note')->textarea(); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'status')->dropdownList($statusList); ?>
    </div>
</div>
