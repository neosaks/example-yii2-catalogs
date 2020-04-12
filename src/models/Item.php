<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\models;

use Yii;
use common\behaviors\PositionBehavior;
use common\behaviors\OptionsBehavior;
use common\behaviors\StatusBehavior;
use common\interfaces\entity\ImageBoxInterface;
use common\modules\catalogs\DataManager;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * Item model
 *
 * @property integer $id
 * @property string $name
 * @property string $alias Optional
 * @property string $description Optional
 * @property string $options Optional
 * @property integer $category_id Optional
 * @property integer $catalog_id
 * @property integer $image_id Optional
 * @property integer $position
 * @property integer $status
 * @property integer $created_by Optional
 * @property integer $updated_by Optional
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property DataManager $dataManager
 * @property Category $category
 * @property Catalog $catalog
 * @property Image $image
 *
 * @author Maxim Chichkanov
 */
class Item extends ActiveRecord implements ImageBoxInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * @var DataManager
     */
    private $_dataManager;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_items}}';
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
            'status' => StatusBehavior::class,
            'options' => [
                'class' => OptionsBehavior::class,
                'keyGetters' => [
                    'widgets.display.title' => 'name',
                    'widgets.display.description' => 'description'
                ]
            ]
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

            ['description', 'trim'],
            ['description', 'string', 'max' => 500],

            ['options', 'string', 'max' => 1000],

            ['catalog_id', 'required'],
            ['catalog_id', 'exist', 'targetClass' => Catalog::class, 'targetAttribute' => 'id'],

            ['category_id', 'exist', 'targetClass' => Category::class, 'targetAttribute' => 'id'],
            ['image_id', 'exist', 'targetClass' => Image::class, 'targetAttribute' => 'id'],

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
            'category_id' => 'Категория',
            'catalog_id' => 'Каталог',
            'image_id' => 'Изображение',
            'position' => 'Позиция',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',

            'category' => 'Категория',
            'catalog' => 'Каталог',
            'image' => 'Изображение'
        ];
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, [
            'id' => 'category_id'
        ]);
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
    public function getImage()
    {
        return $this->hasOne(Image::class, [
            'id' => 'image_id'
        ])->where(['status' => Image::STATUS_ACTIVE]);
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

    /**
     * @return DataManager
     */
    public function getDataManager()
    {
        if (!$this->_dataManager) {
            $this->_dataManager = new DataManager($this->catalog, $this);
        }

        return $this->_dataManager;
    }

    /**
     * @return boolean
     */
    public function hasImage()
    {
        return (boolean) $this->image;
    }

    /**
     * @return ImageInterface
     */
    public function fetchImage()
    {
        return $this->image;
    }
}
