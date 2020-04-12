<?php

/** @var yii\web\View $this */

use common\widgets\bootstrap4\Alert;
?>

<?= Alert::widget([
    'options' => ['class' => 'alert-info'],
    'body' => 'Настройки в этом разделе управляются модулем автоматически, не рекомендуется их менять.',
]); ?>

<div class="form-row">
    <div class="col-sm-3">
        <?= $form->field($model, 'hits')->textInput(); ?>
    </div>
</div>
