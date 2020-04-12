<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours;

use Yii;
use yii\helpers\Html;
use common\events\behaviors\OptionsEvent;
use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Item;
use common\modules\catalogs\plugins\Listener;
use common\modules\catalogs\plugins\tours\forms\Booking;
use common\modules\catalogs\plugins\tours\forms\Customer;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class ItemOptionsListener extends Listener
{
    /**
     * @var ToursPlugin
     */
    public $plugin;

    /**
     * @var Catalog
     */
    public $catalog;

    /**
     * Description.
     *
     * @param ToursPlugin $plugin
     */
    public function __construct(ToursPlugin $plugin, $config = [])
    {
        $this->plugin = $plugin;
        parent::__construct($config);
    }

    /**
     * Description.
     *
     * @return void
     */
    public function register(Catalog $catalog)
    {
        $this->catalog = $catalog;

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
            if ($event->key === 'widgets.display.headerButtons') {
                $event->value = $this->renderHeaderButtons($event->sender);
            }

            if ($event->key === 'widgets.display.footerText') {
                $event->value = $this->renderFooterText($event->sender);
            }
        }
    }

    /**
     * Description.
     *
     * @param Item $item
     * @return array
     */
    public function renderHeaderButtons(Item $item)
    {
        $booking = $this->plugin->getBooking() ?? new Booking($item);
        $customer = $booking->getCustomer() ?? new Customer();

        echo $this->renderPartial('bookingModal', [
            'plugin' => $this->plugin->getId(),
            'catalog' => $this->catalog,
            'item' => $item,
            'customer' => $customer,
            'booking' => $booking,
            'price' => $booking->calc(),
            'persons' => $booking->getPersons()
        ]);

        return [
            'buttons' => [
                [
                    'label' => 'Забронировать',
                    'options' => [
                        'class' => 'btn-outline-white',
                        'data-toggle' => 'modal',
                        'data-target' => '#booking-modal'
                    ]
                ]
            ]
        ];
    }

    /**
     * Description.
     *
     * @return string|null
     */
    public function renderFooterText(Item $item)
    {
        $footerText = $item->getDataManager()->getValue('footer_text');
        $minPrice = $item->getDataManager()->getValue('min_price');

        $contents = [];

        if ($minPrice && $minPrice->value) {
            $formatted = Yii::$app->getFormatter()->asCurrency($minPrice->value);
            $contents[] = "от $formatted";
        }

        if ($footerText && $footerText->value) {
            $contents[] = $footerText->value;
        }

        if (!$contents) {
            return;
        }

        $result = '';
        foreach ($contents as $content) {
            $result .= Html::tag('div', $content);
        }

        return Html::tag('div', $result, ['class' => 'd-flex justify-content-between']);
    }
}
