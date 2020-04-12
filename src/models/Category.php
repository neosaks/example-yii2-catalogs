<?php
namespace common\modules\catalogs\models;

use Yii;
use common\behaviors\PositionBehavior;
use common\behaviors\StatusBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * Category model
 *
 * @property integer $id
 * @property string $name
 * @property string $alias Optional
 * @property string $description
 * @property integer $catalog_id Optional
 * @property integer $position
 * @property integer $status
 * @property integer $created_by Optional
 * @property integer $updated_by Optional
 * @property integer $created_at
 * @property integer $updated_at
 */
class Category extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_categories}}';
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
            BlameableBehavior::class,
            TimestampBehavior::class,
            PositionBehavior::class,
            StatusBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'trim'],
            ['name', 'string', 'max' => 255],

            ['alias', 'unique'],
            ['alias', 'string', 'max' => 255],
            ['alias', 'match', 'pattern' => '/^[a-z0-9_-]*$/i'],

            ['description', 'string', 'max' => 500],

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
            'alias' => 'Псевдоним',
            'description' => 'Описание',
            'position' => 'Позиция',
            'catalog_id' => 'Каталог',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',

            'items' => 'Элементы',
            'catalog' => 'Каталог'
        ];
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getItems()
    {
        return $this->hasMany(Item::class, ['category_id' => 'id'])
            ->where(['status' => Item::STATUS_ACTIVE])
            ->orderBy('position');
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
