<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\widgets\vegas;

use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Item;
use common\modules\catalogs\Module;
use common\widgets\vegas\ExtVegasWidget;
use common\helpers\Framework;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * Description
 *
 * @author Maxim Chichkanov
 */
class VegasWidget extends ExtVegasWidget
{
    /**
     * @var integer
     */
    public $catalog;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        Framework::getModule(Module::class);

        $catalog = $this->findModel($this->catalog);

        foreach ($catalog->items as $item) {
            if (!$item->hasImage()) {
                continue;
            }

            $this->slides[] = [
                'src' => $item->image->getUrl(),
                'content' => $this->content($catalog, $item)
            ];
        }
    }

    /**
     * Description.
     * @param Catalog $catalog
     * @param Item $item
     * @return string
     */
    protected function content($catalog, $item)
    {
        $defaultPosition = $catalog->getOptions('widgets.vegas.position', 'random');
        $defaultAnimation = $catalog->getOptions('widgets.vegas.animation', 'random');
        $defaultShowOverlay = $catalog->getOptions('widgets.vegas.showOverlay', true);
        $defaultShowTitle = $catalog->getOptions('widgets.vegas.showTitle', true);
        $defaultShowDescription = $catalog->getOptions('widgets.vegas.showDescription', true);
        $defaultShowButton = $catalog->getOptions('widgets.vegas.showButton', true);
        $defaultButtonOptions = $catalog->getOptions('widgets.vegas.buttonOptions', [
            'class' => 'btn btn-lg btn-vegas'
        ]);

        $position = $item->getOptions('widgets.vegas.position', $defaultPosition);
        $animation = $item->getOptions('widgets.vegas.animation', $defaultAnimation);
        $showOverlay = $item->getOptions('widgets.vegas.showOverlay', $defaultShowOverlay);
        $showTitle = $item->getOptions('widgets.vegas.showTitle', $defaultShowTitle);
        $showDescription = $item->getOptions('widgets.vegas.showDescription', $defaultShowDescription);
        $showButton = $item->getOptions('widgets.vegas.showButton', $defaultShowButton);
        $buttonOptions = $item->getOptions('widgets.vegas.buttonOptions', $defaultButtonOptions);

        $animationClass = $this->getAnimationClass($animation);
        $positionClass = $this->getPositionClass($position);

        $options = [];
        $content = [];

        Html::addCssClass($options, $positionClass);

        if ($showTitle) {
            $titleOptions = ['class' => 'vegas-title display-4'];
            Html::addCssClass($titleOptions, $animationClass);
            if ($showOverlay) {
                Html::addCssClass($titleOptions, 'vegas-content-overlay');
            }
            $content[] = Html::tag('div', $item->name, $titleOptions);
        }

        if ($showDescription) {
            $descriptionOptions = ['class' => 'vegas-description lead vegas-content-overlay'];
            Html::addCssClass($descriptionOptions, 'delay-1s');
            Html::addCssClass($descriptionOptions, $animationClass);
            if ($showOverlay) {
                Html::addCssClass($descriptionOptions, 'vegas-content-overlay');
            }
            $content[] = Html::tag('div', $item->description, $descriptionOptions);
        }

        if ($showButton) {
            $defaultUrl = Framework::composeUrl(Module::class, 'items/view', ['id' => $item->id]);
            $arrow = Html::tag('span', null, ['class' => 'arrow-vegas']);
            $text = ArrayHelper::remove($buttonOptions, 'text', "Подробнее $arrow");
            $url = ArrayHelper::remove($buttonOptions, 'url', $defaultUrl);
            Html::addCssClass($buttonOptions, $animationClass);
            $content[] = Html::a($text, $url, $buttonOptions);
        }

        return Html::tag('div', implode('', $content), $options);
    }

    /**
     * Description.
     * @param string $animation
     * @param string $filter
     * @return string
     */
    protected function getAnimationClass($animation, $filter = 'In')
    {
        if (!$animation) {
            return '';
        }

        if (isset($this->animations[$animation])) {
            return $this->animations[$animation];
        }

        $animations = $this->findAnimations($filter);
        $key = array_rand($animations, 1);

        return $key ? $animations[$key] : '';
    }

    /**
     * Description.
     * @param string $position
     * @return string
     */
    protected function getPositionClass($position)
    {
        if (!$position) {
            return '';
        }

        if (isset($this->positions[$position])) {
            return $this->positions[$position];
        }

        $key = array_rand($this->positions, 1);

        return $key ? $this->positions[$key] : '';
    }

    /**
     * Finds the Catalog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Catalog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $condition = ['id' => $id, 'status' => Catalog::STATUS_ACTIVE];

        if (($model = Catalog::find()->where($condition)->with('items.image')->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
