<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours\components;

use backend\components\crud\catalogs\ItemComponent;
use backend\modules\crud\Component;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\helpers\Framework;
use common\modules\catalogs\Module;
use common\modules\catalogs\models\Plugin;
use common\modules\catalogs\plugins\tours\ToursPlugin;
use common\modules\catalogs\plugins\tours\models\Booking as Model;
use common\modules\catalogs\plugins\tours\models\BookingSearch as Search;
use Yii;

/**
 * Description.
 *
 * @author Maxim Chichkanov <email>
 */
class BookingComponent extends Component
{
    /**
     * @return string
     */
    public function getSearchClass()
    {
        return Search::class;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return Model::class;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Брони';
    }

    /**
     * @param ActiveRecord $model
     * @return string
     */
    public function toDisplay(ActiveRecord $model)
    {
        $customerName = $model->customer->name;
        $cusomerSurname = $model->customer->surname;
        $itemName = $model->item->name;

        return "$customerName $cusomerSurname - $itemName";
    }

    /**
     * @param ActiveRecord $model
     * @param ActiveForm $form
     * @return array
     */
    public function getFields(ActiveRecord $model, ActiveForm $form)
    {
        $params = [
            'form' => $form,
            'model' => $model,
            'itemList' => $this->_getItems(),
            'statusList' => $this->_getStatus()
        ];

        return [
            'Бронь' => [
                $this->render('__form_main', $params)
            ]
        ];
    }

    /**
     * @return void
     */
    public function initGridViewColumns()
    {
        Framework::getModule(Module::class);

        return [
            [
                'attribute' => 'item_id',
                'content' => function ($model) {
                    return $this->formatter->asViewLink(
                        $model->item->name,
                        $model->item->id,
                        ItemComponent::class
                    );
                }
            ],
            [
                'attribute' => 'date_id',
                'content' => function ($model) {
                    return $this->formatter->asViewLink(
                        Yii::$app->getFormatter()->asDate($model->date->begin_at),
                        $model->date->id,
                        DateComponent::class
                    );
                }
            ],
            [
                'attribute' => 'customer_id',
                'content' => function ($model) {
                    $name = $model->customer->name;
                    $surname = $model->customer->surname;

                    return $this->formatter->asViewLink(
                        "$name $surname",
                        $model->customer->id,
                        PersonComponent::class
                    );
                }
            ],
            [
                'attribute' => 'persons',
                'content' => function ($model) {
                    $formatted = [];

                    foreach ($model->persons as $person) {
                        $formatted[] = $this->formatter->asViewLink(
                            "$person->name $person->surname",
                            $person->id,
                            PersonComponent::class
                        );
                    }

                    return implode(', ', $formatted);
                }
            ],
            'price:currency',
            [
                'attribute' => 'created_by',
                'content' => function ($model) {
                    return $this->formatter->asUser($model->createdBy);
                },
            ],
            [
                'attribute' => 'updated_by',
                'content' => function ($model) {
                    return $this->formatter->asUser($model->updatedBy);
                },
            ],
            'created_at:date',
            'updated_at:date',
            'id'
        ];
    }

    /**
     * @return void
     */
    public function initDetailViewAttributes()
    {
        Framework::getModule(Module::class);

        return [
            'id',
            [
                'attribute' => 'item_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->formatter->asViewLink(
                        $model->item->name,
                        $model->item->id,
                        ItemComponent::class
                    );
                }
            ],
            [
                'attribute' => 'date_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->formatter->asViewLink(
                        Yii::$app->getFormatter()->asDate($model->date->begin_at)
                        . ' - ' .
                        Yii::$app->getFormatter()->asDate($model->date->end_at),
                        $model->date->id,
                        DateComponent::class
                    );
                }
            ],
            [
                'attribute' => 'customer_id',
                'format' => 'raw',
                'value' => function ($model) {
                    $name = $model->customer->name;
                    $surname = $model->customer->surname;

                    return $this->formatter->asViewLink(
                        "$name $surname",
                        $model->customer->id,
                        PersonComponent::class
                    );
                }
            ],
            [
                'attribute' => 'persons',
                'format' => 'raw',
                'value' => function ($model) {
                    $formatted = [];

                    foreach ($model->persons as $person) {
                        $formatted[] = $this->formatter->asViewLink(
                            "$person->name $person->surname",
                            $person->id,
                            PersonComponent::class
                        );
                    }

                    return implode(', ', $formatted);
                }
            ],
            'price:currency',
            'note:ntext',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $this->_getStatus($model->status);
                }
            ],
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->formatter->asUser($model->createdBy);
                },
            ],
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->formatter->asUser($model->updatedBy);
                },
            ],
            'created_at:datetime',
            'updated_at:datetime'
        ];
    }

    /**
     * @return array
     */
    public function initGridViewButtons()
    {
        $buttons = [];

        $booking = Framework::getComponentId(BookingComponent::class, Yii::$app->controller->module);
        $date = Framework::getComponentId(DateComponent::class, Yii::$app->controller->module);
        $person = Framework::getComponentId(PersonComponent::class, Yii::$app->controller->module);

        if ($booking) {
            $buttons['catalog-plugin-tours-booking'] = [
                'label' => 'Брони',
                'url' => Url::to([
                    'default/index',
                    'component' => $booking
                ]),
                'options' => [
                    'class' => 'btn btn-success'
                ]
            ];
        }

        if ($date) {
            $buttons['catalog-plugin-tours-date'] = [
                'label' => 'Даты',
                'url' => Url::to([
                    'default/index',
                    'component' => $date
                ])
            ];
        }

        if ($person) {
            $buttons['catalog-plugin-tours-person'] = [
                'label' => 'Туристы',
                'url' => Url::to([
                    'default/index',
                    'component' => $person
                ])
            ];
        }

        return parent::initGridViewButtons() + $buttons;
    }

    /**
     * @param ActiveRecord $model
     * @return array
     */
    public function initDetailViewButtons(ActiveRecord $model)
    {
        return parent::initDetailViewButtons($model);
    }

    /**
     * @param integer $code
     * @return array|string
     */
    private function _getStatus($code = null)
    {
        $statuses = [
            Model::STATUS_ACTIVE => 'Активный',
            Model::STATUS_DELETED => 'Отключен'
        ];

        if ($code === null) {
            return $statuses;
        }

        if (isset($statuses[$code])) {
            return $statuses[$code];
        }
    }

    /**
     * @return array
     */
    private function _getItems()
    {
        $plugins = Plugin::find()
            ->where([
                'class_name' => ToursPlugin::class,
                'status' => Plugin::STATUS_ACTIVE
            ])
            ->orderBy('position')
            ->with('catalog.items')
            ->all();

        $items = array_reduce($plugins, function ($items, $plugin) {
            return array_merge($items, $plugin->catalog->items);
        }, []);

        return ArrayHelper::map($items, 'id', 'name');
    }
}
