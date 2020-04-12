<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\widgets\images;

use common\widgets\images\ImagesWidget as Images;
use common\modules\catalogs\models\Attribute;
use common\modules\catalogs\models\Data;
use common\modules\catalogs\CatalogHelper;
use common\modules\catalogs\ImageManager;
use common\modules\catalogs\DataManager;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * Description
 *
 * @author Maxim Chichkanov
 */
class ImagesWidget extends \yii\base\Widget
{
    /**
     * @var ImageManager
     */
    public $imageManager;

    /**
     * @var DataManager
     */
    public $dataManager;

    /**
     * @var Attribute
     */
    public $attribute;

    /**
     * @var ActiveForm
     */
    public $form;

    /**
     * @var array
     */
    public $options = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        if (!$this->attribute instanceof Attribute) {
            throw new InvalidConfigException();
        }

        if (!$this->dataManager instanceof DataManager) {
            throw new InvalidConfigException();
        }

        if (!$this->form instanceof ActiveForm) {
            throw new InvalidConfigException();
        }

        if (!CatalogHelper::isImageAttribute($this->attribute)) {
            throw new InvalidConfigException();
        }

        if (!$this->imageManager instanceof ImageManager) {
            $this->imageManager = new ImageManager(
                $this->attribute,
                $this->dataManager
                    ->getValue($this->attribute)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->registerAssetBundle();
        $content = $this->renderFields() . "\n" . $this->renderImages();
        return Html::tag('div', $content, $this->options);
    }

    /**
     * @return string
     */
    public function renderFields()
    {
        $hiddenField = $this->form->field($this->imageManager->getModel(), $this->attribute->name);
        $defaultField = $this->form->field($this->dataManager->getModel(), $this->attribute->name);

        $hiddenField = $this->imageManager->configureField($this->attribute, $hiddenField);
        $defaultField = $this->dataManager->configureField($this->attribute, $defaultField);

        return $hiddenField . "\n" . $defaultField;
    }

    /**
     * @return string
     */
    public function renderImages()
    {
        $data = $this->dataManager->getValue($this->attribute);

        if (!$data instanceof Data) {
            return '';
        }

        return Images::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $data->getImages()
            ]),
            'defaultButtons' => false,
            'buttons' => [
                'delete' => function ($url, $model) {
                    return Html::tag('i', '', [
                        'class' => 'fa fa-times',
                        'data-key' => $model->id,
                        'data-widget' => 'catalog-file-delete'
                    ]);
                }
            ]
        ]);
    }

    /**
     * @return void
     */
    public function registerAssetBundle()
    {
        $view = $this->getView();

        ImagesAsset::register($view);

        $id = $this->options['id'];
        $view->registerJs("new Catalogs_Widgets_Images_CatalogImages('#$id');");
    }
}
