<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours;

use common\modules\catalogs\plugins\tours\forms\Booking;

/**
 * Description
 *
 * @author Maximm Chichkanov
 */
interface CrmAdapterInterface
{
    /**
     * @param Booking $model
     * @return boolean
     */
    public function send(Booking $model);
}
