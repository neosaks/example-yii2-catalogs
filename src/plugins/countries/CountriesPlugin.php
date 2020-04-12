<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\countries;

use common\modules\catalogs\plugins\Plugin;

/**
 * Countries plugin for Catalog.
 *
 * @author Maxim Chichkanov
 */
class CountriesPlugin extends Plugin
{
    /**
     * Description.
     *
     * @return Requirement[]
     */
    public function requirements()
    {
        return [
            new AttributesRequirement(),
            new ItemsRequirement()
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
     * @return Action[]
     */
    public function actions()
    {
        return [];
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getName()
    {
        return 'Countries Catalog';
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Примечание: Проверка требований и установка плагина может занять '
            . 'длительное время. Пожалуйста, дождитесь выполнения всех операций.';
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
