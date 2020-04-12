<?php

/** @var yii\web\View $this */

use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Category;
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
            <?= $form->field($model, 'description')->textarea(); ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'catalog_id')
                ->dropdownList(Catalog::getList(), ['disabled' => true]); ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'category_id')
                ->dropdownList(Category::getList(), ['prompt' => 'Без категории']); ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'status')->dropdownList([
                Catalog::STATUS_ACTIVE => 'Активный',
                Catalog::STATUS_DELETED => 'Отключен'
            ]); ?>
        </div>
    </div>
</div>
