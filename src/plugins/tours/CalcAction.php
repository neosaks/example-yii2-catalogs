<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours;

use Yii;
use yii\web\NotFoundHttpException;
use common\modules\catalogs\models\Item;
use common\modules\catalogs\plugins\Action;
use common\modules\catalogs\plugins\tours\forms\Booking;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class CalcAction extends Action
{
    /**
     * Description.
     *
     * @param string $item
     * @return string
     */
    public function run($item)
    {
        $model = new Booking($this->findItem($item));
        $model->loadAll(Yii::$app->getRequest()->post());
        return $model->calc();
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findItem($id)
    {
        $condition = ['id' => $id, 'status' => Item::STATUS_ACTIVE];
        $with = ['catalog'];

        if (($model = Item::find()->where($condition)->with($with)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
