<?php

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var common\modules\catalogs\plugins\tours\models\Person $model */

use common\modules\catalogs\plugins\tours\models\Person;
use common\widgets\datepicker\DatePicker;
use yii\widgets\MaskedInput;

?>

<div class="form-row">
    <div class="col-sm-6">
        <?= $form->field($model, 'name')->textInput(); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'surname')->textInput(); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'patronymic')->textInput(); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'email')->textInput(); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'phone')->widget(MaskedInput::class, [
            'mask' => '+7 (999) 999-99-99'
        ])->textInput(); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'birthday')
            ->widget(DatePicker::class)
            ->textInput(); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'sex')->dropDownList([
            Person::SEX_MALE => 'Мужской',
            Person::SEX_FEMALE => 'Женский'
        ], ['prompt' => 'Выберите пол'])->label(false); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'status')->dropdownList($statusList); ?>
    </div>
</div>
