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
use common\modules\catalogs\plugins\tours\CrmAdapterInterface;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class BookingAction extends Action
{
    /**
     * @var ToursPlugin
     */
    public $plugin;

    /**
     * @var string
     */
    public $email;

    /**
     * Description.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        // @todo init email
    }

    /**
     * Description.
     *
     * @param string $item
     * @return string
     */
    public function run($item)
    {
        $model = new Booking($this->findItem($item));

        if ($model->loadAll(Yii::$app->getRequest()->post()) && $model->send($this->email)) {
            foreach ($this->plugin->crmAdapters as $config) {
                $adapter = Yii::createObject($config);
                if ($adapter instanceof CrmAdapterInterface) {
                    $adapter->send($model);
                }
            }

            return $this->controller->redirect([
                'complete',
                'catalog' => $model->getItem()->catalog_id,
                'token' => $model->getModel()->token,
                'booking' => $model->getModel()->id,
                'plugin' => $this->plugin->getId()
            ]);
        }

        $this->plugin->setBooking($model);

        Yii::$app->getView()->registerJs('$("#booking-modal").modal("show");');

        $view = '@common/modules/catalogs/views/items/view';

        return $this->controller->render($view, [
            'model' => $model->getItem()
        ]);
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
