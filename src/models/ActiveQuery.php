<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\models;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * Description.
     *
     * @param integer $status
     * @return self
     */
    public function active($status = 10)
    {
        return $this->andWhere(['status' => $status]);
    }
}
