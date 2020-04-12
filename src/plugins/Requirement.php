<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins;

use yii\base\DynamicModel;
use common\modules\catalogs\models\Catalog;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
abstract class Requirement extends DynamicModel
{
    /**
     * Description.
     *
     * @param Catalog $catalog
     * @param DynamicModel $model
     * @return boolean
     */
    abstract public function correct(Catalog $catalog, DynamicModel $model);

        /**
     * Description.
     *
     * @param Catalog $catalog
     * @param DynamicModel $model
     * @return boolean
     */
    abstract public function check(Catalog $catalog, DynamicModel $model);
}
