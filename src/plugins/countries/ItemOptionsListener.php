<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\countries;

use Yii;
use yii\helpers\Html;
use common\widgets\flags\Flag;
use common\events\behaviors\OptionsEvent;
use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Item;
use common\modules\catalogs\plugins\Listener;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class ItemOptionsListener extends Listener
{
    /**
     * @var string
     */
    public $cardBackgroundCssStyleGradient = 'linear-gradient(rgba(255,255,255,0.4), rgba(255,255,255,0.8))';

    /**
     * @var boolean
     */
    private $cardBackgroundAssetBundleRegistered = false;

    /**
     * Description.
     *
     * @return void
     */
    public function register(Catalog $catalog)
    {
        $catalog->on(Catalog::EVENT_BEFORE_GET_OPTIONS, [$this, 'getOptions']);
    }

    /**
     * Description.
     *
     * @param OptionsEvent $event
     * @return void
     */
    public function getOptions(OptionsEvent $event)
    {
        if ($event->sender instanceof Item) {
            if ($event->key === 'widgets.display.beforeCardBody') {
                $event->value = $this->renderCardBackground($event->sender);
            }

            if ($event->key === 'widgets.display.cardOptions') {
                $event->value = ['class' => ['catalogs-plugins-countries']];
            }
        }
    }

    /**
     * Description.
     *
     * @param Item $item
     * @return string
     */
    public function renderCardBackground(Item $item)
    {
        if (!$this->cardBackgroundAssetBundleRegistered) {
            $this->cardBackgroundAssetBundleRegistered = true;
            CardBackgroundAsset::register(Yii::$app->getView());
        }

        if (($data = $item->getDataManager()->getValue('iso')) && Flag::has($data->value)) {
            $options = ['style' => Flag::cssStyle($data->value, [
                'background-image' => "{$this->cardBackgroundCssStyleGradient}, {styleUrl}"
            ])];

            Html::addCssClass($options, ['class' => 'card-background']);

            return Html::tag('div', '', $options);
        }
    }
}
