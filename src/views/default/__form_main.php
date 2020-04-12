<?php

/** @var yii\web\View $this */

use common\modules\catalogs\models\Catalog;
?>

<div class="form-row">
    <div class="col-md-6">
        <div class="col-sm-12">
            <?= $form->field($model, 'name')->textInput(); ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'alias')->textInput(); ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'keywords')->textarea(); ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'description')->textarea(); ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'status')->dropdownList([
                Catalog::STATUS_ACTIVE => 'Активный',
                Catalog::STATUS_DELETED => 'Отключен'
            ]); ?>
        </div>
    </div>
    <div class="col-md-6">
        There will be a widget that allows you to edit the list of attributes for the catalog.
    </div>
</div>
