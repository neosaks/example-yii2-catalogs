<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\promo;

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
            if ($event->key === 'widgets.vegas.buttonOptions') {
                $vegasUrl = $event->sender->getDataManager()->getValue('vegas_url');

                if (!$vegasUrl || !$vegasUrl->value) {
                    return;
                }

                $event->value = [
                    'class' => 'btn btn-lg btn-vegas',
                    'url' => $vegasUrl->value
                ];
            }
        }
    }
}
