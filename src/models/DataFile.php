<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\models;

use Yii;
use common\behaviors\PositionBehavior;
use common\behaviors\StatusBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * DataFile model
 *
 * @property integer $id
 * @property integer $image_id
 * @property integer $data_id
 * @property integer $position
 * @property integer $status
 * @property integer $created_by Optional
 * @property integer $updated_by Optional
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author Maxim Chichkanov
 */
class DataFile extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_dataset_files}}';
    }

    /**
     * {@inheritdoc}
     */
    public static function find()
    {
        return new ActiveQuery(get_called_class());
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
            ['file_id', 'required'],
            ['file_id', 'exist', 'targetClass' => File::class, 'targetAttribute' => 'id'],

            ['data_id', 'required'],
            ['data_id', 'exist', 'targetClass' => Data::class, 'targetAttribute' => 'id'],

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
            'file_id' => 'Файл',
            'data_id' => 'Элемент',
            'position' => 'Позиция',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',

            'file' => 'Файл',
            'data' => 'Данные'
        ];
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface|mixed
     */
    public function getImage()
    {
        return $this->hasOne(File::class, [
            'id' => 'file_id'
        ]);
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getData()
    {
        return $this->hasOne(Data::class, [
            'id' => 'data_id'
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
}
