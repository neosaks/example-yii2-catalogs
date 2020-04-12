<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs;

use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveField;
use common\core\uploader\UploadWidget;
use common\modules\catalogs\models\Attribute;
use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Item;
use common\modules\catalogs\models\Data;
use common\modules\catalogs\widgets\catalogs\CatalogsDisplay;
use common\modules\catalogs\widgets\items\ItemsDisplay;
use common\modules\catalogs\widgets\item\ItemDisplay;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class CatalogHelper
{
    /**
     * Description.
     *
     * @param Attribute $attribute
     * @param DynamicModel $model
     * @return void
     */
    public static function setAttributeRules(Attribute $attribute, DynamicModel $model)
    {
        switch ($attribute->type) {
            case Attribute::TYPE_NONE:
                break;

            case Attribute::TYPE_TEXT:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'string', $options);
                break;

            case Attribute::TYPE_INTEGER:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'integer', $options);
                break;

            case Attribute::TYPE_NUMBER:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'number', $options);
                break;

            case Attribute::TYPE_BOOLEAN:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'boolean', $options);
                break;

            case Attribute::TYPE_IN:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'in', $options);
                break;

            case Attribute::TYPE_DATE:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'date', $options);
                break;

            case Attribute::TYPE_EMAIL:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'email', $options);
                break;

            case Attribute::TYPE_URL:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'url', $options);
                break;

            case Attribute::TYPE_PHONE:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'integer', $options); /** @todo */
                break;

            case Attribute::TYPE_FILE:
                $attribute->trim = false;

                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'file', $options);
                break;

            case Attribute::TYPE_IMAGE:
                $attribute->trim = false;

                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'image', $options);
                break;

            default:
                $options = $attribute->getOptions('validation', []);
                $model->addRule($attribute->name, 'safe', $options);
                break;
        }

        if ($attribute->required) {
            $options = $attribute->getOptions('validation.required', []);
            $model->addRule($attribute->name, 'required', $options);
        }

        if ($attribute->trim) {
            $options = $attribute->getOptions('validation.trim', []);
            $model->addRule($attribute->name, 'trim', $options);
        }

        if ($attribute->default_value !== null) {
            $model->addRule($attribute->name, 'default', ['value' => $attribute->default_value]);
        }
    }

    /**
     * Description.
     *
     * @param Attribute $attribute
     * @param ActiveField $field
     * @return void
     */
    public static function configureField(Attribute $attribute, ActiveField $field)
    {
        $additionalOptions = [];

        if ($attribute->default_value !== null) {
            $additionalOptions['value'] = $attribute->default_value;
        }

        if ($attribute->label !== null) {
            $field->label($attribute->label);
        }

        switch ($attribute->type) {
            case Attribute::TYPE_NONE:
                break;

            case Attribute::TYPE_TEXT:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                ArrayHelper::remove($options, 'textarea', false)
                    ? $field->textarea($options) : $field->textInput($options);
                break;

            case Attribute::TYPE_INTEGER:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $field->textInput($options);
                break;

            case Attribute::TYPE_NUMBER:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $field->textInput($options);
                break;

            case Attribute::TYPE_BOOLEAN:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $field->textInput($options);
                break;

            case Attribute::TYPE_IN:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $range = $attribute->getOptions('range', []);
                $field->dropDownList($range, $options);
                break;

            case Attribute::TYPE_DATE:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $field->textInput($options);
                break;

            case Attribute::TYPE_EMAIL:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $field->textInput($options);
                break;

            case Attribute::TYPE_URL:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $field->textInput($options);
                break;

            case Attribute::TYPE_PHONE:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $field->textInput($options);
                break;

            case Attribute::TYPE_FILE:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $upload = $attribute->getOptions('upload.class', UploadWidget::class);
                $config = $attribute->getOptions('upload.config', []);
                $field->widget($upload, $config + $options);
                break;

            case Attribute::TYPE_IMAGE:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $upload = $attribute->getOptions('upload.class', UploadWidget::class);
                $config = $attribute->getOptions('upload.config', []);
                $field->widget($upload, $config + $options);
                break;

            default:
                $options = $attribute->getOptions('field', [], $additionalOptions);
                $field->textInput($options);
                break;
        }

        // set widgets
        if ($attribute->getOptions('field.widgets')) {
            foreach ($attribute->getOptions('field.widgets') as $widget) {
                if (isset($widget['class'], $widget['config'])) {
                    $field->widget($widget['class'], $widget['config']);
                }
            }
        }
    }

    /**
     * Description.
     *
     * @param Attribute $attribute
     * @param Data $data
     * @return void
     */
    public static function setDataFormat(Attribute $attribute, Data $data)
    {
        switch ($attribute->type) {
            case Attribute::TYPE_NONE:
                $data->format = Data::FORMAT_NONE;
                break;

            case Attribute::TYPE_TEXT:
                $data->format = Data::FORMAT_TEXT;
                break;

            case Attribute::TYPE_INTEGER:
                $data->format = Data::FORMAT_INTEGER;
                break;

            case Attribute::TYPE_NUMBER:
                $data->format = Data::FORMAT_NUMBER;
                break;

            case Attribute::TYPE_BOOLEAN:
                $data->format = Data::FORMAT_BOOLEAN;
                break;

            case Attribute::TYPE_IN:
                $data->format = Data::FORMAT_TEXT;
                break;

            case Attribute::TYPE_DATE:
                $data->format = Data::FORMAT_DATE;
                break;

            case Attribute::TYPE_EMAIL:
                $data->format = Data::FORMAT_EMAIL;
                break;

            case Attribute::TYPE_URL:
                $data->format = Data::FORMAT_URL;
                break;

            case Attribute::TYPE_PHONE:
                $data->format = Data::FORMAT_PHONE;
                break;

            case Attribute::TYPE_FILE:
                $data->format = Data::FORMAT_FILE;
                break;

            case Attribute::TYPE_IMAGE:
                $data->format = Data::FORMAT_IMAGE;
                break;

            case Attribute::TYPE_CURRENCY:
                $data->format = Data::FORMAT_CURRENCY;
                break;

            default:
                $data->format = Data::FORMAT_NONE;
                break;
        }
    }

    /**
     * Description.
     *
     * @param Attribute $attribute
     * @return boolean
     */
    public static function isFileAttribute(Attribute $attribute)
    {
        return $attribute->type === Attribute::TYPE_FILE;
    }

    /**
     * Description.
     *
     * @param Attribute $attribute
     * @return boolean
     */
    public static function isImageAttribute(Attribute $attribute)
    {
        return $attribute->type === Attribute::TYPE_IMAGE;
    }

    /**
     * Description.
     *
     * @return string
     */
    public static function renderCatalogs()
    {
        return CatalogsDisplay::widget();
    }

    /**
     * Description.
     *
     * @return string
     */
    public static function renderItems()
    {
        return ItemsDisplay::widget();
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @return string
     */
    public static function renderCatalog(Catalog $catalog)
    {
        $defaultConfig = [];

        $widget = $catalog->getOptions('render.class', ItemsDisplay::class);
        $config = $catalog->getOptions('render.config', $defaultConfig);

        if (!class_exists($widget)) {
            $widget = ItemsDisplay::class;
            $config = $defaultConfig;
        }

        $config['condition'] = ['catalog_id' => $catalog->id];

        /** @var \yii\base\Widget $widget */

        return $widget::widget($config);
    }

    /**
     * Description.
     *
     * @param Item $item
     * @return string
     */
    public static function renderItem(Item $item)
    {
        $defaultConfig = [];

        $widget = $item->getOptions('render.class', ItemDisplay::class);
        $config = $item->getOptions('render.config', $defaultConfig);

        if (!class_exists($widget)) {
            $widget = ItemDisplay::class;
            $config = $defaultConfig;
        }

        $config['model'] = $item;

        /** @var \yii\base\Widget $widget */

        return $widget::widget($config);
    }
}
