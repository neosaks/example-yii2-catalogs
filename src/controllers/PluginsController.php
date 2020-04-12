<?php
namespace common\modules\catalogs\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Plugin;

/**
 * Plugins controller
 */
class PluginsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $catalog = Yii::$app->request->get('catalog');
        $plugin = Yii::$app->request->get('plugin');
        
        if ($catalog !== null && $plugin !== null) {
            if (($instance = $this->findPlugin($plugin, $catalog))) {
                return parent::actions() + $instance->actions();
            }
        }

        return parent::actions();
    }

    /**
     * Description.
     * @param mixed $id
     * @return mixed
     */
    public function actionIndex($id)
    {
        $model = $this->findModel($id);

        return $this->render('index', [
            'model' => $model
        ]);
    }

    /**
     * Finds the Catalog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Catalog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $condition = ['id' => $id, 'status' => Catalog::STATUS_ACTIVE];

        if (($model = Catalog::find()->where($condition)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Description.
     * @param string $id
     * @param integer $catalog
     * @return Plugin
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPlugin($id, $catalog)
    {
        $catalog = $this->findModel($catalog);
        $plugins = $this->module->getPlugins();

        if (isset($plugins[$id]) && $catalog->isConnected($plugins[$id])) {
            return $plugins[$id];
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
