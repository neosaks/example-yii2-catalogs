<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\countries;

use common\helpers\Framework;
use common\modules\catalogs\plugins\ItemsRequirement as BaseItemsRequirement;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class ItemsRequirement extends BaseItemsRequirement
{
    /**
     * Description.
     *
     * @return array
     */
    public function requirements()
    {
        if (!$module = Framework::getModule('common\modules\geonames\Module')) {
            return [];
        }

        /** @var \common\modules\geonames\Module $module */

        $requirements = [];

        foreach ($module->getCountries() as $country) {
            $requirements[] = [
                'check' => [
                    'item' => [
                        'name' => $country->country
                    ]
                ],
                'correct' => [
                    'item' => [
                        'name' => $country->country
                    ],
                    'data' => [
                        'iso' => $country->iso
                    ]
                ]
            ];
        }
        
        return $requirements;
    }
}
