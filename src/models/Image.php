<?php
namespace common\modules\catalogs\models;

use Yii;
use common\interfaces\entity\ImageInterface;
use common\behaviors\PositionBehavior;
use common\behaviors\StatusBehavior;
use common\modules\catalogs\Module;
use common\helpers\Framework;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Image model
 *
 * @property integer $id
 * @property string $name Optional
 * @property string $description Optional
 * @property string $sub_directory Optional
 * @property string $resolution
 * @property string $filename
 * @property string $basename
 * @property string $extension Optional
 * @property integer $size
 * @property integer $position
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class Image extends ActiveRecord implements ImageInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%catalogs_images}}';
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
            ['name', 'trim'],
            ['name', 'string', 'max' => 255],

            ['description', 'trim'],
            ['description', 'string', 'max' => 500],

            ['sub_directory', 'string', 'max' => 255],

            ['resolution', 'required'],
            ['resolution', 'string', 'max' => 20],

            ['filename', 'required'],
            ['filename', 'string', 'max' => 255],

            ['basename', 'required'],
            ['basename', 'string', 'max' => 255],

            ['extension', 'string', 'max' => 20],

            ['size', 'required'],
            ['size', 'integer'],

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
            'sub_directory' => 'Поддиректория',
            'resolution' => 'Разрешение',
            'filename' => 'Имя файла',
            'basename' => 'Оригинальное имя',
            'extension' => 'Расширение файла',
            'size' => 'Размер',
            'status' => 'Статус',
            'created_by' => 'Владелец',
            'updated_by' => 'Изменён',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён'
        ];
    }

    /**
     * @return Module
     */
    public function getModule()
    {
        return Framework::getModule(Module::class);
    }

    /**
     *
     */
    public function getDirectory()
    {
        return $this->getModule()->getUploader()->getDirectory();
    }

    /**
     * Description
     *
     * @return string
     */
    public function getPath()
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->getDirectory(),
            $this->subDirectory,
            $this->filename
        ]);
    }

    /**
     * Description
     *
     * @return string
     */
    public function getUrl()
    {
        $assetManager = Yii::$app->assetManager;
        $published = $assetManager->publish($this->getPath());

        return $published[1];
    }

    /**
     * Description
     *
     * @return boolean
     */
    public function fileExists()
    {
        return file_exists($this->getPath());
    }

    /**
     * Description
     *
     * @return string
     */
    public function getSubDirectory()
    {
        return $this->sub_directory;
    }

    /**
     * Description
     *
     * @param string $subDirectory
     * @return void
     */
    public function setSubDirectory($subDirectory)
    {
        $this->sub_directory = $subDirectory;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->getAttribute('description');
    }

    /**
     * @param boolean $extension
     * @return string
     */
    public function getName($extension = false)
    {
        $name = $this->getAttribute('name');

        if (!$name) {
            $name = $this->getAttribute('basename');
        }

        if ($extension) {
            $name .= '.' . $this->getAttribute('extension');
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getResolution()
    {
        return $this->getAttribute('resolution');
    }
}
