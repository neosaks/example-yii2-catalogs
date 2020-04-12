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
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * Attribute model
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property string $options Optional
 * @property string $placeholder Optional
 * @property string $hint Optional
 * @property string $label Optional
 * @property string $default_value Optional
 * @property integer $unique
 * @property integer $trim
 * @property integer $required
 * @property integer $catalog_id
 * @property integer $position
 * @property integer $status
 * @property integer $created_by Optional
 * @property integer $updated_by Optional
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property array $options
 *
 * @author Maxim Chichkanov
 */
class Attribute extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const TYPE_NONE = 'none';
    const TYPE_TEXT = 'text';
    const TYPE_INTEGER = 'integer';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_IN = 'in';
    const TYPE_DATE = 'date';
    const TYPE_EMAIL = 'email';
    const TYPE_URL = 'url';
    const TYPE_PHONE = 'phone';
    const TYPE_FILE = 'file';
    const TYPE_IMAGE = 'image';

    const TYPE_CURRENCY = 'currency';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_attributes}}';
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
            ['catalog_id', 'required'],
            ['catalog_id', 'exist', 'targetClass' => Catalog::class, 'targetAttribute' => 'id'],

            ['name', 'required'],
            ['name', 'trim'],
            ['name', 'string', 'max' => 255],

            ['name', 'unique', 'filter' => ['catalog_id' => $this->catalog_id]],
            ['name', 'match', 'pattern' => '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/'],

            ['description', 'trim'],
            ['description', 'string', 'max' => 500],

            ['type', 'default', 'value' => self::TYPE_TEXT],
            ['type', 'in', 'range' => [
                self::TYPE_NONE,
                self::TYPE_TEXT,
                self::TYPE_INTEGER,
                self::TYPE_NUMBER,
                self::TYPE_BOOLEAN,
                self::TYPE_IN,
                self::TYPE_DATE,
                self::TYPE_EMAIL,
                self::TYPE_URL,
                self::TYPE_PHONE,
                self::TYPE_FILE,
                self::TYPE_IMAGE,

                self::TYPE_CURRENCY
            ]],

            ['options', 'string', 'max' => 1000],
            ['placeholder', 'string', 'max' => 255],
            ['hint', 'string', 'max' => 255],
            ['label', 'string', 'max' => 255],

            ['options', 'default', 'value' => null],
            ['placeholder', 'default', 'value' => null],
            ['hint', 'default', 'value' => null],
            ['label', 'default', 'value' => null],

            ['default_value', 'string'],
            ['default_value', 'default', 'value' => null],

            ['unique', 'boolean'],
            ['unique', 'default', 'value' => false],

            ['trim', 'boolean'],
            ['trim', 'default', 'value' => true],

            ['required', 'boolean'],
            ['required', 'default', 'value' => false],

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
            'description' => 'Описание',
            'type' => 'Тип',
            'placeholder' => 'Плейсхолдер',
            'hint' => 'Всплывающая подсказка',
            'label' => 'Метка',
            'default_value' => 'Значение по-умолчанию',
            'unique' => 'Уникальный',
            'trim' => 'Обрезать пробелы',
            'required' => 'Обязательный',
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

    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_NONE => 'Не определен',
            self::TYPE_TEXT => 'Текстовые данные',
            self::TYPE_INTEGER => 'Тип Integer',
            self::TYPE_NUMBER => 'Числовые данные',
            self::TYPE_BOOLEAN => 'Логический тип',
            self::TYPE_IN => 'Диапазон значений',
            self::TYPE_DATE => 'Дата',
            self::TYPE_EMAIL => 'E-Mail',
            self::TYPE_URL => 'URL',
            self::TYPE_PHONE => 'Номер телефона',
            self::TYPE_FILE => 'Файл',
            self::TYPE_IMAGE => 'Изображение',

            self::TYPE_CURRENCY => 'Денежная единица'
        ];
    }
}
