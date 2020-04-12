<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours;

use common\modules\catalogs\plugins\Action;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class TouristAction extends Action
{
    /**
     * Description.
     *
     * @return string
     */
    public function run()
    {
        return $this->render('tourist');
    }
}
