<?php

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var common\modules\catalogs\DataManager $manager */

use common\modules\catalogs\CatalogHelper;
use common\modules\catalogs\widgets\images\ImagesWidget;

?>

<div class="form-row">
    <div class="col-md-6">
    <?php foreach ($manager->getAttributes() as $attribute) {
        if (!CatalogHelper::isFileAttribute($attribute)
            && !CatalogHelper::isImageAttribute($attribute)) {
            $field = $form->field($manager->getModel(), $attribute->name);

            echo '<div class="col-sm-12">';
            echo $manager->configureField($attribute, $field);
            echo '</div>';
        }
    } ?>
    </div>
    <div class="col-md-6">
    <?php foreach ($manager->getAttributes() as $attribute) {
        if (CatalogHelper::isFileAttribute($attribute)) {
            echo '<div class="col-sm-12">';
            echo 'File list editing is temporarily not supported.';
            echo '</div>';
        }

        if (CatalogHelper::isImageAttribute($attribute)) {
            echo '<div class="col-sm-12">';
            echo ImagesWidget::widget([
                'dataManager' => $manager,
                'attribute' => $attribute,
                'form' => $form
            ]);
            echo '</div>';
        }
    } ?>
    </div>
</div>
