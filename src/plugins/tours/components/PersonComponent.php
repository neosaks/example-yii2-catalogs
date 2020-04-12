<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours\components;

use backend\modules\crud\Component;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\helpers\Framework;
use common\modules\catalogs\Module;
use common\modules\catalogs\plugins\tours\models\Person as Model;
use common\modules\catalogs\plugins\tours\models\PersonSearch as Search;
use Yii;

/**
 * Description.
 *
 * @author Maxim Chichkanov <email>
 */
class PersonComponent extends Component
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
        return 'Туристы';
    }

    /**
     * @param ActiveRecord $model
     * @return string
     */
    public function toDisplay(ActiveRecord $model)
    {
        return $model->name . ' ' . $model->surname;
    }

    /**
     * @param ActiveRecord $model
     * @param ActiveForm $form
     * @return array
     */
    public function getFields(ActiveRecord $model, ActiveForm $form)
    {
        $params = ['form' => $form, 'model' => $model, 'statusList' => $this->_getStatus()];

        return [
            'Турист' => [
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
            'name:ntext',
            'surname:ntext',
            'patronymic:ntext',
            'email:email',
            'phone:ntext',
            [
                'attribute' => 'sex',
                'content' => function ($model) {
                    return $model->sex == Model::SEX_MALE ? 'Муж.' : 'Жен.';
                }
            ],
            'birthday:date',
            // 'note:ntext',
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
            'name:ntext',
            'surname:ntext',
            'patronymic:ntext',
            'email:email',
            'phone:ntext',
            [
                'attribute' => 'sex',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->sex == Model::SEX_MALE ? 'Муж.' : 'Жен.';
                }
            ],
            'birthday:date',
            'note:ntext',
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
                ])
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
                ]),
                'options' => [
                    'class' => 'btn btn-success'
                ]
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
}
