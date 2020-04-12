<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\widgets\item;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\interfaces\entity\ImageInterface;
use common\bundles\animate\AnimateAsset;
use common\widgets\grid\GridWidget;
use common\components\Thumbnail;
use common\widgets\buttons\ButtonsWidget;

/**
 * Description
 *
 * @author Maxim Chichkanov
 */
class ItemHeader extends \yii\base\Widget
{
    /**
     * @var ImageInterface
     */
    public $background;

    /**
     * @var array
     */
    public $backgroundOptions = [];

    /**
     * @var boolean
     */
    public $overlayEnable = true;

    /**
     * @var array
     */
    public $overlayOptions = [];

    /**
     * @var ImageInterface
     */
    // public $image;

    /**
     * @var array
     */
    // public $imageOptions = [];

    /**
     * @var string
     */
    public $title;

    /**
     * @var array
     */
    public $titleOptions = ['class' => 'display-3'];

    /**
     * @var string
     */
    public $description;

    /**
     * @var array
     */
    public $descriptionOptions = [];

    /**
     * @var array
     */
    public $buttons = [];

    /**
     * @var array
     */
    public $buttonOptions = [];

    /**
     * @var array
     */
    public $coverOptions = [];

    /**
     * @var array
     */
    public $contentOptions = [];

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var boolean
     */
    public $animationEnable = true;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        Html::addCssClass($this->options, ['widget' => 'header-widget']);
        Html::addCssClass($this->coverOptions, ['widget' => 'header-cover bg-gradient-secondary']);
        Html::addCssClass($this->overlayOptions, ['widget' => 'cover-overlay']);
        Html::addCssClass($this->titleOptions, ['widget' => 'title']);
        Html::addCssClass($this->descriptionOptions, ['widget' => 'description']);
        Html::addCssClass($this->contentOptions, ['widget' => 'header-content']);

        // animations
        if ($this->animationEnable) {
            Html::addCssClass($this->titleOptions, ['animation' => 'animated zoomIn']);
            Html::addCssClass($this->descriptionOptions, ['animation' => 'animated zoomIn delay-1s']);
        }

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->registerAssetBundle();

        $tag = ArrayHelper::remove($this->options, 'tag', 'header');

        $cover = $this->renderCover();
        $content = $this->renderContent();

        return Html::tag($tag, $cover . "\n" . $content, $this->options);
    }

    /**
     * @return string
     */
    public function renderCover()
    {
        if ($this->background instanceof ImageInterface) {
            $options = $this->backgroundOptions;

            $height = ArrayHelper::remove($options, 'height', 450);

            $thumbnail = Yii::createObject([
                'class' => Thumbnail::class,
                'source' => $this->background->getPath(),
                'height' => $height * 2
            ]);

            Html::addCssClass($options, ['class' => 'cover-image']);
            Html::addCssStyle($options, ['min-height' => $height . 'px']);
            Html::addCssStyle($options, $thumbnail->cssStyle([
                'background-image' => '{styleUrl}'
            ]));

            $background = Html::tag('div', $this->renderOverlay(), $options);
        } else {
            $background = '';
        }

        return Html::tag('div', $background, $this->coverOptions);
    }

    /**
     * @return string
     */
    public function renderOverlay()
    {
        if (!$this->overlayEnable) {
            return '';
        }

        return Html::tag('div', null, $this->overlayOptions);
    }

    /**
     * @return string
     */
    public function renderContent()
    {
        return GridWidget::widget([
            'columns' => [
                [
                    'content' => $this->renderTitle(),
                    'options' => ['class' => 'header-title'],
                    'columnOptions' => ['class' => 'col-12']
                ],
                [
                    'content' => $this->renderDescription(),
                    'options' => ['class' => 'header-description'],
                    'columnOptions' => ['class' => 'col-12 col-md-6']
                ],
                [
                    'content' => $this->renderFooter(),
                    'options' => ['class' => 'header-footer'],
                    'columnOptions' => ['class' => 'col-12']
                ]
            ],
            'containerOptions' => $this->contentOptions
        ]);
    }

    /**
     * @return string
     */
    public function renderTitle()
    {
        $tag = ArrayHelper::remove($this->titleOptions, 'tag', 'h3');
        return Html::tag($tag, $this->title, $this->titleOptions);
    }

    /**
     * @return string
     */
    public function renderDescription()
    {
        $tag = ArrayHelper::remove($this->descriptionOptions, 'tag', 'div');
        return Html::tag($tag, $this->description, $this->descriptionOptions);
    }

    /**
     * @return string
     */
    public function renderFooter()
    {
        $tag = ArrayHelper::remove($this->buttonOptions, 'tag', 'div');
        return Html::tag($tag, $this->renderButtons(), $this->buttonOptions);
    }

    /**
     * @return string
     */
    public function renderButtons()
    {
        return ButtonsWidget::widget($this->buttons);
    }

    /**
     * @return void
     */
    public function registerAssetBundle()
    {
        $view = $this->getView();

        ItemHeaderAsset::register($view);

        if ($this->animationEnable) {
            AnimateAsset::register($view);
        }
    }
}
