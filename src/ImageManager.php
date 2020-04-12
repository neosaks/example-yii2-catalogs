<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\web\UploadedFile;
use yii\widgets\ActiveField;
use yii\helpers\ArrayHelper;
use common\helpers\ImageHelper;
use common\core\DynamicModel;
use common\core\uploader\Uploader;
use common\modules\catalogs\models\Attribute;
use common\modules\catalogs\models\Data;
use common\modules\catalogs\models\Image;

/**
 * Image manager
 *
 * @author Maxim Chichkanov
 */
class ImageManager
{
    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * @var Data
     */
    private $data;

    /**
     * @var DynamicModel
     */
    private $model;

    /**
     * @var string
     */
    private $delimiter = ',';

    /**
     * @var Image[]
     */
    private $images;

    /**
     * @var array
     */
    private $changeList;

    /**
     * @var array
     */
    private $loadData = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(Attribute $attribute, Data $data = null)
    {
        if (!CatalogHelper::isImageAttribute($attribute)) {
            throw new InvalidConfigException();
        }

        $this->attribute = $attribute;
        $this->data = $data ?? new Data();
    }

    /**
     * Description.
     *
     * @return DynamicModel
     */
    public function getModel()
    {
        if ($this->model instanceof DynamicModel) {
            return $this->model;
        }

        $idList = ArrayHelper::getColumn($this->getImages(), 'id');

        $model = new DynamicModel();

        $model->setFormName('ImageManager');

        $model->defineAttribute($this->attribute->name, implode($this->delimiter, $idList));

        $model->addRule($this->attribute->name, 'safe');

        return $this->model = $model;
    }

    /**
     * Description.
     *
     * @param ActiveForm $form
     * @param Attribute $attribute
     * @return ActiveField
     */
    public function configureField(Attribute $attribute, ActiveField $field)
    {
        if ($this->attribute !== $attribute) {
            throw new InvalidArgumentException();
        }

        $field->label(false)->hiddenInput([
            'data-widget' => 'catalog-file-list'
        ]);

        return $field;
    }

    /**
     * Description.
     *
     * @return void
     */
    public function reset()
    {
        $this->model = null;
        $this->images = null;
        $this->changeList = null;
    }

    /**
     * Description.
     *
     * @param array $data
     * @return boolean
     */
    public function load($data)
    {
        $this->reset();

        $this->loadData = $data;
        return $this->getModel()->load($data);
    }

    /**
     * Description.
     *
     * @param DataManager $dataManager
     * @return void
     */
    public function upload(DataManager $dataManager)
    {
        if ($this->data->getIsNewRecord()) {
            $this->data->attribute_id = $this->attribute->id;
            $this->data->item_id = $dataManager->getItem()->id;
            $this->data->value = '';

            if (!$this->data->save()) {
                $dataManager->getModel()->addErrors([
                    $this->attribute->name => $this->data->getErrors($this->attribute->name)
                ]);

                return;
            }
        }

        $transaction = Yii::$app->getDb()->beginTransaction();

        $uploader = $this->getUploader($dataManager);

        $uploader->on(Uploader::EVENT_BEFORE_UPLOAD, function ($event) {
            $resolution = ImageHelper::calcResolution($event->file->tempName);

            $model = new Image([
                'extension' => $event->file->extension,
                'basename' => $event->file->baseName,
                'size' => $event->file->size,
                'filename' => $event->filename,
                'subDirectory' => $event->suffix,
                'resolution' => $resolution
            ]);

            if ($event->isValid = $model->save()) {
                $this->data->link('images', $model);
            }
        });


        $uploader->files = UploadedFile::getInstances($dataManager->getModel(), $this->attribute->name);

        if ($this->validate($dataManager, $uploader)) {
            if ($uploader->files && !$uploader->upload() && $uploader->hasErrors()) {
                $errors = implode("\n", $uploader->getErrors('files'));
                $dataManager->getModel()->addError($this->attribute->name, $errors);
            }

            list('link' => $link, 'unlink' => $unlink) = $this->getChangeList();

            foreach ($link as $image) {
                $this->data->link('images', $image);
            }

            foreach ($unlink as $image) {
                $this->data->unlink('images', $image, true);
            }

            $transaction->commit();
        }
    }

    /**
     * Description.
     *
     * @param DataManager $dataManager
     * @param Uploader $uploader
     * @return boolean
     */
    protected function validate(DataManager $dataManager, Uploader $uploader)
    {
        $model = new DynamicModel();

        $model->defineAttribute($this->attribute->name);

        $changeList = $this->getChangeList();

        /** @todo добавить проверку на минимальное количество файлов */
        /** @todo лимит (максимальное кол-во изобр. в альбоме) установить опционально для каждого каталога */

        // $filesCount = count($this->getImages()) - count($changeList['unlink'])
        //             + count($uploader->files) + count($changeList['link']);

        // if ($uploader->maxFiles && $filesCount > $uploader->maxFiles) {
        //     $message = Yii::t('yii', 'You can upload at most {limit, number} {limit, plural, one{file} other{files}}.');
        //     $this->addError($model, $this->attribute->name, $message, ['limit' => $uploader->maxFiles]);
        // }

        if ($model->hasErrors()) {
            $dataManager->getModel()->addErrors($model->getErrors());
        }

        return !$model->hasErrors();
    }

    /**
     * Description.
     *
     * @return array
     */
    protected function getChangeList()
    {
        if ($this->changeList !== null) {
            return $this->changeList;
        }

        $images = $this->getImages();

        $link = [];
        $unlink = [];

        $idList = ArrayHelper::getValue($this->getModel(), $this->attribute->name);
        $idList = array_combine(($idList = explode($this->delimiter, $idList)), $idList);

        foreach ($images as $image) {
            if (!isset($idList[$image->id])) {
                $unlink[] = $image;
            }
            unset($idList[$image->id]);
        }

        if (count($idList)) {
            $link = Image::find()
                ->where(['id' => $idList])
                ->active()
                ->all();
        }

        return $this->changeList = ['link' => $link, 'unlink' => $unlink];
    }

    /**
     * Description.
     *
     * @return Image[]
     */
    protected function getImages()
    {
        if ($this->data->getIsNewRecord()) {
            return [];
        }

        if ($this->images === null) {
            $this->images = $this->data->images;
        }

        return $this->images;
    }

    /**
     * Description.
     *
     * @param DataManager $dataManager
     * @return Uploader
     */
    protected function getUploader(DataManager $dataManager)
    {
        $uploader = Module::getInstance()->getUploader();

        $validators = $dataManager->getModel()
            ->getActiveValidators($this->attribute->name);

        $uploader->configure($validators);

        return $uploader;
    }

    /**
     * Adds an error about the specified attribute to the model object.
     * This is a helper method that performs message selection and internationalization.
     * @param \yii\base\Model $model the data model being validated
     * @param string $attribute the attribute being validated
     * @param string $message the error message
     * @param array $params values for the placeholders in the error message
     * @see \yii\validators\Validator
     */
    protected function addError($model, $attribute, $message, $params = [])
    {
        $params['attribute'] = $model->getAttributeLabel($attribute);
        if (!isset($params['value'])) {
            $value = $model->$attribute;
            if (is_array($value)) {
                $params['value'] = 'array()';
            } elseif (is_object($value) && !method_exists($value, '__toString')) {
                $params['value'] = '(object)';
            } else {
                $params['value'] = $value;
            }
        }
        $model->addError($attribute, $this->formatMessage($message, $params));
    }

    /**
     * Formats a mesage using the I18N, or simple strtr if `\Yii::$app` is not available.
     * @param string $message
     * @param array $params
     * @since 2.0.12
     * @return string
     * @see \yii\validators\Validator
     */
    protected function formatMessage($message, $params)
    {
        if (Yii::$app !== null) {
            return Yii::$app->getI18n()->format($message, $params, Yii::$app->language);
        }

        $placeholders = [];
        foreach ((array) $params as $name => $value) {
            $placeholders['{' . $name . '}'] = $value;
        }

        return ($placeholders === []) ? $message : strtr($message, $placeholders);
    }
}
