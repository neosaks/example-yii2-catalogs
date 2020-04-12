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
use yii\db\ActiveRecord;

/**
 * Attribute model
 *
 * @property integer $id
 * @property string $value Optional
 * @property string $format
 * @property string $options Optional
 * @property integer $attribute_id
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
class Data extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const FORMAT_NONE = 'none';
    const FORMAT_TEXT = 'text';
    const FORMAT_INTEGER = 'integer';
    const FORMAT_NUMBER = 'number';
    const FORMAT_BOOLEAN = 'boolean';
    const FORMAT_DATE = 'date';
    const FORMAT_EMAIL = 'email';
    const FORMAT_URL = 'url';
    const FORMAT_PHONE = 'phone';
    const FORMAT_FILE = 'file';
    const FORMAT_IMAGE = 'image';

    const FORMAT_CURRENCY = 'currency';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_dataset}}';
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
            'options' => OptionsBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['value', 'string', 'max' => 50000],
            ['options', 'string', 'max' => 1000],

            ['format', 'default', 'value' => self::FORMAT_TEXT],
            ['format', 'in', 'range' => [
                self::FORMAT_NONE,
                self::FORMAT_TEXT,
                self::FORMAT_INTEGER,
                self::FORMAT_NUMBER,
                self::FORMAT_BOOLEAN,
                self::FORMAT_DATE,
                self::FORMAT_EMAIL,
                self::FORMAT_URL,
                self::FORMAT_PHONE,
                self::FORMAT_FILE,
                self::FORMAT_IMAGE,

                self::FORMAT_CURRENCY
            ]],

            ['attribute_id', 'required'],
            ['attribute_id', 'exist', 'targetClass' => Attribute::class, 'targetAttribute' => 'id'],

            ['item_id', 'required'],
            ['item_id', 'exist', 'targetClass' => Item::class, 'targetAttribute' => 'id'],

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
            'value' => 'Значение',
            'format' => 'Формат',
            'attribute_id' => 'Атрибут',
            'item_id' => 'Элемент',
            'position' => 'Позиция',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',

            'attribute' => 'Атрибут',
            'item' => 'Элемент'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function link($name, $model, $extraColumns = [])
    {
        if ($name === 'files' && !isset($extraColumns['status'])) {
            $extraColumns['status'] = DataFile::STATUS_ACTIVE;
        }

        if ($name === 'images' && !isset($extraColumns['status'])) {
            $extraColumns['status'] = DataImage::STATUS_ACTIVE;
        }

        return parent::link($name, $model, $extraColumns);
    }

    /**
     * Description
     *
     * @param string|null $name
     * @return yii\db\ActiveQueryInterface|mixed
     */
    public function getAttribute($name = null)
    {
        if ($name !== null) {
            return parent::getAttribute($name);
        }

        return $this->hasOne(Attribute::class, [
            'id' => 'attribute_id'
        ]);
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
    public function getDataFile()
    {
        // return $this->hasMany(DataFile::class, ['data_id' => 'id'])
        //     ->where(['status' => DataFile::STATUS_ACTIVE])
        //     ->orderBy('position');
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getDataImage()
    {
        return $this->hasMany(DataImage::class, ['data_id' => 'id'])
            ->where(['status' => DataImage::STATUS_ACTIVE])
            ->orderBy('position');
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getFiles()
    {
        // return $this->hasMany(File::class, ['id' => 'file_id'])
        //     ->via('dataFile');
    }

    /**
     * Description
     *
     * @return yii\db\ActiveQueryInterface
     */
    public function getImages()
    {
        return $this->hasMany(Image::class, ['id' => 'image_id'])
            ->via('dataImage');
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
     * @return array
     */
    public static function getFormatList()
    {
        return [
            self::FORMAT_NONE => 'Не определен',
            self::FORMAT_TEXT => 'Текстовые данные',
            self::FORMAT_INTEGER => 'Числовые данные',
            self::FORMAT_NUMBER => 'Числовые данные',
            self::FORMAT_BOOLEAN => 'Логический тип',
            self::FORMAT_DATE => 'Дата',
            self::FORMAT_EMAIL => 'E-Mail',
            self::FORMAT_URL => 'URL',
            self::FORMAT_PHONE => 'Номер телефона',
            self::FORMAT_FILE => 'Файл',
            self::FORMAT_IMAGE => 'Изображение',

            self::FORMAT_CURRENCY => 'Денежная единица'
        ];
    }
}
