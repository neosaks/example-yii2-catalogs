<?php

/** @var yii\web\View $this */

use common\widgets\images\ImagesWidget;

$this->title = 'Выбор изображения';
?>

<div class="catalogs-images-select mt-3">
    <div class="container-fluid">
        <?= ImagesWidget::widget([
            'template' => 'select',
            'dataProvider' => $dataProvider
        ]); ?>
    </div>
</div>
