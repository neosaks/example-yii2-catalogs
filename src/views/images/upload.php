<?php

/** @var yii\web\View $this */

use common\core\uploader\UploadWidget;

$this->title = 'Загрузка';

$this->params['breadcrumbs'][] = ['label' => 'Каталоги', 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Изображения', 'url' => ['images/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalogs-images-upload">
    <?= UploadWidget::widget([
        'uploader' => $model
    ]); ?>
</div>
