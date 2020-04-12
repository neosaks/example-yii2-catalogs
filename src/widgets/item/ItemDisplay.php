<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\widgets\item;

use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;
use common\modules\catalogs\models\Attribute;
use common\modules\catalogs\models\Item;
use common\widgets\display\DisplayWidget;
use common\widgets\images\ImagesWidget;

/**
 * Description
 *
 * @author Maxim Chichkanov
 */
class ItemDisplay extends DisplayWidget
{
    /**
     * {@inheritdoc}
     */
    public $template = 'default';

    /**
     * @var object
     */
    public $model;

    /**
     * @param object|null $model
     * @return array
     */
    public function getSections($model = null)
    {
        $model = $model ?? $this->getModel();

        if (!$model instanceof Item) {
            return [];
        }

        $attributes = $model->dataManager->findAttributes([
            'options' => ['is.section' => true]
        ]);

        $sections = [];

        foreach ($attributes as $attribute) {
            $data = $model->dataManager->getValue($attribute);
            $data = is_array($data) ? $data[0] : $data;

            if ($data && is_string($data->value) && mb_strlen($data->value)) {
                $sections[] = [
                    'label' => $attribute->label,
                    'content' => $data->value
                ];
            }
        }

        return $sections;
    }

    /**
     * @param object|null $model
     * @return string
     */
    public function getSidebar($model = null)
    {
        $model = $model ?? $this->getModel();

        if (!$model instanceof Item) {
            return '';
        }

        $attribute = $model->dataManager->findAttribute([
            'type' => Attribute::TYPE_IMAGE
        ]);

        if ($data = $model->dataManager->getValue($attribute)) {
            $defaultPageSize = $model->catalog->getOptions('widgets.display.imagePageSize', 40);
            $defaultShowNames = $model->catalog->getOptions('widgets.display.imageShowNames', false);
            $defaultTemplate = $model->catalog->getOptions('widgets.display.imageTemplate', 'masonry');
            $defaultFirstPageLabel = $model->catalog->getOptions('widgets.display.firstPageLabel', false);
            $defaultLastPageLabel = $model->catalog->getOptions('widgets.display.lastPageLabel', false);

            $pageSize = $model->getOptions('widgets.display.imagePageSize', $defaultPageSize);
            $showNames = $model->getOptions('widgets.display.imageShowNames', $defaultShowNames);
            $template = $model->getOptions('widgets.display.imageTemplate', $defaultTemplate);
            $firstPageLabel = $model->getOptions('widgets.display.firstPageLabel', $defaultFirstPageLabel);
            $lastPageLabel = $model->getOptions('widgets.display.lastPageLabel', $defaultLastPageLabel);

            return ImagesWidget::widget([
                'dataProvider' => new ActiveDataProvider([
                    'query' => $data->getImages(),
                    'pagination' => [
                        'pageSize' => $pageSize
                    ]
                ]),
                'pager' => [
                    'firstPageLabel' => $firstPageLabel,
                    'lastPageLabel' => $lastPageLabel
                ],
                'defaultButtons' => false,
                'showNames' => $showNames,
                'template' => $template
            ]);
        }

        return '';
    }

    /**
     * @return array
     */
    public function getButtons($model = null)
    {
        return ($model ?? $this->getModel())
            ->getOptions('widgets.display.headerButtons');
    }

    /**
     * @return OptionsInterface
     * @throws InvalidConfigException
     */
    public function getModel()
    {
        if (!$this->ensureOptionsInterface($this->model)) {
            throw new InvalidConfigException();
        }

        return $this->model;
    }

    /**
     * @param object|null $model
     * @return string
     */
    public function getTitle($model = null)
    {
        $model = $model ?? $this->getModel();

        if ($model instanceof Item && !$model->getIsNewRecord()) {
            if ($data = $model->getDataManager()->getValue('title')) {
                return is_array($data) ? $data[0]->value : $data->value;
            }

            if ($data = $model->getDataManager()->getValue('name')) {
                return is_array($data) ? $data[0]->value : $data->value;
            }
        }

        return parent::getTitle($model);
    }

    /**
     * @param object|null $model
     * @return string
     */
    public function getDescription($model = null)
    {
        $model = $model ?? $this->getModel();

        if ($model instanceof Item && !$model->getIsNewRecord()) {
            if ($data = $model->getDataManager()->getValue('description')) {
                return is_array($data) ? $data[0]->value : $data->value;
            }
        }

        return parent::getDescription($model);
    }

    /**
     * @param object|null $model
     * @return boolean
     */
    public function hasImage($model = null)
    {
        return parent::hasImage($model ?? $this->getModel());
    }

    /**
     * @param object|null $model
     * @return ImageInterface
     */
    public function getImage($model = null)
    {
        return parent::getImage($model ?? $this->getModel());
    }

    /**
     * @param object|null $model
     * @return string
     */
    public function getFooterText($model = null)
    {
        return parent::getFooterText($model ?? $this->getModel());
    }

    /**
     * @param object|null $model
     * @return array
     */
    public function getFooterUrl($model = null)
    {
        return parent::getFooterUrl($model ?? $this->getModel());
    }


    /**
     * @return void
     */
    public function registerAssetBundle()
    {
        ItemDisplayAsset::register($this->getView());
    }

    /**
     * @throws NotSupportedException
     */
    public function getModels()
    {
        throw new NotSupportedException();
    }

    /**
     * @throws NotSupportedException
     */
    public function getDataProvider()
    {
        throw new NotSupportedException();
    }

    /**
     * @throws NotSupportedException
     */
    public function renderPager()
    {
        throw new NotSupportedException();
    }
}
