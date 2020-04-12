<?php

/** @var yii\web\View $this */

use common\widgets\images\SelectImage;
?>

<div class="form-row">
    <div class="col-sm-12">
        <?= $form->field($model, 'image_id')->widget(SelectImage::class, [
            'url' => ['images/select']
        ]); ?>
    </div>
</div>
