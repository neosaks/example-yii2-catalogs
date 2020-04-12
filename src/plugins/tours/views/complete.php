<?php

/** @var yii\web\View $this */
/** @var common\modules\catalogs\plugins\tours\models\Booking $model */

use common\components\Thumbnail;
use yii\helpers\Html;

$this->title = 'Вы успешно оформили заявку';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalogs-plugins-tours-booking-complete">
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-4"><?= Html::encode($this->title); ?></h1>
            <p class="lead">На ваш E-Mail адрес отправлено письмо с данными для входа в личный кабинет.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <?php if ($model->item->hasImage()) : ?>
                    <?= Yii::createObject([
                        'class' => Thumbnail::class,
                        'source' => $model->item->image->getPath(),
                        'width' => 1200
                    ])->img(['style' => ['max-height' => '200px']]); ?>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= $model->item->name; ?></h5>
                    <p class="card-text"><?= $model->item->description; ?></p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span>ID брони</span>
                        <span><strong><?= $model->id ?></strong></span>
                    </li>
                    <li class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span>Количество человек</span>
                        <span><strong><?= count($model->persons) + 1; ?></strong></span>
                    </li>
                    <li class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span>Дата начала</span>
                        <span><strong><?= Yii::$app->getFormatter()->asDate($model->date->begin_at); ?></strong></span>
                    </li>
                    <li class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span>Дата окончания</span>
                        <span><strong><?= Yii::$app->getFormatter()->asDate($model->date->end_at); ?></strong></span>
                    </li>
                    <li class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span>Итоговая цена</span>
                        <span><strong><?= Yii::$app->getFormatter()->asCurrency($model->price); ?></strong></span>
                    </li>
                </ul>
                <div class="card-body">
                    <?= Html::a('На страницу тура', ['items/view', 'id' => $model->item->id], ['class' => 'card-link']); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
        <div class="card bg-secondary">
            <div class="card-header text-center text-white">
                <strong>
                    <?= $model->customer->surname . ' ' . $model->customer->name . ' ' . $model->customer->patronymic; ?>
                </strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>ID</span>
                    <span><?= $model->customer->id; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>E-Mail</span>
                    <span><?= $model->customer->email; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Телефон</span>
                    <span><?= $model->customer->phone; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Примечание</span>
                    <span><?= $model->customer->note ? $model->customer->note : '-'; ?></span>
                </li>
            </ul>
        </div>

        <?php foreach ($model->persons as $person) : ?>
            <div class="card mt-3">
                <div class="card-header text-center">
                    <strong>
                        <?= $person->surname . ' ' . $person->name . ' ' . $person->patronymic; ?>
                    </strong>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>ID</span>
                        <span><?= $person->id; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Примечание</span>
                        <span><?= $person->note ? $person->note : '-'; ?></span>
                    </li>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>
