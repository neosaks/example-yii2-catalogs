<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\models;

use Yii;
use yii\base\Event;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\behaviors\PositionBehavior;
use common\behaviors\OptionsBehavior;
use common\behaviors\StatusBehavior;
use common\interfaces\entity\ImageBoxInterface;
use common\modules\catalogs\plugins\Plugin as PluginHost;
use Closure;

/**
 * Catalog model
 *
 * @property integer $id
 * @property string $name
 * @property string $alias Optional
 * @property string $description
 * @property string $keywords Optional
 * @property string $options Optional
 * @property integer $hits
 * @property integer $category_id Optional
 * @property integer $image_id Optional
 * @property integer $position
 * @property integer $status
 * @property integer $created_by Optional
 * @property integer $updated_by Optional
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Plugin[] $plugins
 * @property Item[] $items
 * @property Category $category
 * @property Image $image
 *
 * @author Maxim Chichkanov
 */
class Catalog extends ActiveRecord implements ImageBoxInterface
{
    const EVENT_BEFORE_GET_OPTIONS = OptionsBehavior::EVENT_BEFORE_GET_OPTIONS;
    const EVENT_AFTER_GET_OPTIONS = OptionsBehavior::EVENT_AFTER_GET_OPTIONS;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_list}}';
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
    public function init()
    {
        parent::init();

        Event::on(Item::class, ActiveRecord::EVENT_AFTER_FIND, function ($event) {
            $this->capturing($event->sender, function ($sender) {
                return $sender->catalog_id === $this->id;
            }, static::EVENT_BEFORE_GET_OPTIONS);
        });
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
            ['description', 'string', 'max' => 1000],

            ['keywords', 'trim'],
            ['keywords', 'string', 'max' => 255],

            ['options', 'string', 'max' => 1000],

            ['hits', 'integer'],
            ['hits', 'default', 'value' => 0],

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
            'name' => 'Имя',
            'alias' => 'Псевдоним',
            'description' => 'Описание',
            'keywords' => 'Ключевые слова',
            'hits' => 'Просмотры',
            'category_id' => 'Категория',
            'image_id' => 'Изображение',
            'position' => 'Позиция',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',

            'image' => 'Изображение'
        ];
    }

    /**
     * Description
     *
     * @return void
     */
    public function incrementHit()
    {
        $this->hits++;
    }

    /**
     * Description
     *
     * @param PluginHost $plugin
     * @return boolean
     */
    public function isConnected(PluginHost $plugin)
    {
        foreach ($this->plugins as $connection) {
            /** @var Plugin $connection */
            if ($connection->class_name === get_class($plugin)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Description
     *
     * @param PluginHost $plugin
     * @return Plugin|boolean
     */
    public function getConnection(PluginHost $plugin)
    {
        foreach ($this->plugins as $connection) {
            /** @var Plugin $connection */
            if ($connection->class_name === get_class($plugin)) {
                return $connection;
            }
        }

        return false;
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getPlugins()
    {
        return $this->hasMany(Plugin::class, ['catalog_id' => 'id'])
            ->where(['status' => Plugin::STATUS_ACTIVE])
            ->orderBy('position');
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getItems()
    {
        return $this->hasMany(Item::class, ['catalog_id' => 'id'])
            ->where(['status' => Item::STATUS_ACTIVE])
            ->orderBy('position');
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

    /**
     * Description.
     *
     * @param Component $component
     * @param Closure $predicate
     * @param string $name
     * @return void
     */
    protected function capturing(Component $component, Closure $predicate, $name)
    {
        if ($predicate->call($this, $component)) {
            $component->on($name, function ($event) use ($name) {
                $this->trigger($name, $event);
            });
        }
    }
}
