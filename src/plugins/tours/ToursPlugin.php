<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours;

use backend\components\crud\catalogs\CatalogComponent;
use backend\modules\crud\Module;
use common\helpers\Framework;
use common\modules\catalogs\plugins\Plugin;
use common\modules\catalogs\plugins\tours\forms\Booking;
use common\modules\catalogs\plugins\tours\components\BookingComponent;
use yii\helpers\Url;

/**
 * Tours plugin for Catalog.
 *
 * @author Maxim Chichkanov
 */
class ToursPlugin extends Plugin
{
    /**
     * @var array
     */
    public $crmAdapters = [];

    /**
     * @var Booking
     */
    protected $_booking;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $crud = Framework::getModule(Module::class);

        if (!$crud) {
            return;
        }

        $component = Framework::getComponent(CatalogComponent::class, $crud);

        if (!$component) {
            return;
        }

        $name = Framework::getComponentId(BookingComponent::class, $crud);

        if (!$name) {
            return;
        }

        /** @var CatalogComponent $component */
        $component->addDetailViewButtons([
            'catalog-plugin-tours' => [
                'label' => \Yii::t('app', 'Тур-агенство'),
                'url' => Url::to(['default/index', 'component' => $name])
            ]
        ]);
    }

    /**
     * Description.
     *
     * @param Booking $form
     * @return void
     */
    public function setBooking($form)
    {
        $this->_booking = $form;
    }

    /**
     * Description.
     *
     * @return Booking
     */
    public function getBooking()
    {
        return $this->_booking;
    }

    /**
     * Description.
     *
     * @return Requirement[]
     */
    public function requirements()
    {
        return [
            new AttributesRequirement()
        ];
    }

    /**
     * Description.
     *
     * @return Listener[]
     */
    public function listeners()
    {
        return [
            new ItemOptionsListener($this)
        ];
    }

    /**
     * Description.
     *
     * @return Action[]
     */
    public function actions()
    {
        return [
            'booking' => [
                'class' => BookingAction::class,
                'plugin' => $this
            ],
            'calc' => [
                'class' => CalcAction::class
            ],
            'complete' => [
                'class' => CompleteAction::class
            ]
        ];
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getName()
    {
        return 'Tour Catalog';
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'See readme.txt';
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getAuthor()
    {
        return 'Maxim Chichkanov';
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getCopyright()
    {
        return 'Maxim Chichkanov';
    }
}
