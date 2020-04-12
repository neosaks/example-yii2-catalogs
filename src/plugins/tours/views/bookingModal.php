<?php

/** @var yii\web\View $this */
/** @var common\modules\catalogs\models\Catalog $catalog */
/** @var common\modules\catalogs\models\Item $item */
/** @var string $plugin */

use common\helpers\SiteHelper;
use common\modules\catalogs\ActiveField;
use common\modules\catalogs\plugins\tours\forms\Customer;
use common\modules\catalogs\plugins\tours\models\Date;
use common\modules\catalogs\plugins\tours\BookingAsset;
use common\modules\catalogs\plugins\tours\models\Person;
use common\widgets\bootstrap4\ActiveForm;
use common\widgets\bootstrap4\Modal;
use common\widgets\datepicker\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

BookingAsset::register($this);
$this->registerJs('new Catalogs_Plugins_Tours_CatalogToursBooking();');

?>

<div class="catalogs-plugins-tours-booking-modal">
    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'id' => 'booking-form',
                'action' => [
                    'plugins/booking',
                    'catalog' => $catalog->id,
                    'item' => $item->id,
                    'plugin' => $plugin
                ],
                'fieldClass' => ActiveField::class,
                'options' => [
                    'data-calc-action' => Url::to([
                        'plugins/calc',
                        'catalog' => $catalog->id,
                        'item' => $item->id,
                        'plugin' => $plugin
                    ])
                ]
            ]); ?>

            <?php Modal::begin([
                'id' => 'booking-modal',
                'title' => 'Забронировать тур',
                'size' => Modal::SIZE_LARGE,
                'footerOptions' => [
                    'class' => 'justify-content-between'
                ],
                'footer' => $this->render('_bookingModalFooter', [
                    'price' => $price
                ])
            ]); ?>

            <?= $form->errorSummary($booking); ?>

            <div class="form-row booking-cards">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            Информация о брони
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $item->name; ?></h5>
                            <p class="card-text">
                                С поддержкой текста ниже, как естественный ввод к дополнительному контенту.
                            </p>
                            <div class="form-row">
                                <div class="col-12">
                                    <?= $form->field($booking, 'date_id')->dropDownList(Date::getList($item->id), [
                                        'prompt' => 'Выберите дату'
                                    ])->label(false); ?>
                                </div>
                                <div class="col-12">
                                    <?= $form->field($booking, 'note')->textarea(); ?>
                                </div>
                                <div class="col-12">
                                    <?= $form->field($booking, 'agreement')
                                        ->checkbox()
                                        ->label(SiteHelper::getAgreementLabel());
                                    ?>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="button" data-action="add-tourist">
                                Добавить туриста
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card mt-3 mt-lg-0">
                        <div class="card-header">
                            Информация о клиенте
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-6">
                                    <?= $form->field($customer, 'name')->textInput(); ?>
                                </div>
                                <div class="col-6">
                                    <?= $form->field($customer, 'surname')->textInput(); ?>
                                </div>
                                <div class="col-12">
                                    <?= $form->field($customer, 'patronymic')->textInput(); ?>
                                </div>
                                <div class="col-6">
                                    <?= $form->field($customer, 'email')->textInput(); ?>
                                </div>
                                <div class="col-6">
                                    <?= $form->field($customer, 'phone')->widget(MaskedInput::class, [
                                        'mask' => '+7 (999) 999-99-99'
                                    ])->textInput(); ?>
                                </div>
                                <div class="col-6">
                                    <?= $form->field($customer, 'birthday')
                                        ->widget(DatePicker::class)
                                        ->textInput(); ?>
                                </div>
                                <div class="col-6">
                                    <?= $form->field($customer, 'sex')->dropDownList([
                                        Customer::SEX_MALE => 'Мужской',
                                        Customer::SEX_FEMALE => 'Женский'
                                    ])->label(false); ?>
                                </div>
                                <div class="col-12">
                                    <?= $form->field($customer, 'note')->textarea(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php foreach ($persons as $index => $person) : ?>
                <div class="col-lg-6 card-tourist" id="card-tourist-<?= $index ?>" data-index="<?= $index ?>">
                    <div class="card mt-3">
                        <div class="card-header">Информация о туристе</div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-6">
                                    <?= $form->field($person, 'name', [
                                        'inputOptions' => [
                                            'id' => "person-$index-name",
                                            'name' => "Person[$index][name]"
                                        ]
                                    ])->textInput(); ?>
                                </div>
                                <div class="col-6">
                                <?= $form->field($person, 'surname', [
                                        'inputOptions' => [
                                            'id' => "person-$index-surname",
                                            'name' => "Person[$index][surname]"
                                        ]
                                    ])->textInput(); ?>
                                </div>
                                <div class="col-12">
                                    <?= $form->field($person, 'patronymic', [
                                        'inputOptions' => [
                                            'id' => "person-$index-patronymic",
                                            'name' => "Person[$index][patronymic]"
                                        ]
                                    ])->textInput(); ?>
                                </div>
                                <div class="col-6">
                                    <?= $form->field($person, 'birthday', [
                                        'inputOptions' => [
                                            'id' => "person-$index-birthday",
                                            'name' => "Person[$index][birthday]"
                                        ]
                                    ])
                                        ->widget(DatePicker::class)
                                        ->textInput(); ?>
                                </div>
                                <div class="col-6">
                                    <?= $form->field($person, 'sex', [
                                        'inputOptions' => [
                                            'id' => "person-$index-sex",
                                            'name' => "Person[$index][sex]"
                                        ]
                                    ])->dropDownList([
                                        Person::SEX_MALE => 'Мужской',
                                        Person::SEX_FEMALE => 'Женский'
                                    ])->label(false); ?>
                                </div>
                                <div class="col-12">
                                    <?= $form->field($person, 'note', [
                                        'inputOptions' => [
                                            'id' => "person-$index-note",
                                            'name' => "Person[$index][note]"
                                        ]
                                    ])->textarea(); ?>
                                </div>
                            </div>
                            <?= Html::button('Удалить', [
                                'class' => 'btn btn-secondary',
                                'type' => 'button',
                                'data-action' => 'delete-tourist',
                                'data-index' => $index
                            ]); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php Modal::end(); ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
