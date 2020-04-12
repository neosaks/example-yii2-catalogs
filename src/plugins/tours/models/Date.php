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
use yii\helpers\ArrayHelper;

/**
 * Date model
 *
 * @property integer $id
 * @property integer $begin_at
 * @property integer $end_at
 * @property integer $capacity Optional
 * @property integer $price Optional
 * @property string $note Optional
 * @property integer $item_id
 * @property integer $position
 * @property integer $status
 * @property integer $created_by Optional
 * @property integer $updated_by Optional
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author Maxim Chichkanov
 */
class Date extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_tours_dates}}';
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

            ['begin_at', 'required'],
            ['end_at', 'required'],

            ['begin_at', 'date', 'timestampAttribute' => 'begin_at'],
            ['end_at', 'date', 'timestampAttribute' => 'end_at'],

            ['capacity', 'integer'],
            ['price', 'integer'],

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
            'begin_at' => 'Дата начала',
            'end_at' => 'Дата завершения',
            'capacity' => 'Вместимость',
            'price' => 'Цена',
            'note' => 'Примечание',
            'item_id' => 'Элемент',
            'position' => 'Позиция',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',

            'item' => 'Элемент'
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
    public function getBookings()
    {
        return $this->hasMany(Booking::class, [
            'date_id' => 'id'
        ]);
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

    /**
     * Description
     *
     * @param integer|null $item
     * @return array
     */
    public static function getList($item = null)
    {
        $list = static::find()
            ->where('begin_at > :now', [':now' => time()])
            ->andWhere(['item_id' => $item, 'status' => static::STATUS_ACTIVE])
            ->orderBy('position')
            ->asArray()
            ->all();

        return ArrayHelper::map($list, 'id', function ($date, $defaultValue) {
            $beginAt = Yii::$app->getFormatter()->asDate($date['begin_at']);
            $endAt = Yii::$app->getFormatter()->asDate($date['end_at']);

            return "$beginAt - $endAt";
        });
    }
}
