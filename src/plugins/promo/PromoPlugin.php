<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\promo;

use common\modules\catalogs\plugins\Plugin;

/**
 * Promo plugin for Catalog.
 *
 * @author Maxim Chichkanov
 */
class PromoPlugin extends Plugin
{
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
            new ItemOptionsListener()
        ];
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getName()
    {
        return 'Promo Catalog';
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
