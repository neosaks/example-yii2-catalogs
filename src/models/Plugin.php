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
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * Plugin model
 *
 * @property integer $id
 * @property string $name Optional
 * @property string $class_name
 * @property integer $catalog_id
 * @property integer $position
 * @property integer $status
 * @property integer $created_by Optional
 * @property integer $updated_by Optional
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author Maxim Chichkanov
 */
class Plugin extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_plugins}}';
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
            ['name', 'trim'],
            ['name', 'string', 'max' => 255],

            ['class_name', 'required'],
            ['class_name', 'trim'],
            ['class_name', 'string', 'max' => 255],

            ['catalog_id', 'exist', 'targetClass' => Catalog::class, 'targetAttribute' => 'id'],

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
            'name' => 'Название',
            'class_name' => 'Имя класса',
            'catalog_id' => 'Каталог',
            'position' => 'Позиция',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',

            'catalog' => 'Каталог'
        ];
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getCatalog()
    {
        return $this->hasOne(Catalog::class, [
            'id' => 'catalog_id'
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
     * @return array
     */
    public static function getList()
    {
        $list = self::find()->orderBy('position')->asArray()->all();
        return ArrayHelper::map($list, 'id', 'name');
    }
}
