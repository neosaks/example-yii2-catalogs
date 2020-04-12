<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours;

use yii\web\NotFoundHttpException;
use common\modules\catalogs\plugins\Action;
use common\modules\catalogs\plugins\tours\models\Booking;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class CompleteAction extends Action
{
    /**
     * Description.
     *
     * @param string $booking
     * @return string
     */
    public function run($booking, $token)
    {
        return $this->render('complete', [
            'model' => $this->findModel($booking, $token)
        ]);
    }

    /**
     * Finds the Booking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $token
     * @return Booking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $token)
    {
        $condition = ['id' => $id, 'token' => $token, 'status' => Booking::STATUS_ACTIVE];
        $with = ['item', 'date', 'customer', 'persons'];

        if (($model = Booking::find()->where($condition)->with($with)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
