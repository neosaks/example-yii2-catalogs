<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours\models;

use Yii;
use common\modules\catalogs\models\Item;
use common\behaviors\PositionBehavior;
use common\behaviors\StatusBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Tour booking model
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $date_id
 * @property integer $customer_id
 * @property integer $price
 * @property string $note Optional
 * @property string $token
 * @property integer $position
 * @property integer $status
 * @property integer $created_by Optional
 * @property integer $updated_by Optional
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author Maxim Chichkanov
 */
class Booking extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_tours_booking}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'blameable' => BlameableBehavior::class,
            'timestamp' => TimestampBehavior::class,
            'position' => PositionBehavior::class,
            'status' => StatusBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['item_id', 'required'],
            ['item_id', 'exist', 'targetClass' => Item::class, 'targetAttribute' => 'id'],

            ['date_id', 'required'],
            ['date_id', 'exist', 'targetClass' => Date::class, 'targetAttribute' => 'id'],

            ['customer_id', 'required'],
            ['customer_id', 'exist', 'targetClass' => Person::class, 'targetAttribute' => 'id'],

            ['price', 'required'],
            ['price', 'integer'],

            ['note', 'trim'],
            ['note', 'string', 'max' => 500],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Элемент',
            'date_id' => 'Дата',
            'customer_id' => 'Клиент',
            'price' => 'Цена',
            'note' => 'Примечание',
            'position' => 'Позиция',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',

            'item' => 'Элемент',
            'date' => 'Дата',
            'customer' => 'Клиент',
            'persons' => 'Туристы'
        ];
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getItem()
    {
        return $this->hasOne(Item::class, [
            'id' => 'item_id'
        ]);
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getDate()
    {
        return $this->hasOne(Date::class, [
            'id' => 'date_id'
        ]);
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getCustomer()
    {
        return $this->hasOne(Person::class, [
            'id' => 'customer_id'
        ]);
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getPersons()
    {
        return $this->hasMany(Person::class, ['id' => 'person_id'])
            ->viaTable('catalogs_tours_booking_persons', ['booking_id' => 'id']);
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getCreatedBy()
    {
        $identityClass = Yii::$app->user->identityClass;

        return $this->hasOne($identityClass, [
            'id' => 'created_by'
        ]);
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getUpdatedBy()
    {
        $identityClass = Yii::$app->user->identityClass;

        return $this->hasOne($identityClass, [
            'id' => 'updated_by'
        ]);
    }
}
