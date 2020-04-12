<?php

/** @var yii\web\View $this */

use common\widgets\bootstrap4\Html;

?>

<strong class="booking-price"><?= $price ?></strong>

<div class="booking-buttons">
    <?= Html::submitButton('Отправить', [
        'class' => 'btn btn-primary',
        'name' => 'submit-button'
    ]); ?>
    <?= Html::button('Закрыть', [
        'class' => 'btn btn-secondary ml-1',
        'data-dismiss' => 'modal'
    ]) ?>
</div>
