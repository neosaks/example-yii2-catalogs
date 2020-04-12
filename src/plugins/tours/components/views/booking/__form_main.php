<?php

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var common\modules\catalogs\plugins\tours\models\Booking $model */

use common\modules\catalogs\plugins\tours\models\Person;
use common\modules\catalogs\plugins\tours\models\Date;
use common\widgets\bootstrap4\Alert;

?>

<?= Alert::widget([
    'options' => ['class' => 'alert-danger'],
    'body' => 'Этот раздел нуждается в доработке. Операция сохранения данных не инициирует'
        . ' отправку сообщений и не осуществляет синхронизацию с подключенной CRM системой.'
        . ' В данный момент отображаются все даты, включая те, которые могут быть никак не'
        . ' связаны с выбранным туром. Несоответствие даты и тура может вызывать ошибки.'
]); ?>

<div class="form-row">
    <div class="col-sm-12">
        <?= $form->field($model, 'item_id')->dropDownList($itemList, [
            'prompt' => 'Выберите элемент'
        ]); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'date_id')->dropDownList(Date::getList(), [
            'Выберите дату'
        ]); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'customer_id')->dropDownList(Person::getList(), [
            'Выберите клиента'
        ]); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'price')->textInput(); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'note')->textarea(); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'status')->dropdownList($statusList); ?>
    </div>
</div>
